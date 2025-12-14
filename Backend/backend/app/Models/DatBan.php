<?php
/**
 * ============================================================================
 * MODEL DATBAN - ĐẶT BÀN
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'datban' trong database.
 * Quản lý các đơn đặt bàn của khách hàng.
 * 
 * BẢNG DATABASE: datban
 * KHÓA CHÍNH: DatBanID (auto increment)
 * 
 * CÁC CỘT:
 * - DatBanID: ID đặt bàn (PK)
 * - KhachHangID: FK -> khachhang
 * - BanID: FK -> ban
 * - ThoiGianDen: Thời gian khách dự kiến đến
 * - SoNguoi: Số người
 * - GhiChu: Ghi chú đặc biệt
 * - TrangThai: 1=Chờ xác nhận, 2=Đã xác nhận, 3=Đã đến, 4=Hủy, 5=Hoàn thành
 * - TaoLuc: Thời gian tạo
 * - CapNhatLuc: Thời gian cập nhật
 * 
 * QUAN HỆ:
 * - belongsTo KhachHang (thuộc về 1 khách hàng)
 * - belongsTo Ban (thuộc về 1 bàn)
 * - hasMany DatMon (có nhiều món đặt trước)
 * - hasOne HoaDon (có 1 hóa đơn)
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatBan extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'datban';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'DatBanID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'KhachHangID',   // FK khách hàng
        'BanID',         // FK bàn
        'ThoiGianDen',   // Thời gian dự kiến đến
        'SoNguoi',       // Số người
        'GhiChu',        // Ghi chú
        'TrangThai',     // Trạng thái đặt bàn
        'TaoLuc',        // Thời gian tạo
        'CapNhatLuc'     // Thời gian cập nhật
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     */
    protected $dates = [
        'ThoiGianDen',   // Thời gian đến (datetime)
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * ========================================================================
     * CONSTANTS - TRẠNG THÁI ĐẶT BÀN
     * ========================================================================
     * 
     * Luồng trạng thái:
     * CHO_XAC_NHAN (1) -> DA_XAC_NHAN (2) -> DA_DEN (3) -> HOAN_THANH (5)
     *                                    \-> HUY (4)
     * 
     * SỬ DỤNG:
     * - $datBan->TrangThai = DatBan::TRANG_THAI_DA_XAC_NHAN
     */
    const TRANG_THAI_CHO_XAC_NHAN = 1;  // Khách vừa đặt, chờ nhà hàng xác nhận
    const TRANG_THAI_DA_XAC_NHAN = 2;   // Nhà hàng đã xác nhận, chờ khách đến
    const TRANG_THAI_DA_DEN = 3;        // Khách đã đến nhà hàng
    const TRANG_THAI_HUY = 4;           // Đã hủy (khách hoặc nhà hàng)
    const TRANG_THAI_HOAN_THANH = 5;    // Hoàn thành (đã thanh toán xong)

    /**
     * ========================================================================
     * QUAN HỆ VỚI KHACHHANG (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi đặt bàn thuộc về một khách hàng.
     * 
     * SỬ DỤNG:
     * - $datBan->khachHang              // Lấy thông tin khách hàng
     * - $datBan->khachHang->TenKH       // Tên khách hàng
     * - $datBan->khachHang->SDT         // SĐT khách hàng
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function khachHang()
    {
        return $this->belongsTo(
            KhachHang::class,    // Model liên kết
            'KhachHangID',       // FK trong bảng datban
            'KhachHangID'        // PK trong bảng khachhang
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI BAN (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi đặt bàn thuộc về một bàn.
     * 
     * SỬ DỤNG:
     * - $datBan->ban              // Lấy thông tin bàn
     * - $datBan->ban->SoBan       // Số bàn
     * - $datBan->ban->SoGhe       // Số ghế
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function ban()
    {
        return $this->belongsTo(
            Ban::class,
            'BanID',
            'BanID'
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI DATMON (MỘT - NHIỀU)
     * ========================================================================
     * 
     * Một đặt bàn có thể có nhiều món đặt trước.
     * Khách có thể đặt món khi đặt bàn để nhà hàng chuẩn bị.
     * 
     * SỬ DỤNG:
     * - $datBan->datMon                      // Lấy các món đã đặt
     * - $datBan->datMon()->sum('SoLuong')    // Tổng số món
     * - $datBan->datMon->sum('ThanhTien')    // Tổng tiền món đặt trước
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function datMon()
    {
        return $this->hasMany(
            DatMon::class,
            'DatBanID',
            'DatBanID'
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI HOADON (MỘT - MỘT)
     * ========================================================================
     * 
     * Mỗi đặt bàn có một hóa đơn (khi khách thanh toán).
     * 
     * SỬ DỤNG:
     * - $datBan->hoaDon              // Lấy hóa đơn
     * - $datBan->hoaDon->TongTien    // Tổng tiền hóa đơn
     * - $datBan->hoaDon->TrangThai   // Trạng thái thanh toán
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hoaDon()
    {
        return $this->hasOne(
            HoaDon::class,
            'DatBanID',
            'DatBanID'
        );
    }

    /**
     * ========================================================================
     * SCOPES - LỌC THEO TRẠNG THÁI
     * ========================================================================
     * 
     * SỬ DỤNG:
     * - DatBan::choXacNhan()->get()    // Đặt bàn chờ xác nhận
     * - DatBan::daXacNhan()->count()   // Đếm đặt bàn đã xác nhận
     * - DatBan::daDen()->whereDate('ThoiGianDen', today())->get()  // Khách đến hôm nay
     */
    
    /**
     * Scope lọc chờ xác nhận
     */
    public function scopeChoXacNhan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_CHO_XAC_NHAN);
    }

    /**
     * Scope lọc đã xác nhận
     */
    public function scopeDaXacNhan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_XAC_NHAN);
    }

    /**
     * Scope lọc đã đến
     */
    public function scopeDaDen($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_DEN);
    }

    /**
     * Scope lọc đã hủy
     */
    public function scopeDaHuy($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HUY);
    }

    /**
     * Scope lọc hoàn thành
     */
    public function scopeHoanThanh($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HOAN_THANH);
    }
}
