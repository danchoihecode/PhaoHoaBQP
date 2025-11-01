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
        Schema::create('categories', function (Blueprint $table) {
            $table->id('id')->unsigned();
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->bigInteger('parent_id')->unsigned()->nullable();
            $table->timestamps();

            // Setting up the foreign key constraint
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
