-- Tạo bảng danh mục món ăn
CREATE TABLE IF NOT EXISTS `danhmucmon` (
  `DanhMucID` int(11) NOT NULL AUTO_INCREMENT,
  `TenDanhMuc` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL,
  `ThuTu` int(11) DEFAULT 0,
  `TrangThai` tinyint(4) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`DanhMucID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm dữ liệu mẫu cho danh mục món
INSERT INTO `danhmucmon` (`TenDanhMuc`, `MoTa`, `ThuTu`, `TrangThai`) VALUES
('Phở', 'Các loại phở truyền thống', 1, 1),
('Bún', 'Các món bún đa dạng', 2, 1),
('Cơm', 'Cơm phần, cơm đĩa', 3, 1),
('Bánh', 'Bánh mì, bánh cuốn, bánh xèo...', 4, 1),
('Lẩu', 'Các loại lẩu nóng', 5, 1),
('Hải sản', 'Tôm, cua, cá, mực...', 6, 1),
('Món nướng', 'Thịt nướng, hải sản nướng', 7, 1),
('Đồ uống', 'Nước ngọt, trà, cà phê...', 8, 1),
('Tráng miệng', 'Chè, kem, trái cây', 9, 1);

-- Thêm cột DanhMucID vào bảng monan nếu chưa có
ALTER TABLE `monan` ADD COLUMN `DanhMucID` int(11) DEFAULT NULL;

-- Cập nhật DanhMucID cho các món ăn hiện có
UPDATE `monan` SET `DanhMucID` = 1 WHERE LOWER(`TenMon`) LIKE '%phở%';
UPDATE `monan` SET `DanhMucID` = 2 WHERE LOWER(`TenMon`) LIKE '%bún%';
UPDATE `monan` SET `DanhMucID` = 3 WHERE LOWER(`TenMon`) LIKE '%cơm%';
UPDATE `monan` SET `DanhMucID` = 4 WHERE LOWER(`TenMon`) LIKE '%bánh%';
UPDATE `monan` SET `DanhMucID` = 5 WHERE LOWER(`TenMon`) LIKE '%lẩu%';
UPDATE `monan` SET `DanhMucID` = 6 WHERE LOWER(`TenMon`) LIKE '%tôm%' OR LOWER(`TenMon`) LIKE '%cá %' OR LOWER(`TenMon`) LIKE '%mực%';
UPDATE `monan` SET `DanhMucID` = 7 WHERE LOWER(`TenMon`) LIKE '%nướng%';
UPDATE `monan` SET `DanhMucID` = 8 WHERE LOWER(`TenMon`) LIKE '%trà%' OR LOWER(`TenMon`) LIKE '%cà phê%' OR LOWER(`TenMon`) LIKE '%nước%';
UPDATE `monan` SET `DanhMucID` = 9 WHERE LOWER(`TenMon`) LIKE '%chè%' OR LOWER(`TenMon`) LIKE '%kem%';
