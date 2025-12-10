<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhachHang extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'khachhang';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'KhachHangID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'TenKH',
        'SDT',
        'Email',
        'GhiChu',
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
     * Quan hệ với DatBan
     */
    public function datBan()
    {
        return $this->hasMany(DatBan::class, 'KhachHangID', 'KhachHangID');
    }
}
