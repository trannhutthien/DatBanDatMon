<?php
/**
 * ============================================================================
 * MODEL DATMON - ĐẶT MÓN TRƯỚC
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'datmon' trong database.
 * Quản lý các món ăn được đặt trước khi khách đến.
 * 
 * BẢNG DATABASE: datmon
 * KHÓA CHÍNH: DatMonID (auto increment)
 * 
 * CÁC CỘT:
 * - DatMonID: ID đặt món (PK)
 * - DatBanID: FK -> datban (thuộc đơn đặt bàn nào)
 * - MonAnID: FK -> monan (món ăn nào)
 * - SoLuong: Số lượng đặt
 * - GiaLucDat: Giá tại thời điểm đặt (lưu lại để tránh thay đổi giá)
 * - GhiChu: Ghi chú (VD: "Không cay", "Ít đường")
 * - TaoLuc: Thời gian tạo
 * 
 * QUAN HỆ:
 * - belongsTo DatBan (thuộc về 1 đơn đặt bàn)
 * - belongsTo MonAn (thuộc về 1 món ăn)
 * 
 * LƯU Ý:
 * - GiaLucDat lưu giá tại thời điểm đặt, không phải giá hiện tại
 * - Điều này đảm bảo tính chính xác khi giá món thay đổi
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatMon extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'datmon';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'DatMonID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'DatBanID',    // FK đơn đặt bàn
        'MonAnID',     // FK món ăn
        'SoLuong',     // Số lượng
        'GiaLucDat',   // Giá tại thời điểm đặt
        'GhiChu',      // Ghi chú đặc biệt
        'TaoLuc'       // Thời gian tạo
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     */
    protected $dates = [
        'TaoLuc'
    ];

    /**
     * ÉP KIỂU DỮ LIỆU
     */
    protected $casts = [
        'GiaLucDat' => 'decimal:2',  // Giá tiền: 65000.00
        'SoLuong' => 'integer'        // Số lượng: 2
    ];

    /**
     * ========================================================================
     * QUAN HỆ VỚI DATBAN (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi đặt món thuộc về một đơn đặt bàn.
     * 
     * SỬ DỤNG:
     * - $datMon->datBan                  // Lấy đơn đặt bàn
     * - $datMon->datBan->ThoiGianDen     // Thời gian khách đến
     * - $datMon->datBan->khachHang       // Thông tin khách hàng
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
     * Mỗi đặt món thuộc về một món ăn.
     * 
     * SỬ DỤNG:
     * - $datMon->monAn              // Lấy thông tin món ăn
     * - $datMon->monAn->TenMon      // Tên món
     * - $datMon->monAn->HinhAnh     // Hình ảnh món
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
     * Accessor là method tự động được gọi khi truy cập attribute.
     * Tên method: get{AttributeName}Attribute
     * 
     * Tính thành tiền = Số lượng × Giá lúc đặt
     * 
     * SỬ DỤNG:
     * - $datMon->ThanhTien           // Tự động gọi method này
     * - $datMon->ThanhTien           // VD: 130000 (2 × 65000)
     * 
     * LƯU Ý: Đây là computed attribute, không lưu trong DB
     * 
     * @return float Thành tiền
     */
    public function getThanhTienAttribute()
    {
        return $this->SoLuong * $this->GiaLucDat;
    }
}
