<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;

    /**
     * Tên bảng trong database
     */
    protected $table = 'hoadon';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'HoaDonID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'DatBanID',
        'NgayLap',
        'GiamGia',
        'Thue',
        'TongTien',
        'PhuongThucTT',
        'TrangThai',
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * Các trường ngày tháng
     */
    protected $dates = [
        'NgayLap',
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * Các trường ép kiểu
     */
    protected $casts = [
        'GiamGia' => 'decimal:2',
        'Thue' => 'decimal:2',
        'TongTien' => 'decimal:2',
        'PhuongThucTT' => 'integer',
        'TrangThai' => 'integer'
    ];

    /**
     * Các phương thức thanh toán
     */
    const PHUONG_THUC_TIEN_MAT = 1;     // Tiền mặt
    const PHUONG_THUC_CHUYEN_KHOAN = 2; // Chuyển khoản
    const PHUONG_THUC_THE = 3;          // Thẻ

    /**
     * Các trạng thái hóa đơn
     */
    const TRANG_THAI_CHUA_THANH_TOAN = 1;  // Chưa thanh toán
    const TRANG_THAI_DA_THANH_TOAN = 2;    // Đã thanh toán
    const TRANG_THAI_HUY = 3;              // Đã hủy

    /**
     * Quan hệ với DatBan
     */
    public function datBan()
    {
        return $this->belongsTo(DatBan::class, 'DatBanID', 'DatBanID');
    }

    /**
     * Quan hệ với ChiTietHoaDon
     */
    public function chiTietHoaDon()
    {
        return $this->hasMany(ChiTietHoaDon::class, 'HoaDonID', 'HoaDonID');
    }

    /**
     * Scope lọc theo trạng thái
     */
    public function scopeChuaThanhToan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_CHUA_THANH_TOAN);
    }

    public function scopeDaThanhToan($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_DA_THANH_TOAN);
    }

    public function scopeDaHuy($query)
    {
        return $query->where('TrangThai', self::TRANG_THAI_HUY);
    }

    /**
     * Tính tổng tiền hóa đơn
     */
    public function tinhTongTien()
    {
        $tongTienMon = $this->chiTietHoaDon->sum('ThanhTien');
        $tongSauGiam = $tongTienMon - $this->GiamGia;
        $tongSauThue = $tongSauGiam + ($tongSauGiam * $this->Thue / 100);
        return $tongSauThue;
    }
}
