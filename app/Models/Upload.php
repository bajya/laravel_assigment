<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Upload extends Model
{
    protected $fillable = ['upload_uuid','filename','total_chunks','completed','checksum','entity_type','entity_id'];

    public function images()
    {
        return $this->hasMany(Image::class, 'upload_id');
    }
}
