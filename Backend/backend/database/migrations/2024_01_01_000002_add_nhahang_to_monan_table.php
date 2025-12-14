<?php
/**
 * ============================================================================
 * MIGRATION: THÊM CỘT NHAHANGID VÀO BẢNG MONAN
 * ============================================================================
 * 
 * Thêm foreign key liên kết món ăn với nhà hàng
 * 
 * Chạy migration: php artisan migrate
 * Rollback: php artisan migrate:rollback
 * ============================================================================
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Chạy migration - thêm cột
     */
    public function up(): void
    {
        Schema::table('monan', function (Blueprint $table) {
            // Thêm cột NhaHangID sau cột MonAnID
            $table->unsignedBigInteger('NhaHangID')->nullable()->after('MonAnID');
            
            // Thêm foreign key
            $table->foreign('NhaHangID')
                  ->references('NhaHangID')
                  ->on('nhahang')
                  ->onDelete('set null');  // Nếu xóa nhà hàng, set null
        });
    }

    /**
     * Rollback migration - xóa cột
     */
    public function down(): void
    {
        Schema::table('monan', function (Blueprint $table) {
            $table->dropForeign(['NhaHangID']);
            $table->dropColumn('NhaHangID');
        });
    }
};
