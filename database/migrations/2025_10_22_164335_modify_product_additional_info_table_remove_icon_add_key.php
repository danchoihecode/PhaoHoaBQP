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
        Schema::table('product_additional_info', function (Blueprint $table) {
            // 1. Thêm cột 'key' (kiểu string), đặt sau cột 'content'
            // (Bạn có thể bỏ ->after('content') nếu không quan tâm vị trí)
            $table->string('key')->after('content');

            // 2. Xóa bỏ cột 'icon_url'
            $table->dropColumn('icon_url');
        });
    }

    /**
     * Reverse the migrations.
     * (Hàm này để rollback, nó sẽ làm ngược lại)
     */
    public function down(): void
    {
        Schema::table('product_additional_info', function (Blueprint $table) {
            // 1. Thêm lại cột 'icon_url' (Giả định nó là string và nullable)
            // (Đặt sau 'content' để đúng vị trí cũ nếu 'key' cũng ở đó)
            $table->string('icon_url')->nullable()->after('content');
            
            // 2. Xóa bỏ cột 'key'
            $table->dropColumn('key');
        });
    }
};