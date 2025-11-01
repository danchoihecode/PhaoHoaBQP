<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
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
            $table->string('name');
            $table->text('description');
            $table->unsignedBigInteger('brand_id');
            $table->string('slug')->unique();
            $table->string('sku');
            $table->string('serial_number');
            $table->string('stock_status');
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->boolean('is_cheap_percent')->default(0);
            $table->boolean('is_cheap_online')->default(0);
            $table->decimal('discount_price', 10, 2);
            $table->boolean('publish')->default(0);
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
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
