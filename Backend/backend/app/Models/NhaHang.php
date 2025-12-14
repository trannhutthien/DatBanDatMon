<?php
/**
 * ============================================================================
 * MODEL NHAHANG - NHÀ HÀNG
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'nhahang' trong database.
 * Quản lý thông tin các nhà hàng/quán ăn.
 * 
 * BẢNG DATABASE: nhahang
 * KHÓA CHÍNH: NhaHangID (auto increment)
 * 
 * CÁC CỘT:
 * - NhaHangID: ID nhà hàng (PK)
 * - TenNhaHang: Tên nhà hàng
 * - DiaChi: Địa chỉ
 * - SDT: Số điện thoại
 * - Email: Email liên hệ
 * - MoTa: Mô tả nhà hàng
 * - HinhAnh: Logo/hình ảnh nhà hàng
 * - TrangThai: 1=Hoạt động, 0=Ngừng hoạt động
 * - TaoLuc: Thời gian tạo
 * - CapNhatLuc: Thời gian cập nhật
 * 
 * QUAN HỆ:
 * - hasMany MonAn (một nhà hàng có nhiều món ăn)
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaHang extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'nhahang';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'NhaHangID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'TenNhaHang',   // Tên nhà hàng
        'DiaChi',       // Địa chỉ
        'SDT',          // Số điện thoại
        'Email',        // Email
        'MoTa',         // Mô tả
        'HinhAnh',      // Logo/hình ảnh
        'TrangThai',    // Trạng thái hoạt động
        'TaoLuc',       // Thời gian tạo
        'CapNhatLuc'    // Thời gian cập nhật
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     */
    protected $dates = [
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * ÉP KIỂU DỮ LIỆU
     */
    protected $casts = [
        'TrangThai' => 'integer'
    ];

    /**
     * CONSTANTS - TRẠNG THÁI
     */
    const TRANG_THAI_HOAT_DONG = 1;
    const TRANG_THAI_NGUNG = 0;

    /**
     * ========================================================================
     * QUAN HỆ VỚI MONAN (MỘT - NHIỀU)
     * ========================================================================
     * 
     * Một nhà hàng có nhiều món ăn.
     * 
     * SỬ DỤNG:
     * - $nhaHang->monAn                    // Lấy tất cả món ăn
     * - $nhaHang->monAn()->conMon()->get() // Lấy món còn
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function monAn()
    {
        return $this->hasMany(MonAn::class, 'NhaHangID', 'NhaHangID');
    }

    /**
     * Scope lọc nhà hàng đang hoạt động
     */
    public function scopeHoatDong($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HOAT_DONG);
    }
}
