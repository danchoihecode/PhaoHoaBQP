<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id(); // Auto-incrementing BIGINT column as primary key
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Foreign key to the products table
            $table->foreignId('category_id')->constrained()->onDelete('cascade'); // Foreign key to the categories table
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_categories');
    }
}
