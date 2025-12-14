-- ============================================================================
-- SQL: TẠO BẢNG NHAHANG VÀ CẬP NHẬT MONAN
-- ============================================================================
-- Chạy file này trong MySQL để tạo bảng và dữ liệu mẫu
-- ============================================================================

-- Tạo bảng nhahang
CREATE TABLE IF NOT EXISTS `nhahang` (
  `NhaHangID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `TenNhaHang` VARCHAR(200) NOT NULL,
  `DiaChi` VARCHAR(500) NULL,
  `SDT` VARCHAR(20) NULL,
  `Email` VARCHAR(100) NULL,
  `MoTa` TEXT NULL,
  `HinhAnh` VARCHAR(500) NULL,
  `TrangThai` TINYINT DEFAULT 1 COMMENT '1=Hoạt động, 0=Ngừng',
  `TaoLuc` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `CapNhatLuc` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`NhaHangID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm cột NhaHangID vào bảng monan (nếu chưa có)
-- Kiểm tra trước khi thêm
SET @column_exists = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
  WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'monan' 
  AND COLUMN_NAME = 'NhaHangID'
);

SET @sql = IF(@column_exists = 0, 
  'ALTER TABLE `monan` ADD COLUMN `NhaHangID` BIGINT UNSIGNED NULL AFTER `MonAnID`',
  'SELECT "Column NhaHangID already exists"'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Thêm foreign key (nếu chưa có)
-- ALTER TABLE `monan` ADD CONSTRAINT `fk_monan_nhahang` 
-- FOREIGN KEY (`NhaHangID`) REFERENCES `nhahang`(`NhaHangID`) ON DELETE SET NULL;

-- ============================================================================
-- DỮ LIỆU MẪU
-- ============================================================================

-- Xóa dữ liệu cũ (nếu cần)
-- TRUNCATE TABLE nhahang;

-- Thêm nhà hàng mẫu
INSERT INTO `nhahang` (`TenNhaHang`, `DiaChi`, `SDT`, `Email`, `MoTa`, `TrangThai`) VALUES
('Phở Hà Nội', '123 Nguyễn Huệ, Quận 1, TP.HCM', '0901234567', 'phohanoi@email.com', 'Phở truyền thống Hà Nội, nước dùng đậm đà', 1),
('Bún Chả Dác Kim', '45 Lê Lợi, Quận 1, TP.HCM', '0902345678', 'bunchadackim@email.com', 'Bún chả Hà Nội chính hiệu', 1),
('Cơm Gà Bà Buội', '78 Hai Bà Trưng, Quận 3, TP.HCM', '0903456789', 'comgababuoi@email.com', 'Cơm gà xối mỡ giòn rụm', 1),
('Bánh Mì Huỳnh Hoa', '26 Lê Thị Riêng, Quận 1, TP.HCM', '0904567890', 'banhmihh@email.com', 'Bánh mì nổi tiếng Sài Gòn', 1),
('Gỏi Cuốn Sài Gòn', '99 Pasteur, Quận 1, TP.HCM', '0905678901', 'goicuonsg@email.com', 'Gỏi cuốn tươi ngon mỗi ngày', 1),
('Chè Cung Đình', '56 Nguyễn Trãi, Quận 5, TP.HCM', '0906789012', 'checungdinh@email.com', 'Chè truyền thống cung đình Huế', 1);

-- Thêm món ăn mẫu (giả sử bảng monan đã có, chỉ cập nhật NhaHangID)
-- Hoặc INSERT mới nếu bảng trống

-- Phở Hà Nội (NhaHangID = 1)
INSERT INTO `monan` (`TenMon`, `NhaHangID`, `DonGia`, `MoTa`, `TrangThai`) VALUES
('Phở Bò Tái', 1, 65000, 'Phở bò tái với nước dùng ninh xương 12 tiếng', 1),
('Phở Bò Chín', 1, 65000, 'Phở bò chín mềm, thơm ngon', 1),
('Phở Gà', 1, 60000, 'Phở gà ta thả vườn', 1);

-- Bún Chả Dác Kim (NhaHangID = 2)
INSERT INTO `monan` (`TenMon`, `NhaHangID`, `DonGia`, `MoTa`, `TrangThai`) VALUES
('Bún Chả Hà Nội', 2, 70000, 'Bún chả nướng than hoa, thịt thơm mềm', 1),
('Bún Chả Nem', 2, 80000, 'Bún chả kèm nem rán giòn', 1);

-- Cơm Gà Bà Buội (NhaHangID = 3)
INSERT INTO `monan` (`TenMon`, `NhaHangID`, `DonGia`, `MoTa`, `TrangThai`) VALUES
('Cơm Gà Xối Mỡ', 3, 60000, 'Cơm gà xối mỡ giòn rụm, da vàng ươm', 1),
('Cơm Gà Luộc', 3, 55000, 'Cơm gà luộc da giòn, thịt mềm', 1);

-- Bánh Mì Huỳnh Hoa (NhaHangID = 4)
INSERT INTO `monan` (`TenMon`, `NhaHangID`, `DonGia`, `MoTa`, `TrangThai`) VALUES
('Bánh Mì Pate', 4, 25000, 'Bánh mì pate thịt nguội đặc biệt', 1),
('Bánh Mì Thịt Nướng', 4, 35000, 'Bánh mì thịt nướng than hoa', 1);

-- Gỏi Cuốn Sài Gòn (NhaHangID = 5)
INSERT INTO `monan` (`TenMon`, `NhaHangID`, `DonGia`, `MoTa`, `TrangThai`) VALUES
('Gỏi Cuốn Tôm Thịt', 5, 45000, 'Gỏi cuốn tôm thịt tươi ngon (4 cuốn)', 1),
('Gỏi Cuốn Bò', 5, 50000, 'Gỏi cuốn bò nướng lá lốt (4 cuốn)', 1);

-- Chè Cung Đình (NhaHangID = 6)
INSERT INTO `monan` (`TenMon`, `NhaHangID`, `DonGia`, `MoTa`, `TrangThai`) VALUES
('Chè Ba Màu', 6, 25000, 'Chè ba màu truyền thống', 1),
('Chè Thái', 6, 30000, 'Chè Thái nhiều topping', 1),
('Chè Đậu Xanh', 6, 20000, 'Chè đậu xanh nước cốt dừa', 1);

-- ============================================================================
-- KIỂM TRA DỮ LIỆU
-- ============================================================================
-- SELECT * FROM nhahang;
-- SELECT m.*, n.TenNhaHang, n.DiaChi FROM monan m LEFT JOIN nhahang n ON m.NhaHangID = n.NhaHangID;
