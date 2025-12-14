<?php
/**
 * ============================================================================
 * MODEL NGUOIDUNG - NGƯỜI DÙNG HỆ THỐNG
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'nguoidung' trong database.
 * Quản lý tất cả người dùng: Admin, Nhân viên, Khách hàng.
 * 
 * BẢNG DATABASE: nguoidung
 * KHÓA CHÍNH: NguoiDungID (auto increment)
 * 
 * CÁC CỘT CHÍNH:
 * - NguoiDungID: ID người dùng (PK)
 * - HoTen: Họ tên đầy đủ
 * - Email: Email (unique)
 * - MatKhau: Mật khẩu đã mã hóa (bcrypt)
 * - SDT: Số điện thoại
 * - LoaiNguoiDung: 1=Admin, 2=Nhân viên, 3=Khách hàng
 * - DiaChi: Địa chỉ
 * - GhiChu: Ghi chú
 * - google_id: Google User ID (cho OAuth)
 * - google_token: Google Access Token
 * - Avatar: URL ảnh đại diện
 * - TaoLuc: Thời gian tạo
 * - CapNhatLuc: Thời gian cập nhật
 * 
 * QUAN HỆ:
 * - belongsToMany VaiTro (nhiều-nhiều qua bảng nguoidung_vaitro)
 * 
 * SỬ DỤNG:
 * - AuthController: Đăng ký, đăng nhập, Google OAuth
 * - config/auth.php: Provider 'users' dùng model này
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;  // Base class cho authentication
use Illuminate\Notifications\Notifiable;                  // Gửi notifications
use Laravel\Sanctum\HasApiTokens;                         // Tạo API tokens

/**
 * Class NguoiDung
 * 
 * Extend Authenticatable thay vì Model để hỗ trợ authentication.
 * Authenticatable cung cấp các method cần thiết cho Laravel Auth.
 */
class NguoiDung extends Authenticatable
{
    /**
     * TRAITS SỬ DỤNG:
     * 
     * HasApiTokens: Từ Laravel Sanctum
     * - Cho phép tạo API tokens: $user->createToken('name')
     * - Lấy token hiện tại: $user->currentAccessToken()
     * - Lấy tất cả tokens: $user->tokens()
     * 
     * HasFactory: Cho phép dùng Factory để tạo test data
     * - NguoiDung::factory()->create()
     * 
     * Notifiable: Cho phép gửi notifications
     * - $user->notify(new WelcomeNotification())
     */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * TÊN BẢNG TRONG DATABASE
     * 
     * Mặc định Laravel sẽ dùng tên class số nhiều (nguoi_dungs)
     * Phải chỉ định rõ vì bảng tên là 'nguoidung'
     */
    protected $table = 'nguoidung';

    /**
     * KHÓA CHÍNH
     * 
     * Mặc định Laravel dùng 'id'
     * Phải chỉ định vì bảng dùng 'NguoiDungID'
     */
    protected $primaryKey = 'NguoiDungID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     * 
     * Laravel mặc định tự động cập nhật created_at, updated_at
     * Tắt vì bảng dùng TaoLuc, CapNhatLuc (tự quản lý)
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT (Mass Assignment)
     * 
     * Chỉ các trường trong $fillable mới được gán qua:
     * - NguoiDung::create([...])
     * - $nguoiDung->fill([...])
     * - $nguoiDung->update([...])
     * 
     * Bảo vệ khỏi mass assignment vulnerability
     */
    protected $fillable = [
        'HoTen',           // Họ tên
        'Email',           // Email (unique)
        'MatKhau',         // Mật khẩu (đã hash)
        'SDT',             // Số điện thoại
        'LoaiNguoiDung',   // Loại: 1=Admin, 2=NV, 3=KH
        'DiaChi',          // Địa chỉ
        'GhiChu',          // Ghi chú
        'TaoLuc',          // Thời gian tạo
        'CapNhatLuc',      // Thời gian cập nhật
        'GoogleID',        // Google ID (legacy)
        'Avatar',          // URL ảnh đại diện
        'google_id',       // Google User ID
        'google_token'     // Google Access Token
    ];

    /**
     * CÁC TRƯỜNG ẨN KHI SERIALIZE
     * 
     * Các trường này sẽ bị ẩn khi:
     * - Trả về JSON response
     * - Gọi toArray()
     * - Gọi toJson()
     * 
     * QUAN TRỌNG: Ẩn mật khẩu và token nhạy cảm!
     */
    protected $hidden = [
        'MatKhau',       // Không bao giờ trả về mật khẩu
        'google_token'   // Không trả về Google token
    ];

    /**
     * CÁC TRƯỜNG NGÀY THÁNG
     * 
     * Laravel tự động convert sang Carbon instance
     * Cho phép dùng các method như: $user->TaoLuc->format('d/m/Y')
     */
    protected $dates = [
        'TaoLuc',
        'CapNhatLuc'
    ];

    /**
     * ========================================================================
     * CONSTANTS - LOẠI NGƯỜI DÙNG
     * ========================================================================
     * 
     * Định nghĩa các loại người dùng để dùng trong code.
     * Thay vì hardcode số, dùng constant cho dễ đọc và bảo trì.
     * 
     * Ví dụ:
     * - NguoiDung::LOAI_ADMIN = 1
     * - $user->LoaiNguoiDung == NguoiDung::LOAI_KHACH_HANG
     */
    const LOAI_ADMIN = 1;       // Quản trị viên - toàn quyền
    const LOAI_NHAN_VIEN = 2;   // Nhân viên - quản lý đơn hàng, bàn
    const LOAI_KHACH_HANG = 3;  // Khách hàng - đặt bàn, đặt món

    /**
     * ========================================================================
     * LẤY CỘT MẬT KHẨU CHO AUTHENTICATION
     * ========================================================================
     * 
     * Laravel Auth mặc định tìm cột 'password'
     * Override method này để chỉ định cột 'MatKhau'
     * 
     * Được gọi bởi:
     * - Hash::check() khi đăng nhập
     * - Auth::attempt()
     * 
     * @return string Mật khẩu đã hash
     */
    public function getAuthPassword()
    {
        return $this->MatKhau;
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI VAI TRÒ (NHIỀU - NHIỀU)
     * ========================================================================
     * 
     * Một người dùng có thể có nhiều vai trò.
     * Một vai trò có thể thuộc nhiều người dùng.
     * 
     * BẢNG TRUNG GIAN: nguoidung_vaitro
     * - NguoiDungID (FK -> nguoidung)
     * - VaiTroID (FK -> vaitro)
     * 
     * SỬ DỤNG:
     * - $user->vaiTro                    // Lấy tất cả vai trò
     * - $user->vaiTro()->attach($id)     // Thêm vai trò
     * - $user->vaiTro()->detach($id)     // Xóa vai trò
     * - $user->vaiTro()->sync([$id1, $id2]) // Đồng bộ vai trò
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vaiTro()
    {
        return $this->belongsToMany(
            VaiTro::class,           // Model liên kết
            'nguoidung_vaitro',      // Bảng trung gian
            'NguoiDungID',           // FK của model này trong bảng trung gian
            'VaiTroID'               // FK của model kia trong bảng trung gian
        );
    }

    /**
     * ========================================================================
     * KIỂM TRA CÓ VAI TRÒ CỤ THỂ KHÔNG
     * ========================================================================
     * 
     * Kiểm tra user có vai trò với mã cụ thể không.
     * 
     * SỬ DỤNG:
     * - $user->coVaiTro('ADMIN')     // true/false
     * - $user->coVaiTro('MANAGER')
     * 
     * @param string $maVaiTro Mã vai trò cần kiểm tra
     * @return bool
     */
    public function coVaiTro($maVaiTro)
    {
        return $this->vaiTro()->where('MaVaiTro', $maVaiTro)->exists();
    }

    /**
     * ========================================================================
     * SCOPES - LỌC THEO LOẠI NGƯỜI DÙNG
     * ========================================================================
     * 
     * Scopes là các method để filter query một cách tiện lợi.
     * 
     * SỬ DỤNG:
     * - NguoiDung::admin()->get()      // Lấy tất cả admin
     * - NguoiDung::nhanVien()->get()   // Lấy tất cả nhân viên
     * - NguoiDung::khachHang()->count() // Đếm khách hàng
     * 
     * Có thể chain với các query khác:
     * - NguoiDung::admin()->where('Email', 'like', '%@gmail.com')->get()
     */
    
    /**
     * Scope lọc Admin
     */
    public function scopeAdmin($query)
    {
        return $query->where('LoaiNguoiDung', self::LOAI_ADMIN);
    }

    /**
     * Scope lọc Nhân viên
     */
    public function scopeNhanVien($query)
    {
        return $query->where('LoaiNguoiDung', self::LOAI_NHAN_VIEN);
    }

    /**
     * Scope lọc Khách hàng
     */
    public function scopeKhachHang($query)
    {
        return $query->where('LoaiNguoiDung', self::LOAI_KHACH_HANG);
    }
}
