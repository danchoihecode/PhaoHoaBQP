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
        Schema::create('brands', function (Blueprint $table) {
            // $table->id(); // Creates an auto-incrementing BIGINT (primary key) named 'id'
            // $table->string('name', 255)->unique(); // 'name' column, with unique constraint
            // $table->string('img_url', 255); // 'img_url' column for storing image URLs
            // $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
            $table->id();
            $table->string('name');
            $table->string('img_url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brands');
    }
};
