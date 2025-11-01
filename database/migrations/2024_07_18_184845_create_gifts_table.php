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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id(); // Auto-incrementing BIGINT column as primary key
            $table->string('description', 255); // Description of the gift
            $table->string('image_url', 255)->nullable(); // Image URL of the gift
            $table->decimal('value', 10, 2); // Value of the gift
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
