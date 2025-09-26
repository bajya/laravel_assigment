<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('discounts', function(Blueprint $t){
            $t->id();
            $t->string('code')->unique();
            $t->string('type'); // percentage|fixed
            $t->decimal('value',8,2);
            $t->boolean('active')->default(true);
            $t->timestamp('expires_at')->nullable();
            $t->integer('usage_cap')->nullable();
            $t->timestamps();
        });

        Schema::create('user_discounts', function(Blueprint $t){
            $t->id();
            $t->foreignId('discount_id')->constrained('discounts');
            $t->unsignedBigInteger('user_id');
            $t->integer('used')->default(0);
            $t->timestamps();
        });

        Schema::create('discount_audits', function(Blueprint $t){
            $t->id();
            $t->unsignedBigInteger('user_id');
            $t->unsignedBigInteger('discount_id');
            $t->decimal('amount',10,2);
            $t->timestamps();
        });
    }
    public function down(){
        Schema::dropIfExists('discount_audits');
        Schema::dropIfExists('user_discounts');
        Schema::dropIfExists('discounts');
    }
};
