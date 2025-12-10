<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuVuc extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'khuvuc';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'KhuVucID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'TenKhuVuc',
        'MoTa',
        'TaoLuc'
    ];

    /**
     * Các trường ngày tháng
     */
    protected $dates = [
        'TaoLuc'
    ];
}
