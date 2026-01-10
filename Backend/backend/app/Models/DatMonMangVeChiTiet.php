<?php
/**
 * ============================================================================
 * MODEL DATMON_MANGVE_CHITIET - CHI TIẾT ĐẶT MÓN MANG VỀ
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DatMonMangVeChiTiet extends Model
{
    use HasFactory;

    protected $table = 'datmon_mangve_chitiet';
    protected $primaryKey = 'ChiTietID';
    public $timestamps = false;

    protected $fillable = [
        'DatMonID',
        'MonAnID',
        'SoLuong',
        'DonGia',
        'GhiChu',
        'TaoLuc'
    ];

    protected $casts = [
        'SoLuong' => 'integer',
        'DonGia' => 'decimal:2'
    ];

    // Quan hệ với đơn đặt món
    public function datMon()
    {
        return $this->belongsTo(DatMonMangVe::class, 'DatMonID', 'DatMonID');
    }

    // Quan hệ với món ăn
    public function monAn()
    {
        return $this->belongsTo(MonAn::class, 'MonAnID', 'MonAnID');
    }

    // Tính thành tiền
    public function getThanhTienAttribute()
    {
        return $this->SoLuong * $this->DonGia;
    }
}
