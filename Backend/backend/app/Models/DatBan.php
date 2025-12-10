<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatBan extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'datban';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'DatBanID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'KhachHangID',
        'BanID',
        'ThoiGianDen',
        'SoNguoi',
        'GhiChu',
        'TrangThai',
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * Các trường ngày tháng
     */
    protected $dates = [
        'ThoiGianDen',
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * Các trạng thái đặt bàn
     */
    const TRANG_THAI_CHO_XAC_NHAN = 1;  // Chờ xác nhận
    const TRANG_THAI_DA_XAC_NHAN = 2;   // Đã xác nhận
    const TRANG_THAI_DA_DEN = 3;        // Đã đến
    const TRANG_THAI_HUY = 4;           // Đã hủy
    const TRANG_THAI_HOAN_THANH = 5;    // Hoàn thành

    /**
     * Quan hệ với KhachHang
     */
    public function khachHang()
    {
        return $this->belongsTo(KhachHang::class, 'KhachHangID', 'KhachHangID');
    }

    /**
     * Quan hệ với Ban
     */
    public function ban()
    {
        return $this->belongsTo(Ban::class, 'BanID', 'BanID');
    }

    /**
     * Quan hệ với DatMon
     */
    public function datMon()
    {
        return $this->hasMany(DatMon::class, 'DatBanID', 'DatBanID');
    }

    /**
     * Quan hệ với HoaDon
     */
    public function hoaDon()
    {
        return $this->hasOne(HoaDon::class, 'DatBanID', 'DatBanID');
    }

    /**
     * Scope lọc theo trạng thái
     */
    public function scopeChoXacNhan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_CHO_XAC_NHAN);
    }

    public function scopeDaXacNhan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_XAC_NHAN);
    }

    public function scopeDaDen($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_DEN);
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
