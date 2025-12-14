-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3307
-- Thời gian đã tạo: Th12 14, 2025 lúc 07:00 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `ql_nha_hang`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ban`
--

CREATE TABLE `ban` (
  `BanID` int(11) NOT NULL,
  `NhaHangID` int(11) NOT NULL,
  `KhuVucID` int(11) DEFAULT NULL,
  `SoBan` int(11) NOT NULL,
  `SoGhe` int(11) NOT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitiethoadon`
--

CREATE TABLE `chitiethoadon` (
  `ChiTietID` int(11) NOT NULL,
  `HoaDonID` int(11) NOT NULL,
  `MonAnID` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(12,2) NOT NULL,
  `ThanhTien` decimal(14,2) GENERATED ALWAYS AS (`SoLuong` * `DonGia`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `datban`
--

CREATE TABLE `datban` (
  `DatBanID` int(11) NOT NULL,
  `NguoiDungID` int(11) NOT NULL,
  `NhaHangID` int(11) NOT NULL,
  `BanID` int(11) DEFAULT NULL,
  `ThoiGianDen` datetime NOT NULL,
  `SoNguoi` int(11) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

CREATE TABLE `hoadon` (
  `HoaDonID` int(11) NOT NULL,
  `DatBanID` int(11) DEFAULT NULL,
  `NguoiDungID` int(11) NOT NULL,
  `NhaHangID` int(11) NOT NULL,
  `NgayLap` datetime DEFAULT current_timestamp(),
  `TongTien` decimal(14,2) DEFAULT 0.00,
  `PhuongThucTT` varchar(50) DEFAULT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khuvuc`
--

CREATE TABLE `khuvuc` (
  `KhuVucID` int(11) NOT NULL,
  `NhaHangID` int(11) NOT NULL,
  `TenKhuVuc` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monan`
--

CREATE TABLE `monan` (
  `MonAnID` int(11) NOT NULL,
  `NhaHangID` int(11) NOT NULL,
  `TenMon` varchar(150) NOT NULL,
  `DonGia` decimal(12,2) NOT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `MoTa` text DEFAULT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `monan`
--

INSERT INTO `monan` (`MonAnID`, `NhaHangID`, `TenMon`, `DonGia`, `HinhAnh`, `MoTa`, `TrangThai`, `TaoLuc`, `CapNhatLuc`) VALUES
(1, 1, 'Cơm gà xối mỡ', 45000.00, '/images/huongque/comga.jpg', 'Cơm gà chiên giòn ăn kèm nước mắm tỏi ớt.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(2, 1, 'Canh chua cá lóc', 60000.00, '/images/huongque/canhchua.jpg', 'Canh chua miền Tây nấu cá lóc tươi.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(3, 1, 'Thịt kho trứng', 55000.00, '/images/huongque/thitkho.jpg', 'Thịt kho trứng nước dừa đậm vị quê nhà.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(4, 2, 'Phở bò tái', 50000.00, '/images/saigonxua/phobo.jpg', 'Phở bò nước dùng truyền thống.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(5, 2, 'Bún thịt nướng', 45000.00, '/images/saigonxua/bunthitnuong.jpg', 'Bún thịt nướng Sài Gòn kèm rau sống.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(6, 2, 'Hủ tiếu Nam Vang', 48000.00, '/images/saigonxua/hutieu.jpg', 'Hủ tiếu Nam Vang đầy đủ topping.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(7, 3, 'Tôm hùm nướng bơ tỏi', 350000.00, '/images/bienxanh/tomhum.jpg', 'Tôm hùm tươi nướng bơ tỏi thơm béo.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(8, 3, 'Mực hấp gừng', 180000.00, '/images/bienxanh/muc.jpg', 'Mực tươi hấp gừng chấm muối tiêu.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(9, 3, 'Lẩu hải sản', 250000.00, '/images/bienxanh/lauhaisan.jpg', 'Lẩu hải sản tổng hợp cho 3–4 người.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(10, 4, 'Cơm sườn nướng', 40000.00, '/images/comnha/comsuon.jpg', 'Cơm sườn nướng than hoa.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(11, 4, 'Cá kho tộ', 55000.00, '/images/comnha/cakho.jpg', 'Cá kho tộ đậm đà kiểu gia đình.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(12, 4, 'Rau muống xào tỏi', 30000.00, '/images/comnha/raumuong.jpg', 'Rau muống xào tỏi giòn xanh.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(13, 5, 'Bún chả Hà Nội', 50000.00, '/images/phoco/buncha.jpg', 'Bún chả truyền thống Hà Nội.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(14, 5, 'Chả cá Lã Vọng', 120000.00, '/images/phoco/chaca.jpg', 'Chả cá ăn kèm bún và mắm tôm.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31'),
(15, 5, 'Bánh cuốn nóng', 35000.00, '/images/phoco/banhcuon.jpg', 'Bánh cuốn nóng nhân thịt mộc nhĩ.', 1, '2025-12-13 00:23:31', '2025-12-13 00:23:31');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung`
--

CREATE TABLE `nguoidung` (
  `NguoiDungID` int(11) NOT NULL,
  `HoTen` varchar(150) NOT NULL,
  `Email` varchar(150) DEFAULT NULL,
  `MatKhau` varchar(255) DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `LoaiNguoiDung` tinyint(4) NOT NULL DEFAULT 3,
  `DiaChi` varchar(255) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  `CapNhatLuc` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `GoogleID` varchar(255) DEFAULT NULL,
  `Avatar` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `google_token` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nguoidung`
--

INSERT INTO `nguoidung` (`NguoiDungID`, `HoTen`, `Email`, `MatKhau`, `SDT`, `LoaiNguoiDung`, `DiaChi`, `GhiChu`, `TaoLuc`, `CapNhatLuc`, `GoogleID`, `Avatar`, `google_id`, `google_token`) VALUES
(2, 'Nguyễn Admin', 'admin@gmail.com', '123456', '0901234567', 1, 'TP. Hồ Chí Minh', 'Tài khoản quản trị', '2025-12-14 00:04:45', '2025-12-14 00:04:45', NULL, NULL, NULL, NULL),
(3, 'Trần Người Dùng', 'user@gmail.com', '123456', '0782884717', 1, 'TP. Cần Thơ', 'Tài khoản người dùng', '2025-12-14 00:04:45', '2025-12-14 05:32:14', NULL, NULL, NULL, NULL),
(4, 'nguyen hoai thuong', 'thuongnguyen@gmail.com', '123456', '0782881234', 3, NULL, NULL, '2025-12-14 12:54:09', '2025-12-14 12:54:09', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nguoidung_vaitro`
--

CREATE TABLE `nguoidung_vaitro` (
  `NguoiDungID` int(11) NOT NULL,
  `VaiTroID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhahang`
--

CREATE TABLE `nhahang` (
  `NhaHangID` int(11) NOT NULL,
  `TenNhaHang` varchar(150) NOT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `SDT` varchar(20) DEFAULT NULL,
  `TrangThai` tinyint(4) DEFAULT 1,
  `TaoLuc` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `nhahang`
--

INSERT INTO `nhahang` (`NhaHangID`, `TenNhaHang`, `DiaChi`, `SDT`, `TrangThai`, `TaoLuc`) VALUES
(1, 'Nhà hàng Hương Quê', '123 Nguyễn Trãi, Quận 5, TP.HCM', '0909123456', 1, '2025-12-13 00:23:10'),
(2, 'Nhà hàng Sài Gòn Xưa', '45 Lê Lợi, Quận 1, TP.HCM', '0912345678', 1, '2025-12-13 00:23:10'),
(3, 'Nhà hàng Biển Xanh', '89 Trần Phú, Nha Trang, Khánh Hòa', '0923456789', 1, '2025-12-13 00:23:10'),
(4, 'Nhà hàng Cơm Nhà', '12 Võ Văn Tần, Quận 3, TP.HCM', '0934567890', 1, '2025-12-13 00:23:10'),
(5, 'Nhà hàng Phố Cổ', '200 Trần Hưng Đạo, Hà Nội', '0945678901', 1, '2025-12-13 00:23:10');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\NguoiDung', 1, 'google_auth_token', '591ed5d757e70b8fcf07f4a56592f2a4015c378388be201bb7d3a7c13015308a', '[\"*\"]', NULL, NULL, '2025-12-09 23:21:16', '2025-12-09 23:21:16'),
(2, 'App\\Models\\NguoiDung', 2, 'auth_token', '530610c9fee4a0b8af9dca6bb12204993d733bf5a78060b1ffa6ad59ad56729c', '[\"*\"]', NULL, NULL, '2025-12-13 10:18:43', '2025-12-13 10:18:43'),
(3, 'App\\Models\\NguoiDung', 1, 'google_auth_token', '52c87c78eb9099bd9fcc2c4de9b300cd80233ce4a27973d890c9690061dbc3e7', '[\"*\"]', NULL, NULL, '2025-12-13 10:35:48', '2025-12-13 10:35:48'),
(4, 'App\\Models\\NguoiDung', 1, 'google_auth_token', '66c08c5349931a447c8db51029b3bf8bf8e462883bfe5943555c196f2ab6cc95', '[\"*\"]', NULL, NULL, '2025-12-13 10:43:22', '2025-12-13 10:43:22'),
(5, 'App\\Models\\NguoiDung', 2, 'auth_token', '530bfa54f699d641d3fa33510d8bb6a58449e667977b5fb677f3ddb9453989a5', '[\"*\"]', NULL, NULL, '2025-12-13 10:59:49', '2025-12-13 10:59:49'),
(6, 'App\\Models\\NguoiDung', 1, 'google_auth_token', '042528b87c0915478b4c05b21b54c3107d383935920ea798cf7f1fe06e3e6075', '[\"*\"]', NULL, NULL, '2025-12-13 11:07:53', '2025-12-13 11:07:53'),
(7, 'App\\Models\\NguoiDung', 1, 'google_auth_token', '4838109c97694743e88d7ba9e0d632a2a7506e69533b8c22cdcb337ec78d337d', '[\"*\"]', NULL, NULL, '2025-12-13 11:08:10', '2025-12-13 11:08:10'),
(8, 'App\\Models\\NguoiDung', 1, 'google_auth_token', '51968b4972d6b1c8f79b97d84881c8d9b8fb66ca73af648e73d13f5dd5089d41', '[\"*\"]', NULL, NULL, '2025-12-13 11:12:51', '2025-12-13 11:12:51'),
(9, 'App\\Models\\NguoiDung', 1, 'google_auth_token', '1997e0febe029a970c1f9e4f1ddc550d33b79c662c791829c4fe8c1d59b207e2', '[\"*\"]', NULL, NULL, '2025-12-13 11:16:15', '2025-12-13 11:16:15'),
(10, 'App\\Models\\NguoiDung', 2, 'auth_token', '5f4e2c9618376c366341c49995df68a8feac14709a5d72ad8a286517d7565403', '[\"*\"]', NULL, NULL, '2025-12-13 11:18:41', '2025-12-13 11:18:41'),
(11, 'App\\Models\\NguoiDung', 3, 'auth_token', 'ee474fe5ac6f28691e2454c87896cb22556a87d4da23d2237b3883af31c55c38', '[\"*\"]', NULL, NULL, '2025-12-13 22:33:07', '2025-12-13 22:33:07');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

CREATE TABLE `vaitro` (
  `VaiTroID` int(11) NOT NULL,
  `MaVaiTro` varchar(50) NOT NULL,
  `TenVaiTro` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `vaitro`
--

INSERT INTO `vaitro` (`VaiTroID`, `MaVaiTro`, `TenVaiTro`, `MoTa`) VALUES
(1, 'ADMIN', 'Quản trị viên', 'Toàn quyền quản lý hệ thống'),
(2, 'USER', 'Người dùng', 'Người dùng thông thường');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `ban`
--
ALTER TABLE `ban`
  ADD PRIMARY KEY (`BanID`),
  ADD KEY `NhaHangID` (`NhaHangID`),
  ADD KEY `KhuVucID` (`KhuVucID`);

--
-- Chỉ mục cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD PRIMARY KEY (`ChiTietID`),
  ADD KEY `HoaDonID` (`HoaDonID`),
  ADD KEY `MonAnID` (`MonAnID`);

--
-- Chỉ mục cho bảng `datban`
--
ALTER TABLE `datban`
  ADD PRIMARY KEY (`DatBanID`),
  ADD KEY `NguoiDungID` (`NguoiDungID`),
  ADD KEY `NhaHangID` (`NhaHangID`),
  ADD KEY `BanID` (`BanID`);

--
-- Chỉ mục cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`HoaDonID`),
  ADD KEY `DatBanID` (`DatBanID`),
  ADD KEY `NguoiDungID` (`NguoiDungID`),
  ADD KEY `NhaHangID` (`NhaHangID`);

--
-- Chỉ mục cho bảng `khuvuc`
--
ALTER TABLE `khuvuc`
  ADD PRIMARY KEY (`KhuVucID`),
  ADD KEY `NhaHangID` (`NhaHangID`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `monan`
--
ALTER TABLE `monan`
  ADD PRIMARY KEY (`MonAnID`),
  ADD KEY `NhaHangID` (`NhaHangID`);

--
-- Chỉ mục cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`NguoiDungID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Chỉ mục cho bảng `nguoidung_vaitro`
--
ALTER TABLE `nguoidung_vaitro`
  ADD PRIMARY KEY (`NguoiDungID`,`VaiTroID`),
  ADD KEY `VaiTroID` (`VaiTroID`);

--
-- Chỉ mục cho bảng `nhahang`
--
ALTER TABLE `nhahang`
  ADD PRIMARY KEY (`NhaHangID`);

--
-- Chỉ mục cho bảng `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  ADD PRIMARY KEY (`VaiTroID`),
  ADD UNIQUE KEY `MaVaiTro` (`MaVaiTro`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `ban`
--
ALTER TABLE `ban`
  MODIFY `BanID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  MODIFY `ChiTietID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `datban`
--
ALTER TABLE `datban`
  MODIFY `DatBanID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `HoaDonID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khuvuc`
--
ALTER TABLE `khuvuc`
  MODIFY `KhuVucID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `monan`
--
ALTER TABLE `monan`
  MODIFY `MonAnID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `nguoidung`
--
ALTER TABLE `nguoidung`
  MODIFY `NguoiDungID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `nhahang`
--
ALTER TABLE `nhahang`
  MODIFY `NhaHangID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  MODIFY `VaiTroID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `ban`
--
ALTER TABLE `ban`
  ADD CONSTRAINT `ban_ibfk_1` FOREIGN KEY (`NhaHangID`) REFERENCES `nhahang` (`NhaHangID`) ON DELETE CASCADE,
  ADD CONSTRAINT `ban_ibfk_2` FOREIGN KEY (`KhuVucID`) REFERENCES `khuvuc` (`KhuVucID`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD CONSTRAINT `chitiethoadon_ibfk_1` FOREIGN KEY (`HoaDonID`) REFERENCES `hoadon` (`HoaDonID`) ON DELETE CASCADE,
  ADD CONSTRAINT `chitiethoadon_ibfk_2` FOREIGN KEY (`MonAnID`) REFERENCES `monan` (`MonAnID`);

--
-- Các ràng buộc cho bảng `datban`
--
ALTER TABLE `datban`
  ADD CONSTRAINT `datban_ibfk_1` FOREIGN KEY (`NguoiDungID`) REFERENCES `nguoidung` (`NguoiDungID`) ON DELETE CASCADE,
  ADD CONSTRAINT `datban_ibfk_2` FOREIGN KEY (`NhaHangID`) REFERENCES `nhahang` (`NhaHangID`) ON DELETE CASCADE,
  ADD CONSTRAINT `datban_ibfk_3` FOREIGN KEY (`BanID`) REFERENCES `ban` (`BanID`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadon_ibfk_1` FOREIGN KEY (`DatBanID`) REFERENCES `datban` (`DatBanID`) ON DELETE SET NULL,
  ADD CONSTRAINT `hoadon_ibfk_2` FOREIGN KEY (`NguoiDungID`) REFERENCES `nguoidung` (`NguoiDungID`),
  ADD CONSTRAINT `hoadon_ibfk_3` FOREIGN KEY (`NhaHangID`) REFERENCES `nhahang` (`NhaHangID`);

--
-- Các ràng buộc cho bảng `khuvuc`
--
ALTER TABLE `khuvuc`
  ADD CONSTRAINT `khuvuc_ibfk_1` FOREIGN KEY (`NhaHangID`) REFERENCES `nhahang` (`NhaHangID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `monan`
--
ALTER TABLE `monan`
  ADD CONSTRAINT `monan_ibfk_1` FOREIGN KEY (`NhaHangID`) REFERENCES `nhahang` (`NhaHangID`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `nguoidung_vaitro`
--
ALTER TABLE `nguoidung_vaitro`
  ADD CONSTRAINT `nguoidung_vaitro_ibfk_1` FOREIGN KEY (`NguoiDungID`) REFERENCES `nguoidung` (`NguoiDungID`) ON DELETE CASCADE,
  ADD CONSTRAINT `nguoidung_vaitro_ibfk_2` FOREIGN KEY (`VaiTroID`) REFERENCES `vaitro` (`VaiTroID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
