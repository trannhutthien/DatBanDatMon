<?php
/**
 * ============================================================================
 * MIGRATION: TẠO BẢNG NHAHANG
 * ============================================================================
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
     * Chạy migration - tạo bảng
     */
    public function up(): void
    {
        Schema::create('nhahang', function (Blueprint $table) {
            $table->id('NhaHangID');
            $table->string('TenNhaHang', 200);
            $table->string('DiaChi', 500)->nullable();
            $table->string('SDT', 20)->nullable();
            $table->string('Email', 100)->nullable();
            $table->text('MoTa')->nullable();
            $table->string('HinhAnh', 500)->nullable();
            $table->tinyInteger('TrangThai')->default(1);  // 1=Hoạt động
            $table->timestamp('TaoLuc')->useCurrent();
            $table->timestamp('CapNhatLuc')->useCurrent()->useCurrentOnUpdate();
        });
    }

    /**
     * Rollback migration - xóa bảng
     */
    public function down(): void
    {
        Schema::dropIfExists('nhahang');
    }
};
