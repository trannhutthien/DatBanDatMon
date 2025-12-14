<?php
/**
 * ============================================================================
 * MODEL KHUVUC - KHU VỰC
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'khuvuc' trong database.
 * Quản lý các khu vực trong nhà hàng.
 * 
 * BẢNG DATABASE: khuvuc
 * KHÓA CHÍNH: KhuVucID (auto increment)
 * 
 * CÁC CỘT:
 * - KhuVucID: ID khu vực (PK)
 * - TenKhuVuc: Tên khu vực (VD: "Tầng 1", "Sân vườn", "Phòng VIP")
 * - MoTa: Mô tả khu vực
 * - TaoLuc: Thời gian tạo
 * 
 * MỤC ĐÍCH:
 * - Phân loại bàn theo khu vực
 * - Giúp khách chọn vị trí mong muốn khi đặt bàn
 * - Quản lý sức chứa từng khu vực
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuVuc extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'khuvuc';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'KhuVucID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'TenKhuVuc',   // Tên khu vực
        'MoTa',        // Mô tả
        'TaoLuc'       // Thời gian tạo
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     */
    protected $dates = [
        'TaoLuc'
    ];
}
