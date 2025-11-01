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
        Schema::create('right_banners', function (Blueprint $table) {
            $table->id(); // Auto-incrementing BIGINT column as primary key
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Foreign key to the products table
            $table->string('img_url', 255); // Column for image URL
            $table->string('url_link', 255); // Column for URL link
            $table->boolean('is_visible'); // Boolean column to show if the banner is visible
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('right_banners');
    }
};
