<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('product_additional_info', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing INT column as primary key
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade'); // Foreign key to the products table
            $table->string('content'); // VARCHAR column for the content of the information
            $table->string('icon_url'); // VARCHAR column for the URL of the associated icon
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_additional_info');
    }
};
