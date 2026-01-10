<?php
/**
 * ============================================================================
 * MODEL DATMON_MANGVE - ĐẶT MÓN MANG VỀ
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatMonMangVe extends Model
{
    use HasFactory;

    protected $table = 'datmon_mangve';
    protected $primaryKey = 'DatMonID';
    public $timestamps = false;

    // Trạng thái đơn hàng
    const TRANG_THAI_CHO_XAC_NHAN = 1;
    const TRANG_THAI_DA_XAC_NHAN = 2;
    const TRANG_THAI_DANG_CHUAN_BI = 3;
    const TRANG_THAI_SAN_SANG = 4;
    const TRANG_THAI_DA_GIAO = 5;
    const TRANG_THAI_HUY = 6;

    protected $fillable = [
        'NguoiDungID',
        'NhaHangID',
        'HoTen',
        'SDT',
        'DiaChi',
        'ThoiGianLay',
        'TongTien',
        'GhiChu',
        'TrangThai',
        'TaoLuc',
        'CapNhatLuc'
    ];

    protected $casts = [
        'TongTien' => 'decimal:2',
        'TrangThai' => 'integer'
    ];

    // Quan hệ với NguoiDung
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'NguoiDungID', 'NguoiDungID');
    }

    // Quan hệ với NhaHang
    public function nhaHang()
    {
        return $this->belongsTo(NhaHang::class, 'NhaHangID', 'NhaHangID');
    }

    // Quan hệ với chi tiết món
    public function chiTiet()
    {
        return $this->hasMany(DatMonMangVeChiTiet::class, 'DatMonID', 'DatMonID');
    }

    // Lấy text trạng thái
    public function getTrangThaiTextAttribute()
    {
        $texts = [
            1 => 'Chờ xác nhận',
            2 => 'Đã xác nhận',
            3 => 'Đang chuẩn bị',
            4 => 'Sẵn sàng',
            5 => 'Đã giao',
            6 => 'Đã hủy'
        ];
        return $texts[$this->TrangThai] ?? 'Không xác định';
    }
}
