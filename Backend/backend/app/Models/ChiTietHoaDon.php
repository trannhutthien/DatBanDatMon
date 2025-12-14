<?php
/**
 * ============================================================================
 * MODEL CHITIETHOADON - CHI TIẾT HÓA ĐƠN
 * ============================================================================
 * 
 * Model này đại diện cho bảng 'chitiethoadon' trong database.
 * Quản lý các món ăn trong một hóa đơn.
 * 
 * BẢNG DATABASE: chitiethoadon
 * KHÓA CHÍNH: ChiTietID (auto increment)
 * 
 * CÁC CỘT:
 * - ChiTietID: ID chi tiết (PK)
 * - HoaDonID: FK -> hoadon (thuộc hóa đơn nào)
 * - MonAnID: FK -> monan (món ăn nào)
 * - SoLuong: Số lượng
 * - DonGia: Đơn giá tại thời điểm thanh toán
 * - ThanhTien: GENERATED COLUMN = SoLuong × DonGia (tự động tính bởi DB)
 * 
 * QUAN HỆ:
 * - belongsTo HoaDon (thuộc về 1 hóa đơn)
 * - belongsTo MonAn (thuộc về 1 món ăn)
 * 
 * LƯU Ý:
 * - ThanhTien là GENERATED COLUMN trong MySQL
 * - Không cần và không thể gán giá trị cho ThanhTien
 * - MySQL tự động tính: ThanhTien = SoLuong * DonGia
 * ============================================================================
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietHoaDon extends Model
{
    use HasFactory;

    /**
     * TÊN BẢNG TRONG DATABASE
     */
    protected $table = 'chitiethoadon';

    /**
     * KHÓA CHÍNH
     */
    protected $primaryKey = 'ChiTietID';

    /**
     * TẮT TIMESTAMPS TỰ ĐỘNG
     */
    public $timestamps = false;

    /**
     * CÁC TRƯỜNG CÓ THỂ GÁN HÀNG LOẠT
     * 
     * LƯU Ý: ThanhTien KHÔNG có trong $fillable
     * Vì đây là GENERATED COLUMN, MySQL tự tính
     */
    protected $fillable = [
        'HoaDonID',    // FK hóa đơn
        'MonAnID',     // FK món ăn
        'SoLuong',     // Số lượng
        'DonGia'       // Đơn giá
        // ThanhTien - KHÔNG GÁN, MySQL tự tính
    ];

    /**
     * ÉP KIỂU DỮ LIỆU
     */
    protected $casts = [
        'DonGia' => 'decimal:2',     // 65000.00
        'ThanhTien' => 'decimal:2',  // 130000.00 (tự động)
        'SoLuong' => 'integer'        // 2
    ];

    /**
     * ========================================================================
     * QUAN HỆ VỚI HOADON (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi chi tiết thuộc về một hóa đơn.
     * 
     * SỬ DỤNG:
     * - $chiTiet->hoaDon                  // Lấy hóa đơn
     * - $chiTiet->hoaDon->NgayLap         // Ngày lập hóa đơn
     * - $chiTiet->hoaDon->TongTien        // Tổng tiền hóa đơn
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hoaDon()
    {
        return $this->belongsTo(
            HoaDon::class,
            'HoaDonID',
            'HoaDonID'
        );
    }

    /**
     * ========================================================================
     * QUAN HỆ VỚI MONAN (NHIỀU - MỘT)
     * ========================================================================
     * 
     * Mỗi chi tiết thuộc về một món ăn.
     * 
     * SỬ DỤNG:
     * - $chiTiet->monAn              // Lấy thông tin món ăn
     * - $chiTiet->monAn->TenMon      // Tên món
     * - $chiTiet->monAn->HinhAnh     // Hình ảnh món
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
}
