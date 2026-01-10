<?php
/**
 * ============================================================================
 * MODEL DATBAN_DATMON - CHI TIẾT MÓN ĂN KHI ĐẶT BÀN
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'datban_datmon' trong database.
 * Lưu trữ các món ăn được đặt trước khi đặt bàn.
 * 
 * BẢNG DATABASE: datban_datmon
 * KHÓA CHÍNH: ID (auto increment)
 * 
 * CÁC CỘT:
 * - ID: ID chi tiết (PK)
 * - DatBanID: FK -> datban
 * - MonAnID: FK -> monan
 * - SoLuong: Số lượng món
 * - DonGia: Đơn giá tại thời điểm đặt
 * - GhiChu: Ghi chú cho món (ít cay, không hành...)
 * - TaoLuc: Thời gian tạo
 * 
 * QUAN HỆ:
 * - belongsTo DatBan (thuộc về 1 đơn đặt bàn)
 * - belongsTo MonAn (thuộc về 1 món ăn)
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatBanDatMon extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     * Lưu ý: Tên bảng phải khớp chính xác với tên trong MySQL
     */
    protected $table = 'datban_datmon';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'ID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'DatBanID',      // FK đặt bàn
        'MonAnID',       // FK món ăn
        'SoLuong',       // Số lượng
        'DonGia',        // Đơn giá
        'GhiChu',        // Ghi chú món
        'TaoLuc'         // Thời gian tạo
    ];

    /**
     * ÉP KIỂU DỮ LIỆU
     */
    protected $casts = [
        'SoLuong' => 'integer',
        'DonGia' => 'decimal:2'
    ];

    /**
     * ========================================================================
     * QUAN HỆ VỚI DATBAN (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi chi tiết món thuộc về một đơn đặt bàn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function datBan()
    {
        return $this->belongsTo(
            DatBan::class,
            'DatBanID',
            'DatBanID'
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI MONAN (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi chi tiết món thuộc về một món ăn.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function monAn()
    {
        return $this->belongsTo(
            MonAn::class,
            'MonAnID',
            'MonAnID'
        );
    }

    /**
     * ========================================================================
     * ACCESSOR - TÍNH THÀNH TIỀN
     * ========================================================================
     * 
     * Tự động tính thành tiền = SoLuong * DonGia
     * 
     * SỬ DỤNG:
     * - $item->thanh_tien  // Lấy thành tiền
     */
    public function getThanhTienAttribute()
    {
        return $this->SoLuong * $this->DonGia;
    }
}
