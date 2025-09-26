<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use App\Models\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManagerStatic as ImageManager;

class ChunkedUploadController extends Controller
{
    // POST /upload/chunk -> params: upload_uuid, chunk_index, total_chunks, chunk (file)
    public function receiveChunk(Request $r)
    {
        $uuid = $r->input('upload_uuid') ?? Str::uuid();
        $index = (int)$r->input('chunk_index');
        $total = (int)$r->input('total_chunks');
        $chunk = $r->file('chunk');

        $tempDir = storage_path('app/uploads/'.$uuid);
        if (!is_dir($tempDir)) @mkdir($tempDir, 0755, true);
        $chunkPath = $tempDir.'/chunk_'.$index;
        $chunk->move($tempDir, 'chunk_'.$index);

        // Create or update Upload record
        $upload = Upload::firstOrCreate(['upload_uuid'=>$uuid], [
            'filename' => $r->input('filename') ?? 'upload.bin',
            'total_chunks' => $total,
            'completed' => false,
        ]);

        return response()->json(['upload_uuid'=>$uuid,'received_chunk'=>$index]);
    }

    // POST /upload/complete -> upload_uuid, checksum, entity_type, entity_id
    public function complete(Request $r)
    {
        $uuid = $r->input('upload_uuid');
        $checksum = $r->input('checksum');
        $upload = Upload::where('upload_uuid',$uuid)->firstOrFail();
        $tempDir = storage_path('app/uploads/'.$uuid);

        // Assemble
        $chunks = glob($tempDir.'/chunk_*');
        natsort($chunks);
        $assembled = $tempDir.'/assembled.bin';
        $out = fopen($assembled,'wb');
        foreach ($chunks as $c) {
            fwrite($out, file_get_contents($c));
        }
        fclose($out);

        $computed = hash_file('sha256', $assembled);
        if ($computed !== $checksum) {
            return response()->json(['error'=>'checksum_mismatch'], 422);
        }

        // Move to storage and generate variants
        $path = 'uploads/'.$uuid.'/'.basename($upload->filename);
        Storage::disk('public')->putFileAs('uploads/'.$uuid, new \Illuminate\Http\File($assembled), basename($upload->filename));
        $storedPath = Storage::disk('public')->path($path);

        // Generate image variants using Intervention (aspect ratio preserved)
        $img = ImageManager::make($storedPath);
        $variants = [];
        foreach ([256,512,1024] as $size) {
            $copy = clone $img;
            $copy->resize($size, null, function($constraint){ $constraint->aspectRatio(); $constraint->upsize(); });
            $variantPath = 'uploads/'.$uuid.'/variant_'.$size.'_'.basename($upload->filename);
            $copy->save(Storage::disk('public')->path($variantPath));
            $variants[$size] = $variantPath;
        }

        $image = Image::create([
            'upload_id'=>$upload->id,
            'path'=>$path,
            'variant_256'=>$variants[256] ?? null,
            'variant_512'=>$variants[512] ?? null,
            'variant_1024'=>$variants[1024] ?? null,
            'checksum'=>$checksum,
        ]);

        $upload->completed = true; $upload->checksum = $checksum; $upload->save();

        // Optionally link to entity
        if ($r->filled('entity_type') && $r->filled('entity_id')) {
            $etype = $r->input('entity_type');
            $eid = $r->input('entity_id');
            if ($etype === 'product') {
                $product = \App\Models\Product::find($eid);
                if ($product) {
                    // idempotent primary image set
                    if ($product->primary_image_id !== $image->id) {
                        $product->primary_image_id = $image->id; $product->save();
                    }
                }
            }
        }

        return response()->json(['ok'=>true, 'image_id'=>$image->id, 'variants'=>$variants]);
    }
}
