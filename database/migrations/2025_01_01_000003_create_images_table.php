<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('images', function(Blueprint $t){
            $t->id();
            $t->foreignId('upload_id')->constrained('uploads');
            $t->string('path');
            $t->string('variant_256')->nullable();
            $t->string('variant_512')->nullable();
            $t->string('variant_1024')->nullable();
            $t->string('checksum')->nullable();
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('images'); }
};
