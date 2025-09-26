<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('uploads', function(Blueprint $t){
            $t->id();
            $t->uuid('upload_uuid')->unique();
            $t->string('filename');
            $t->integer('total_chunks')->nullable();
            $t->boolean('completed')->default(false);
            $t->string('checksum')->nullable();
            $t->string('entity_type')->nullable();
            $t->unsignedBigInteger('entity_id')->nullable();
            $t->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('uploads'); }
};
