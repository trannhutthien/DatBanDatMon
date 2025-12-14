<?php
/**
 * ============================================================================
 * MODEL VAITRO - VAI TRÒ (PHÂN QUYỀN)
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'vaitro' trong database.
 * Quản lý các vai trò để phân quyền người dùng.
 * 
 * BẢNG DATABASE: vaitro
 * KHÓA CHÍNH: VaiTroID (auto increment)
 * 
 * CÁC CỘT:
 * - VaiTroID: ID vai trò (PK)
 * - MaVaiTro: Mã vai trò (VD: "ADMIN", "MANAGER", "STAFF")
 * - TenVaiTro: Tên hiển thị (VD: "Quản trị viên", "Quản lý", "Nhân viên")
 * - MoTa: Mô tả quyền hạn
 * 
 * QUAN HỆ:
 * - belongsToMany NguoiDung (nhiều-nhiều qua bảng nguoidung_vaitro)
 * 
 * CÁCH HOẠT ĐỘNG:
 * - Một người dùng có thể có nhiều vai trò
 * - Một vai trò có thể thuộc nhiều người dùng
 * - Bảng trung gian: nguoidung_vaitro (NguoiDungID, VaiTroID)
 * 
 * VÍ DỤ VAI TRÒ:
 * - ADMIN: Toàn quyền hệ thống
 * - MANAGER: Quản lý nhà hàng, xem báo cáo
 * - STAFF: Nhận đặt bàn, tạo hóa đơn
 * - KITCHEN: Xem đơn đặt món, cập nhật trạng thái
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VaiTro extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'vaitro';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'VaiTroID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     */
    protected $fillable = [
        'MaVaiTro',    // Mã vai trò (unique, dùng trong code)
        'TenVaiTro',   // Tên hiển thị
        'MoTa'         // Mô tả quyền hạn
    ];

    /**
     * ========================================================================
     * QUAN HỆ VỚI NGUOIDUNG (NHIỀU - NHIỀU)
     * ========================================================================
     * 
     * Một vai trò có thể thuộc nhiều người dùng.
     * 
     * BẢNG TRUNG GIAN: nguoidung_vaitro
     * - VaiTroID (FK -> vaitro)
     * - NguoiDungID (FK -> nguoidung)
     * 
     * SỬ DỤNG:
     * - $vaiTro->nguoiDung                    // Lấy tất cả người dùng có vai trò này
     * - $vaiTro->nguoiDung()->count()         // Đếm số người có vai trò này
     * - $vaiTro->nguoiDung()->attach($id)     // Gán vai trò cho người dùng
     * - $vaiTro->nguoiDung()->detach($id)     // Xóa vai trò khỏi người dùng
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function nguoiDung()
    {
        return $this->belongsToMany(
            NguoiDung::class,        // Model liên kết
            'nguoidung_vaitro',      // Bảng trung gian
            'VaiTroID',              // FK của model này trong bảng trung gian
            'NguoiDungID'            // FK của model kia trong bảng trung gian
        );
    }
}
