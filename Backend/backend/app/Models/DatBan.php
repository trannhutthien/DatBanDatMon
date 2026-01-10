<?php
/**
 * ============================================================================
 * MODEL DATBAN - ĐẶT BÀN
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'datban' trong database.
 * Quản lý các đơn đặt bàn của khách hàng.
 * 
 * BẢNG DATABASE: datban
 * KHÓA CHÍNH: DatBanID (auto increment)
 * 
 * CÁC CỘT:
 * - DatBanID: ID đặt bàn (PK)
 * - NguoiDungID: FK -> nguoidung
 * - NhaHangID: FK -> nhahang
 * - BanID: FK -> ban
 * - ThoiGianDen: Thời gian khách dự kiến đến
 * - SoNguoi: Số người
 * - GhiChu: Ghi chú đặc biệt
 * - TrangThai: 1=Chờ xác nhận, 2=Đã xác nhận, 3=Đã đến, 4=Hủy, 5=Hoàn thành
 * - TaoLuc: Thời gian tạo
 * - CapNhatLuc: Thời gian cập nhật
 * 
 * QUAN HỆ:
 * - belongsTo NguoiDung (thuộc về 1 người dùng)
 * - belongsTo NhaHang (thuộc về 1 nhà hàng)
 * - belongsTo Ban (thuộc về 1 bàn)
 * - hasMany DatBanDatMon (có nhiều món đặt trước)
 * - hasOne HoaDon (có 1 hóa đơn)
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatBan extends Model
{
    use HasFactory;

    protected $table = 'datban';
    protected $primaryKey = 'DatBanID';
    public $timestamps = false;

    protected $fillable = [
        'NguoiDungID',   // FK người dùng
        'NhaHangID',     // FK nhà hàng
        'BanID',         // FK bàn
        'ThoiGianDen',   // Thời gian dự kiến đến
        'SoNguoi',       // Số người
        'GhiChu',        // Ghi chú
        'TrangThai',     // Trạng thái đặt bàn
        'TaoLuc',        // Thời gian tạo
        'CapNhatLuc'     // Thời gian cập nhật
    ];

    protected $dates = [
        'ThoiGianDen',
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * CONSTANTS - TRẠNG THÁI ĐẶT BÀN
     */
    const TRANG_THAI_CHO_XAC_NHAN = 1;
    const TRANG_THAI_DA_XAC_NHAN = 2;
    const TRANG_THAI_DA_DEN = 3;
    const TRANG_THAI_HUY = 4;
    const TRANG_THAI_HOAN_THANH = 5;

    /**
     * Quan hệ với NguoiDung
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungID', 'NguoiDungID');
    }

    /**
     * Quan hệ với NhaHang
     */
    public function nhaHang()
    {
        return $this->belongsTo(NhaHang::class, 'NhaHangID', 'NhaHangID');
    }

    /**
     * Quan hệ với Ban
     */
    public function ban()
    {
        return $this->belongsTo(Ban::class, 'BanID', 'BanID');
    }

    /**
     * Quan hệ với DatBanDatMon (các món đặt trước)
     */
    public function chiTietMon()
    {
        return $this->hasMany(DatBanDatMon::class, 'DatBanID', 'DatBanID');
    }

    /**
     * Quan hệ với HoaDon
     */
    public function hoaDon()
    {
        return $this->hasOne(HoaDon::class, 'DatBanID', 'DatBanID');
    }

    /**
     * Scopes
     */
    public function scopeChoXacNhan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_CHO_XAC_NHAN);
    }

    public function scopeDaXacNhan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_XAC_NHAN);
    }

    public function scopeDaHuy($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HUY);
    }

    public function scopeHoanThanh($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HOAN_THANH);
    }
}
