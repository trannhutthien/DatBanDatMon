<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'vaitro';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'VaiTroID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'MaVaiTro',
        'TenVaiTro',
        'MoTa'
    ];

    /**
     * Quan hệ với NguoiDung (nhiều - nhiều)
     */
    public function nguoiDung()
    {
        return $this->belongsToMany(NguoiDung::class, 'nguoidung_vaitro', 'VaiTroID', 'NguoiDungID');
    }
}
