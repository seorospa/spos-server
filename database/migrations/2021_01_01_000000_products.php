<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Products extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('title');
            $table->double('price');
            $table->double('cost')->nullable();
            $table->double('qty')->nullable();
            $table->double('min')->nullable();
            $table->double('max')->nullable();
            $table->double('ws_min')->nullable();
            $table->double('ws_price')->nullable();
            $table->integer('category')->nullable();
            $table->boolean('unit')->default(true);
            $table->boolean('enable')->default(true);
            $table->string('taxes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
