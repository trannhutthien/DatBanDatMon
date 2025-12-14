<?php
/**
 * ============================================================================
 * SEEDER: NHÀ HÀNG VÀ MÓN ĂN
 * ============================================================================
 * 
 * Tạo dữ liệu mẫu cho bảng nhahang và monan.
 * 
 * CHẠY:
 * php artisan db:seed --class=NhaHangMonAnSeeder
 * 
 * HOẶC chạy tất cả seeders:
 * php artisan db:seed
 * ============================================================================
 */

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NhaHangMonAnSeeder extends Seeder
{
    /**
     * Chạy seeder
     */
    public function run(): void
    {
        // ========== TẠO NHÀ HÀNG ==========
        $nhaHangs = [
            [
                'TenNhaHang' => 'Phở Hà Nội',
                'DiaChi' => '123 Nguyễn Huệ, Quận 1, TP.HCM',
                'SDT' => '0901234567',
                'Email' => 'phohanoi@email.com',
                'MoTa' => 'Phở truyền thống Hà Nội, nước dùng đậm đà',
                'TrangThai' => 1,
            ],
            [
                'TenNhaHang' => 'Bún Chả Dác Kim',
                'DiaChi' => '45 Lê Lợi, Quận 1, TP.HCM',
                'SDT' => '0902345678',
                'Email' => 'bunchadackim@email.com',
                'MoTa' => 'Bún chả Hà Nội chính hiệu',
                'TrangThai' => 1,
            ],
            [
                'TenNhaHang' => 'Cơm Gà Bà Buội',
                'DiaChi' => '78 Hai Bà Trưng, Quận 3, TP.HCM',
                'SDT' => '0903456789',
                'Email' => 'comgababuoi@email.com',
                'MoTa' => 'Cơm gà xối mỡ giòn rụm',
                'TrangThai' => 1,
            ],
            [
                'TenNhaHang' => 'Bánh Mì Huỳnh Hoa',
                'DiaChi' => '26 Lê Thị Riêng, Quận 1, TP.HCM',
                'SDT' => '0904567890',
                'Email' => 'banhmihh@email.com',
                'MoTa' => 'Bánh mì nổi tiếng Sài Gòn',
                'TrangThai' => 1,
            ],
            [
                'TenNhaHang' => 'Gỏi Cuốn Sài Gòn',
                'DiaChi' => '99 Pasteur, Quận 1, TP.HCM',
                'SDT' => '0905678901',
                'Email' => 'goicuonsg@email.com',
                'MoTa' => 'Gỏi cuốn tươi ngon mỗi ngày',
                'TrangThai' => 1,
            ],
            [
                'TenNhaHang' => 'Chè Cung Đình',
                'DiaChi' => '56 Nguyễn Trãi, Quận 5, TP.HCM',
                'SDT' => '0906789012',
                'Email' => 'checungdinh@email.com',
                'MoTa' => 'Chè truyền thống cung đình Huế',
                'TrangThai' => 1,
            ],
        ];

        foreach ($nhaHangs as $nhaHang) {
            DB::table('nhahang')->insert(array_merge($nhaHang, [
                'TaoLuc' => now(),
                'CapNhatLuc' => now(),
            ]));
        }

        // ========== TẠO MÓN ĂN ==========
        $monAns = [
            // Phở Hà Nội (NhaHangID = 1)
            [
                'TenMon' => 'Phở Bò Tái',
                'NhaHangID' => 1,
                'DonGia' => 65000,
                'MoTa' => 'Phở bò tái với nước dùng ninh xương 12 tiếng',
                'TrangThai' => 1,
            ],
            [
                'TenMon' => 'Phở Bò Chín',
                'NhaHangID' => 1,
                'DonGia' => 65000,
                'MoTa' => 'Phở bò chín mềm, thơm ngon',
                'TrangThai' => 1,
            ],
            [
                'TenMon' => 'Phở Gà',
                'NhaHangID' => 1,
                'DonGia' => 60000,
                'MoTa' => 'Phở gà ta thả vườn',
                'TrangThai' => 1,
            ],
            
            // Bún Chả Dác Kim (NhaHangID = 2)
            [
                'TenMon' => 'Bún Chả Hà Nội',
                'NhaHangID' => 2,
                'DonGia' => 70000,
                'MoTa' => 'Bún chả nướng than hoa, thịt thơm mềm',
                'TrangThai' => 1,
            ],
            [
                'TenMon' => 'Bún Chả Nem',
                'NhaHangID' => 2,
                'DonGia' => 80000,
                'MoTa' => 'Bún chả kèm nem rán giòn',
                'TrangThai' => 1,
            ],
            
            // Cơm Gà Bà Buội (NhaHangID = 3)
            [
                'TenMon' => 'Cơm Gà Xối Mỡ',
                'NhaHangID' => 3,
                'DonGia' => 60000,
                'MoTa' => 'Cơm gà xối mỡ giòn rụm, da vàng ươm',
                'TrangThai' => 1,
            ],
            [
                'TenMon' => 'Cơm Gà Luộc',
                'NhaHangID' => 3,
                'DonGia' => 55000,
                'MoTa' => 'Cơm gà luộc da giòn, thịt mềm',
                'TrangThai' => 1,
            ],
            
            // Bánh Mì Huỳnh Hoa (NhaHangID = 4)
            [
                'TenMon' => 'Bánh Mì Pate',
                'NhaHangID' => 4,
                'DonGia' => 25000,
                'MoTa' => 'Bánh mì pate thịt nguội đặc biệt',
                'TrangThai' => 1,
            ],
            [
                'TenMon' => 'Bánh Mì Thịt Nướng',
                'NhaHangID' => 4,
                'DonGia' => 35000,
                'MoTa' => 'Bánh mì thịt nướng than hoa',
                'TrangThai' => 1,
            ],
            
            // Gỏi Cuốn Sài Gòn (NhaHangID = 5)
            [
                'TenMon' => 'Gỏi Cuốn Tôm Thịt',
                'NhaHangID' => 5,
                'DonGia' => 45000,
                'MoTa' => 'Gỏi cuốn tôm thịt tươi ngon (4 cuốn)',
                'TrangThai' => 1,
            ],
            [
                'TenMon' => 'Gỏi Cuốn Bò',
                'NhaHangID' => 5,
                'DonGia' => 50000,
                'MoTa' => 'Gỏi cuốn bò nướng lá lốt (4 cuốn)',
                'TrangThai' => 1,
            ],
            
            // Chè Cung Đình (NhaHangID = 6)
            [
                'TenMon' => 'Chè Ba Màu',
                'NhaHangID' => 6,
                'DonGia' => 25000,
                'MoTa' => 'Chè ba màu truyền thống',
                'TrangThai' => 1,
            ],
            [
                'TenMon' => 'Chè Thái',
                'NhaHangID' => 6,
                'DonGia' => 30000,
                'MoTa' => 'Chè Thái nhiều topping',
                'TrangThai' => 1,
            ],
            [
                'TenMon' => 'Chè Đậu Xanh',
                'NhaHangID' => 6,
                'DonGia' => 20000,
                'MoTa' => 'Chè đậu xanh nước cốt dừa',
                'TrangThai' => 1,
            ],
        ];

        foreach ($monAns as $monAn) {
            DB::table('monan')->insert(array_merge($monAn, [
                'TaoLuc' => now(),
                'CapNhatLuc' => now(),
            ]));
        }
    }
}
