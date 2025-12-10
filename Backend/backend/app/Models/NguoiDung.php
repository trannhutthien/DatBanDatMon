<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class NguoiDung extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Tên bảng trong database
     */
    protected $table = 'nguoidung';

    /**
     * Khóa chính của bảng
     */
    protected $primaryKey = 'NguoiDungID';

    /**
     * Không sử dụng timestamps mặc định của Laravel
     */
    public $timestamps = false;

    /**
     * Các trường có thể gán hàng loạt
     */
    protected $fillable = [
        'HoTen',
        'Email',
        'MatKhau',
        'SDT',
        'LoaiNguoiDung',
        'DiaChi',
        'GhiChu',
        'TaoLuc',
        'CapNhatLuc',
        'GoogleID',
        'Avatar',
        'google_id',
        'google_token'
    ];

    /**
     * Các trường ẩn khi serialize
     */
    protected $hidden = [
        'MatKhau',
        'google_token'
    ];

    /**
     * Các trường ngày tháng
     */
    protected $dates = [
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * Các loại người dùng
     */
    const LOAI_ADMIN = 1;       // Quản trị viên
    const LOAI_NHAN_VIEN = 2;   // Nhân viên
    const LOAI_KHACH_HANG = 3;  // Khách hàng

    /**
     * Lấy tên cột mật khẩu cho authentication
     */
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    /**
     * Quan hệ với VaiTro (nhiều - nhiều)
     */
    public function vaiTro()
    {
        return $this->belongsToMany(VaiTro::class, 'nguoidung_vaitro', 'NguoiDungID', 'VaiTroID');
    }

    /**
     * Kiểm tra có vai trò cụ thể không
     */
    public function coVaiTro($maVaiTro)
    {
        return $this->vaiTro()->where('MaVaiTro', $maVaiTro)->exists();
    }

    /**
     * Scope lọc theo loại người dùng
     */
    public function scopeAdmin($query)
    {
        return $query->where('LoaiNguoiDung', self::LOAI_ADMIN);
    }

    public function scopeNhanVien($query)
    {
        return $query->where('LoaiNguoiDung', self::LOAI_NHAN_VIEN);
    }

    public function scopeKhachHang($query)
    {
        return $query->where('LoaiNguoiDung', self::LOAI_KHACH_HANG);
    }
}
