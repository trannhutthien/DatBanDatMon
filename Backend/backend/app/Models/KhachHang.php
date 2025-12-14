<?php
/**
 * ============================================================================
 * MODEL KHACHHANG - KHÁCH HÀNG
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'khachhang' trong database.
 * Quản lý thông tin khách hàng đặt bàn.
 * 
 * BẢNG DATABASE: khachhang
 * KHÓA CHÍNH: KhachHangID (auto increment)
 * 
 * CÁC CỘT:
 * - KhachHangID: ID khách hàng (PK)
 * - TenKH: Tên khách hàng
 * - SDT: Số điện thoại
 * - Email: Email
 * - GhiChu: Ghi chú (VD: "Khách VIP", "Dị ứng hải sản")
 * - TaoLuc: Thời gian tạo
 * - CapNhatLuc: Thời gian cập nhật
 * 
 * QUAN HỆ:
 * - hasMany DatBan (một khách có nhiều lần đặt bàn)
 * 
 * PHÂN BIỆT VỚI NGUOIDUNG:
 * - NguoiDung: Tài khoản đăng nhập hệ thống (có mật khẩu)
 * - KhachHang: Thông tin khách đặt bàn (không cần tài khoản)
 * - Khách có thể đặt bàn qua điện thoại mà không cần đăng ký
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'khachhang';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'KhachHangID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'TenKH',       // Tên khách hàng
        'SDT',         // Số điện thoại
        'Email',       // Email
        'GhiChu',      // Ghi chú
        'TaoLuc',      // Thời gian tạo
        'CapNhatLuc'   // Thời gian cập nhật
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     */
    protected $dates = [
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * ========================================================================
     * QUAN HỆ VỚI DATBAN (MỘT - NHIỀU)
     * ========================================================================
     * 
     * Một khách hàng có thể đặt bàn nhiều lần.
     * 
     * SỬ DỤNG:
     * - $khachHang->datBan                           // Tất cả lần đặt bàn
     * - $khachHang->datBan()->count()                // Số lần đặt bàn
     * - $khachHang->datBan()->latest('TaoLuc')       // Đặt bàn gần nhất
     * - $khachHang->datBan()->where('TrangThai', 5)  // Các đặt bàn hoàn thành
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function datBan()
    {
        return $this->hasMany(
            DatBan::class,
            'KhachHangID',
            'KhachHangID'
        );
    }
}
