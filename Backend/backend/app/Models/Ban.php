<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'ban';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'BanID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'SoBan',
        'SoGhe',
        'KhuVuc',
        'TrangThai',
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * Các trường ngày tháng
     */
    protected $dates = [
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * Các trạng thái của bàn
     */
    const TRANG_THAI_TRONG = 1;      // Bàn trống
    const TRANG_THAI_DA_DAT = 2;     // Đã đặt
    const TRANG_THAI_DANG_DUNG = 3;  // Đang sử dụng

    /**
     * Quan hệ với DatBan
     */
    public function datBan()
    {
        return $this->hasMany(DatBan::class, 'BanID', 'BanID');
    }

    /**
     * Scope lọc theo trạng thái
     */
    public function scopeTrong($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_TRONG);
    }

    public function scopeDaDat($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_DAT);
    }

    public function scopeDangDung($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DANG_DUNG);
    }
}
