<?php

namespace UserDiscounts\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = ['code','type','value','active','expires_at','usage_cap'];
    protected $casts = ['active'=>'boolean','expires_at'=>'datetime'];
}
