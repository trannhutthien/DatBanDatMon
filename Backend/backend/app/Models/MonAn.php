<?php
/**
 * ============================================================================
 * MODEL MONAN - MÓN ĂN
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'monan' trong database.
 * Quản lý danh sách các món ăn của nhà hàng.
 * 
 * BẢNG DATABASE: monan
 * KHÓA CHÍNH: MonAnID (auto increment)
 * 
 * CÁC CỘT:
 * - MonAnID: ID món ăn (PK)
 * - TenMon: Tên món ăn
 * - DonGia: Giá tiền (decimal)
 * - HinhAnh: Đường dẫn/URL hình ảnh
 * - MoTa: Mô tả món ăn
 * - TrangThai: 1=Còn món, 0=Hết món
 * - TaoLuc: Thời gian tạo
 * - CapNhatLuc: Thời gian cập nhật
 * 
 * QUAN HỆ:
 * - hasMany DatMon (một món có nhiều lần được đặt)
 * - hasMany ChiTietHoaDon (một món xuất hiện trong nhiều hóa đơn)
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonAn extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'monan';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'MonAnID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     * Bảng dùng TaoLuc, CapNhatLuc thay vì created_at, updated_at
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'TenMon',      // Tên món ăn
        'NhaHangID',   // FK nhà hàng (thuộc nhà hàng nào)
        'DanhMucID',   // FK danh mục món
        'DonGia',      // Giá tiền
        'HinhAnh',     // URL hình ảnh
        'MoTa',        // Mô tả
        'TrangThai',   // Trạng thái còn/hết
        'TaoLuc',      // Thời gian tạo
        'CapNhatLuc'   // Thời gian cập nhật
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     * Tự động convert sang Carbon instance
     */
    protected $dates = [
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * ÉP KIỂU DỮ LIỆU
     * 
     * decimal:2 = Số thập phân 2 chữ số sau dấu phẩy
     * integer = Số nguyên
     */
    protected $casts = [
        'DonGia' => 'decimal:2',    // 65000.00
        'TrangThai' => 'integer'     // 0 hoặc 1
    ];

    /**
     * ========================================================================
     * CONSTANTS - TRẠNG THÁI MÓN ĂN
     * ========================================================================
     * 
     * Dùng trong code thay vì hardcode số:
     * - MonAn::TRANG_THAI_CON_MON = 1
     * - MonAn::TRANG_THAI_HET_MON = 0
     */
    const TRANG_THAI_CON_MON = 1;   // Còn món - có thể đặt
    const TRANG_THAI_HET_MON = 0;   // Hết món - không thể đặt

    /**
     * ========================================================================
     * QUAN HỆ VỚI NHAHANG (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi món ăn thuộc về một nhà hàng.
     * 
     * SỬ DỤNG:
     * - $monAn->nhaHang                  // Lấy thông tin nhà hàng
     * - $monAn->nhaHang->TenNhaHang      // Tên nhà hàng
     * - $monAn->nhaHang->DiaChi          // Địa chỉ nhà hàng
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function nhaHang()
    {
        return $this->belongsTo(
            NhaHang::class,
            'NhaHangID',
            'NhaHangID'
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI DANHMUCMON (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi món ăn thuộc về một danh mục.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function danhMuc()
    {
        return $this->belongsTo(
            DanhMucMon::class,
            'DanhMucID',
            'DanhMucID'
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI DATMON (MỘT - NHIỀU)
     * ========================================================================
     * 
     * Một món ăn có thể được đặt nhiều lần (trong nhiều đơn đặt bàn).
     * 
     * SỬ DỤNG:
     * - $monAn->datMon                    // Lấy tất cả lần đặt món này
     * - $monAn->datMon()->count()         // Đếm số lần được đặt
     * - $monAn->datMon()->sum('SoLuong')  // Tổng số lượng đã đặt
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function datMon()
    {
        return $this->hasMany(
            DatMon::class,    // Model liên kết
            'MonAnID',        // FK trong bảng datmon
            'MonAnID'         // PK trong bảng monan
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI CHITIETHOADON (MỘT - NHIỀU)
     * ========================================================================
     * 
     * Một món ăn xuất hiện trong nhiều chi tiết hóa đơn.
     * 
     * SỬ DỤNG:
     * - $monAn->chiTietHoaDon                      // Lấy tất cả chi tiết hóa đơn
     * - $monAn->chiTietHoaDon()->sum('ThanhTien')  // Tổng doanh thu từ món này
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chiTietHoaDon()
    {
        return $this->hasMany(
            ChiTietHoaDon::class,
            'MonAnID',
            'MonAnID'
        );
    }

    /**
     * ========================================================================
     * SCOPES - LỌC THEO TRẠNG THÁI
     * ========================================================================
     * 
     * SỬ DỤNG:
     * - MonAn::conMon()->get()    // Lấy các món còn
     * - MonAn::hetMon()->get()    // Lấy các món hết
     * - MonAn::conMon()->where('DonGia', '<', 100000)->get()
     */
    
    /**
     * Scope lọc món còn
     */
    public function scopeConMon($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_CON_MON);
    }

    /**
     * Scope lọc món hết
     */
    public function scopeHetMon($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HET_MON);
    }
}
