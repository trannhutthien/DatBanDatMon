-- Thêm dữ liệu mẫu cho bảng khuvuc
INSERT INTO `khuvuc` (`NhaHangID`, `TenKhuVuc`, `MoTa`) VALUES
(1, 'Tầng 1', 'Khu vực tầng trệt'),
(1, 'Tầng 2', 'Khu vực tầng lầu'),
(1, 'Sân vườn', 'Khu vực ngoài trời'),
(2, 'Trong nhà', 'Khu vực có máy lạnh'),
(2, 'Ngoài trời', 'Khu vực ban công'),
(3, 'VIP', 'Phòng riêng cao cấp'),
(3, 'Thường', 'Khu vực chung');

-- Thêm dữ liệu mẫu cho bảng ban
-- Nhà hàng 1: Hương Quê
INSERT INTO `ban` (`NhaHangID`, `KhuVucID`, `SoBan`, `SoGhe`, `TrangThai`) VALUES
(1, 1, 1, 4, 1),
(1, 1, 2, 4, 1),
(1, 1, 3, 6, 2),
(1, 2, 4, 4, 1),
(1, 2, 5, 8, 3),
(1, 2, 6, 4, 1),
(1, 3, 7, 6, 1),
(1, 3, 8, 4, 2),
(1, 3, 9, 4, 1),
(1, 3, 10, 10, 1);

-- Nhà hàng 2: Sài Gòn Xưa
INSERT INTO `ban` (`NhaHangID`, `KhuVucID`, `SoBan`, `SoGhe`, `TrangThai`) VALUES
(2, 4, 1, 4, 1),
(2, 4, 2, 4, 3),
(2, 4, 3, 6, 1),
(2, 4, 4, 4, 1),
(2, 5, 5, 8, 1),
(2, 5, 6, 4, 2),
(2, 5, 7, 6, 1),
(2, 5, 8, 4, 1);

-- Nhà hàng 3: Biển Xanh
INSERT INTO `ban` (`NhaHangID`, `KhuVucID`, `SoBan`, `SoGhe`, `TrangThai`) VALUES
(3, 6, 1, 4, 1),
(3, 6, 2, 6, 1),
(3, 7, 3, 4, 2),
(3, 7, 4, 8, 1),
(3, 7, 5, 4, 3),
(3, 7, 6, 6, 1);
