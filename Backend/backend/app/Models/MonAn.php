<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MonAn extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'monan';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'MonAnID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'TenMon',
        'DonGia',
        'HinhAnh',
        'MoTa',
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
     * Các trường ép kiểu
     */
    protected $casts = [
        'DonGia' => 'decimal:2',
        'TrangThai' => 'integer'
    ];

    /**
     * Các trạng thái của món ăn
     */
    const TRANG_THAI_CON_MON = 1;   // Còn món
    const TRANG_THAI_HET_MON = 0;   // Hết món

    /**
     * Quan hệ với DatMon
     */
    public function datMon()
    {
        return $this->hasMany(DatMon::class, 'MonAnID', 'MonAnID');
    }

    /**
     * Quan hệ với ChiTietHoaDon
     */
    public function chiTietHoaDon()
    {
        return $this->hasMany(ChiTietHoaDon::class, 'MonAnID', 'MonAnID');
    }

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
