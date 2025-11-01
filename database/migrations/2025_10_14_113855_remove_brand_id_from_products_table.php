<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Nếu có ràng buộc khóa ngoại, cần kiểm tra trước khi xóa
            if (Schema::hasColumn('products', 'brand_id')) {
                try {
                    $table->dropForeign(['brand_id']);
                } catch (\Exception $e) {
                    // Nếu chưa từng có foreign key, bỏ qua lỗi
                }

                $table->dropColumn('brand_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('cascade');
        });
    }
};
