<?php
/**
 * ============================================================================
 * MODEL HOADON - HÓA ĐƠN
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'hoadon' trong database.
 * Quản lý hóa đơn thanh toán của khách hàng.
 * 
 * BẢNG DATABASE: hoadon
 * KHÓA CHÍNH: HoaDonID (auto increment)
 * 
 * CÁC CỘT:
 * - HoaDonID: ID hóa đơn (PK)
 * - DatBanID: FK -> datban (thuộc đơn đặt bàn nào)
 * - NgayLap: Ngày lập hóa đơn
 * - GiamGia: Số tiền giảm giá (VNĐ)
 * - Thue: Phần trăm thuế (%)
 * - TongTien: Tổng tiền sau giảm giá và thuế
 * - PhuongThucTT: 1=Tiền mặt, 2=Chuyển khoản, 3=Thẻ
 * - TrangThai: 1=Chưa thanh toán, 2=Đã thanh toán, 3=Hủy
 * - TaoLuc: Thời gian tạo
 * - CapNhatLuc: Thời gian cập nhật
 * 
 * QUAN HỆ:
 * - belongsTo DatBan (thuộc về 1 đơn đặt bàn)
 * - hasMany ChiTietHoaDon (có nhiều chi tiết)
 * 
 * CÔNG THỨC TÍNH TIỀN:
 * TongTien = (TongTienMon - GiamGia) × (1 + Thue/100)
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'hoadon';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'HoaDonID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'DatBanID',      // FK đơn đặt bàn
        'NguoiDungID',   // FK người dùng
        'NhaHangID',     // FK nhà hàng
        'NgayLap',       // Ngày lập hóa đơn
        'GiamGia',       // Số tiền giảm giá
        'Thue',          // Phần trăm thuế
        'TongTien',      // Tổng tiền cuối cùng
        'PhuongThucTT',  // Phương thức thanh toán
        'TrangThai',     // Trạng thái hóa đơn
        'TaoLuc',        // Thời gian tạo
        'CapNhatLuc'     // Thời gian cập nhật
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     */
    protected $dates = [
        'NgayLap',
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * ÉP KIỂU DỮ LIỆU
     */
    protected $casts = [
        'GiamGia' => 'decimal:2',     // 50000.00
        'Thue' => 'decimal:2',         // 10.00 (%)
        'TongTien' => 'decimal:2',     // 500000.00
        'PhuongThucTT' => 'integer',   // 1, 2, 3
        'TrangThai' => 'integer'       // 1, 2, 3
    ];

    /**
     * ========================================================================
     * CONSTANTS - PHƯƠNG THỨC THANH TOÁN
     * ========================================================================
     */
    const PHUONG_THUC_TIEN_MAT = 1;     // Thanh toán tiền mặt
    const PHUONG_THUC_CHUYEN_KHOAN = 2; // Chuyển khoản ngân hàng
    const PHUONG_THUC_THE = 3;          // Quẹt thẻ (Visa, Master, etc.)

    /**
     * ========================================================================
     * CONSTANTS - TRẠNG THÁI HÓA ĐƠN
     * ========================================================================
     */
    const TRANG_THAI_CHUA_THANH_TOAN = 1;  // Chưa thanh toán - đang chờ
    const TRANG_THAI_DA_THANH_TOAN = 2;    // Đã thanh toán - hoàn tất
    const TRANG_THAI_HUY = 3;              // Đã hủy

    /**
     * ========================================================================
     * QUAN HỆ VỚI NGUOIDUNG (NHIỀU - MỘT)
     * ========================================================================
     */
    public function nguoiDung()
    {
        return $this->belongsTo(
            NguoiDung::class,
            'NguoiDungID',
            'NguoiDungID'
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI NHAHANG (NHIỀU - MỘT)
     * ========================================================================
     */
    public function nhaHang()
    {
        return $this->belongsTo(
            NhaHang::class,
            'NhaHangID',
            'NhaHangID'
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI DATBAN (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi hóa đơn thuộc về một đơn đặt bàn.
     * 
     * SỬ DỤNG:
     * - $hoaDon->datBan                  // Lấy đơn đặt bàn
     * - $hoaDon->datBan->ban             // Lấy thông tin bàn
     * - $hoaDon->datBan->khachHang       // Lấy thông tin khách hàng
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
     * QUAN HỆ VỚI CHITIETHOADON (MỘT - NHIỀU)
     * ========================================================================
     * 
     * Một hóa đơn có nhiều chi tiết (các món ăn).
     * 
     * SỬ DỤNG:
     * - $hoaDon->chiTietHoaDon                      // Lấy tất cả chi tiết
     * - $hoaDon->chiTietHoaDon->sum('ThanhTien')    // Tổng tiền các món
     * - $hoaDon->chiTietHoaDon()->with('monAn')     // Eager load món ăn
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chiTietHoaDon()
    {
        return $this->hasMany(
            ChiTietHoaDon::class,
            'HoaDonID',
            'HoaDonID'
        );
    }

    /**
     * ========================================================================
     * SCOPES - LỌC THEO TRẠNG THÁI
     * ========================================================================
     * 
     * SỬ DỤNG:
     * - HoaDon::chuaThanhToan()->get()    // Hóa đơn chưa thanh toán
     * - HoaDon::daThanhToan()->sum('TongTien')  // Tổng doanh thu
     * - HoaDon::daThanhToan()->whereDate('NgayLap', today())->get()  // Hôm nay
     */
    
    /**
     * Scope lọc chưa thanh toán
     */
    public function scopeChuaThanhToan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_CHUA_THANH_TOAN);
    }

    /**
     * Scope lọc đã thanh toán
     */
    public function scopeDaThanhToan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_THANH_TOAN);
    }

    /**
     * Scope lọc đã hủy
     */
    public function scopeDaHuy($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HUY);
    }

    /**
     * ========================================================================
     * METHOD - TÍNH TỔNG TIỀN HÓA ĐƠN
     * ========================================================================
     * 
     * Tính tổng tiền dựa trên chi tiết hóa đơn, giảm giá và thuế.
     * 
     * CÔNG THỨC:
     * 1. Tổng tiền món = SUM(chi tiết.ThanhTien)
     * 2. Sau giảm giá = Tổng tiền món - GiamGia
     * 3. Sau thuế = Sau giảm giá × (1 + Thue/100)
     * 
     * SỬ DỤNG:
     * - $tongTien = $hoaDon->tinhTongTien()
     * - $hoaDon->TongTien = $hoaDon->tinhTongTien()
     * - $hoaDon->save()
     * 
     * @return float Tổng tiền sau giảm giá và thuế
     */
    public function tinhTongTien()
    {
        // Bước 1: Tính tổng tiền các món
        // ThanhTien = SoLuong × DonGia (trong ChiTietHoaDon)
        $tongTienMon = $this->chiTietHoaDon->sum('ThanhTien');
        
        // Bước 2: Trừ giảm giá
        $tongSauGiam = $tongTienMon - $this->GiamGia;
        
        // Bước 3: Cộng thuế
        // VD: Thuế 10% -> nhân với 1.1
        $tongSauThue = $tongSauGiam + ($tongSauGiam * $this->Thue / 100);
        
        return $tongSauThue;
    }
}
