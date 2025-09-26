<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use League\Csv\Reader;
use Illuminate\Support\Str;

class CsvImportController extends Controller
{
    // POST /import/products
    public function importProducts(Request $request)
    {
        $file = $request->file('csv');
        if (!$file) return response()->json(['error'=>'csv required'], 400);

        $stream = fopen($file->getRealPath(), 'r');
        $csv = Reader::createFromStream($stream);
        $csv->setHeaderOffset(0);

        $summary = ['total'=>0,'imported'=>0,'updated'=>0,'invalid'=>0,'duplicates'=>0];
        $seen = [];

        foreach ($csv->getRecords() as $row) {
            $summary['total']++;
            // Validate required columns: sku,name,price
            if (empty($row['sku']) || empty($row['name']) || !isset($row['price'])) {
                $summary['invalid']++;
                continue;
            }
            $sku = trim($row['sku']);
            if (isset($seen[$sku])) { $summary['duplicates']++; continue; }
            $seen[$sku]=true;

            $attributes = [
                'name' => $row['name'],
                'description' => $row['description'] ?? null,
                'price' => is_numeric($row['price']) ? (float)$row['price'] : null,
            ];

            $product = Product::updateOrCreate(['sku'=>$sku], $attributes);
            if ($product->wasRecentlyCreated) $summary['imported']++; else $summary['updated']++;
        }

        return response()->json($summary);
    }
}
