<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatMon extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'datmon';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'DatMonID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'DatBanID',
        'MonAnID',
        'SoLuong',
        'GiaLucDat',
        'GhiChu',
        'TaoLuc'
    ];

    /**
     * Các trường ngày tháng
     */
    protected $dates = [
        'TaoLuc'
    ];

    /**
     * Các trường ép kiểu
     */
    protected $casts = [
        'GiaLucDat' => 'decimal:2',
        'SoLuong' => 'integer'
    ];

    /**
     * Quan hệ với DatBan
     */
    public function datBan()
    {
        return $this->belongsTo(DatBan::class, 'DatBanID', 'DatBanID');
    }

    /**
     * Quan hệ với MonAn
     */
    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'MonAnID', 'MonAnID');
    }

    /**
     * Accessor tính thành tiền
     */
    public function getThanhTienAttribute()
    {
        return $this->SoLuong * $this->GiaLucDat;
    }
}
