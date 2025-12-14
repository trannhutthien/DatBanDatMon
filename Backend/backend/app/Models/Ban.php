<?php
/**
 * ============================================================================
 * MODEL BAN - BÀN ĂN
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'ban' trong database.
 * Quản lý các bàn ăn trong nhà hàng.
 * 
 * BẢNG DATABASE: ban
 * KHÓA CHÍNH: BanID (auto increment)
 * 
 * CÁC CỘT:
 * - BanID: ID bàn (PK)
 * - SoBan: Số bàn (VD: "B01", "B02")
 * - SoGhe: Số ghế của bàn
 * - KhuVuc: Khu vực đặt bàn (VD: "Tầng 1", "Sân vườn")
 * - TrangThai: 1=Trống, 2=Đã đặt, 3=Đang dùng
 * - TaoLuc: Thời gian tạo
 * - CapNhatLuc: Thời gian cập nhật
 * 
 * QUAN HỆ:
 * - hasMany DatBan (một bàn có nhiều lượt đặt)
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ban extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'ban';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'BanID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'SoBan',       // Số/tên bàn
        'SoGhe',       // Số ghế
        'KhuVuc',      // Khu vực
        'TrangThai',   // Trạng thái
        'TaoLuc',      // Thời gian tạo
        'CapNhatLuc'   // Thời gian cập nhật
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     */
    protected $dates = [
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * ========================================================================
     * CONSTANTS - TRẠNG THÁI BÀN
     * ========================================================================
     * 
     * Các trạng thái của bàn:
     * - TRONG (1): Bàn trống, có thể đặt
     * - DA_DAT (2): Đã có người đặt, chờ khách đến
     * - DANG_DUNG (3): Khách đang ngồi ăn
     * 
     * SỬ DỤNG:
     * - $ban->TrangThai = Ban::TRANG_THAI_TRONG
     * - if ($ban->TrangThai == Ban::TRANG_THAI_DANG_DUNG)
     */
    const TRANG_THAI_TRONG = 1;      // Bàn trống - sẵn sàng
    const TRANG_THAI_DA_DAT = 2;     // Đã đặt - chờ khách
    const TRANG_THAI_DANG_DUNG = 3;  // Đang sử dụng - có khách

    /**
     * ========================================================================
     * QUAN HỆ VỚI DATBAN (MỘT - NHIỀU)
     * ========================================================================
     * 
     * Một bàn có thể được đặt nhiều lần (theo thời gian).
     * 
     * SỬ DỤNG:
     * - $ban->datBan                           // Tất cả lượt đặt bàn này
     * - $ban->datBan()->where('TrangThai', 2)  // Các đặt bàn đã xác nhận
     * - $ban->datBan()->latest('ThoiGianDen')  // Đặt bàn gần nhất
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function datBan()
    {
        return $this->hasMany(
            DatBan::class,    // Model liên kết
            'BanID',          // FK trong bảng datban
            'BanID'           // PK trong bảng ban
        );
    }

    /**
     * ========================================================================
     * SCOPES - LỌC THEO TRẠNG THÁI
     * ========================================================================
     * 
     * SỬ DỤNG:
     * - Ban::trong()->get()        // Lấy bàn trống
     * - Ban::daDat()->count()      // Đếm bàn đã đặt
     * - Ban::dangDung()->get()     // Lấy bàn đang dùng
     * 
     * Kết hợp với điều kiện khác:
     * - Ban::trong()->where('SoGhe', '>=', 4)->get()  // Bàn trống >= 4 ghế
     */
    
    /**
     * Scope lọc bàn trống
     */
    public function scopeTrong($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_TRONG);
    }

    /**
     * Scope lọc bàn đã đặt
     */
    public function scopeDaDat($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_DAT);
    }

    /**
     * Scope lọc bàn đang dùng
     */
    public function scopeDangDung($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DANG_DUNG);
    }
}
