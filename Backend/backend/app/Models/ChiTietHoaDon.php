<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietHoaDon extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'chitiethoadon';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'ChiTietID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     * Lưu ý: ThanhTien là GENERATED COLUMN nên không gán được
     */
    protected $fillable = [
        'HoaDonID',
        'MonAnID',
        'SoLuong',
        'DonGia'
    ];

    /**
     * Các trường ép kiểu
     */
    protected $casts = [
        'DonGia' => 'decimal:2',
        'ThanhTien' => 'decimal:2',
        'SoLuong' => 'integer'
    ];

    /**
     * Quan hệ với HoaDon
     */
    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'HoaDonID', 'HoaDonID');
    }

    /**
     * Quan hệ với MonAn
     */
    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'MonAnID', 'MonAnID');
    }
}
