<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('products', function(Blueprint $t){
            $t->id();
            $t->string('sku')->unique();
            $t->string('name');
            $t->text('description')->nullable();
            $t->decimal('price', 12, 2)->nullable();
            $t->unsignedBigInteger('primary_image_id')->nullable();
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('products'); }
};
