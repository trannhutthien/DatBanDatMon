-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 19, 2025 lúc 09:54 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

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
  `SoBan` varchar(20) NOT NULL,
  `SoGhe` int(11) NOT NULL,
  `KhuVuc` varchar(50) DEFAULT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `chitiethoadon`
--

CREATE TABLE `chitiethoadon` (
  `ChiTietID` int(11) NOT NULL,
  `HoaDonID` int(11) NOT NULL,
  `MonAnID` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL CHECK (`SoLuong` > 0),
  `DonGia` decimal(12,2) NOT NULL CHECK (`DonGia` >= 0),
  `ThanhTien` decimal(14,2) GENERATED ALWAYS AS (`SoLuong` * `DonGia`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `datban`
--

CREATE TABLE `datban` (
  `DatBanID` int(11) NOT NULL,
  `KhachHangID` int(11) NOT NULL,
  `BanID` int(11) NOT NULL,
  `ThoiGianDen` datetime NOT NULL,
  `SoNguoi` int(11) NOT NULL CHECK (`SoNguoi` > 0),
  `GhiChu` varchar(255) DEFAULT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `datmon`
--

CREATE TABLE `datmon` (
  `DatMonID` int(11) NOT NULL,
  `DatBanID` int(11) NOT NULL,
  `MonAnID` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL CHECK (`SoLuong` > 0),
  `GiaLucDat` decimal(12,2) NOT NULL CHECK (`GiaLucDat` >= 0),
  `GhiChu` varchar(255) DEFAULT NULL,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `hoadon`
--

CREATE TABLE `hoadon` (
  `HoaDonID` int(11) NOT NULL,
  `DatBanID` int(11) NOT NULL,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `GiamGia` decimal(12,2) NOT NULL DEFAULT 0.00 CHECK (`GiamGia` >= 0),
  `Thue` decimal(5,2) NOT NULL DEFAULT 0.00 CHECK (`Thue` >= 0),
  `TongTien` decimal(14,2) NOT NULL DEFAULT 0.00 CHECK (`TongTien` >= 0),
  `PhuongThucTT` tinyint(4) NOT NULL DEFAULT 1,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `khachhang`
--

CREATE TABLE `khachhang` (
  `KhachHangID` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `DiaChi` varchar(255) DEFAULT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `LoaiKhach` tinyint(4) DEFAULT NULL,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `loaimon`
--

CREATE TABLE `loaimon` (
  `LoaiMonID` int(11) NOT NULL,
  `TenLoai` varchar(100) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `monan`
--

CREATE TABLE `monan` (
  `MonAnID` int(11) NOT NULL,
  `TenMon` varchar(150) NOT NULL,
  `Gia` decimal(12,2) NOT NULL CHECK (`Gia` >= 0),
  `DonViTinh` varchar(20) NOT NULL,
  `HinhAnh` varchar(255) DEFAULT NULL,
  `MoTa` text DEFAULT NULL,
  `LoaiMonID` int(11) DEFAULT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `NgayThem` datetime NOT NULL DEFAULT current_timestamp(),
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `nhanvien`
--

CREATE TABLE `nhanvien` (
  `NhanVienID` int(11) NOT NULL,
  `HoTen` varchar(100) NOT NULL,
  `SoDienThoai` varchar(20) DEFAULT NULL,
  `Email` varchar(120) DEFAULT NULL,
  `TenDangNhap` varchar(50) NOT NULL,
  `MatKhauMaHoa` varchar(255) NOT NULL,
  `VaiTroID` int(11) NOT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `NgayVaoLam` date DEFAULT NULL,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `phucvu`
--

CREATE TABLE `phucvu` (
  `PhucVuID` int(11) NOT NULL,
  `NhanVienID` int(11) NOT NULL,
  `BanID` int(11) NOT NULL,
  `ThoiGianBatDau` datetime NOT NULL,
  `ThoiGianKetThuc` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `vaitro`
--

CREATE TABLE `vaitro` (
  `VaiTroID` int(11) NOT NULL,
  `TenVaiTro` varchar(50) NOT NULL,
  `MoTa` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `ban`
--
ALTER TABLE `ban`
  ADD PRIMARY KEY (`BanID`),
  ADD UNIQUE KEY `SoBan` (`SoBan`);

--
-- Chỉ mục cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD PRIMARY KEY (`ChiTietID`),
  ADD KEY `IX_CTHD_HoaDon` (`HoaDonID`),
  ADD KEY `IX_CTHD_MonAn` (`MonAnID`);

--
-- Chỉ mục cho bảng `datban`
--
ALTER TABLE `datban`
  ADD PRIMARY KEY (`DatBanID`),
  ADD KEY `IX_DatBan_ThoiGianDen` (`ThoiGianDen`),
  ADD KEY `IX_DatBan_KhachHang` (`KhachHangID`),
  ADD KEY `IX_DatBan_Ban` (`BanID`);

--
-- Chỉ mục cho bảng `datmon`
--
ALTER TABLE `datmon`
  ADD PRIMARY KEY (`DatMonID`),
  ADD KEY `IX_DatMon_DatBan` (`DatBanID`),
  ADD KEY `IX_DatMon_MonAn` (`MonAnID`);

--
-- Chỉ mục cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`HoaDonID`),
  ADD UNIQUE KEY `DatBanID` (`DatBanID`);

--
-- Chỉ mục cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  ADD PRIMARY KEY (`KhachHangID`),
  ADD KEY `IX_KhachHang_SDT` (`SoDienThoai`),
  ADD KEY `IX_KhachHang_Email` (`Email`);

--
-- Chỉ mục cho bảng `loaimon`
--
ALTER TABLE `loaimon`
  ADD PRIMARY KEY (`LoaiMonID`),
  ADD UNIQUE KEY `TenLoai` (`TenLoai`);

--
-- Chỉ mục cho bảng `monan`
--
ALTER TABLE `monan`
  ADD PRIMARY KEY (`MonAnID`),
  ADD UNIQUE KEY `TenMon` (`TenMon`),
  ADD KEY `IX_MonAn_LoaiMon` (`LoaiMonID`);

--
-- Chỉ mục cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD PRIMARY KEY (`NhanVienID`),
  ADD UNIQUE KEY `TenDangNhap` (`TenDangNhap`),
  ADD KEY `FK_NhanVien_VaiTro` (`VaiTroID`);

--
-- Chỉ mục cho bảng `phucvu`
--
ALTER TABLE `phucvu`
  ADD PRIMARY KEY (`PhucVuID`),
  ADD KEY `IX_PV_NhanVien` (`NhanVienID`),
  ADD KEY `IX_PV_Ban` (`BanID`);

--
-- Chỉ mục cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  ADD PRIMARY KEY (`VaiTroID`),
  ADD UNIQUE KEY `TenVaiTro` (`TenVaiTro`);

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
-- AUTO_INCREMENT cho bảng `datmon`
--
ALTER TABLE `datmon`
  MODIFY `DatMonID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  MODIFY `HoaDonID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `khachhang`
--
ALTER TABLE `khachhang`
  MODIFY `KhachHangID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `loaimon`
--
ALTER TABLE `loaimon`
  MODIFY `LoaiMonID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `monan`
--
ALTER TABLE `monan`
  MODIFY `MonAnID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  MODIFY `NhanVienID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `phucvu`
--
ALTER TABLE `phucvu`
  MODIFY `PhucVuID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `vaitro`
--
ALTER TABLE `vaitro`
  MODIFY `VaiTroID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `chitiethoadon`
--
ALTER TABLE `chitiethoadon`
  ADD CONSTRAINT `FK_CTHD_HoaDon` FOREIGN KEY (`HoaDonID`) REFERENCES `hoadon` (`HoaDonID`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_CTHD_MonAn` FOREIGN KEY (`MonAnID`) REFERENCES `monan` (`MonAnID`);

--
-- Các ràng buộc cho bảng `datban`
--
ALTER TABLE `datban`
  ADD CONSTRAINT `FK_DatBan_Ban` FOREIGN KEY (`BanID`) REFERENCES `ban` (`BanID`),
  ADD CONSTRAINT `FK_DatBan_KhachHang` FOREIGN KEY (`KhachHangID`) REFERENCES `khachhang` (`KhachHangID`);

--
-- Các ràng buộc cho bảng `datmon`
--
ALTER TABLE `datmon`
  ADD CONSTRAINT `FK_DatMon_DatBan` FOREIGN KEY (`DatBanID`) REFERENCES `datban` (`DatBanID`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_DatMon_MonAn` FOREIGN KEY (`MonAnID`) REFERENCES `monan` (`MonAnID`);

--
-- Các ràng buộc cho bảng `hoadon`
--
ALTER TABLE `hoadon`
  ADD CONSTRAINT `FK_HoaDon_DatBan` FOREIGN KEY (`DatBanID`) REFERENCES `datban` (`DatBanID`);

--
-- Các ràng buộc cho bảng `monan`
--
ALTER TABLE `monan`
  ADD CONSTRAINT `FK_MonAn_LoaiMon` FOREIGN KEY (`LoaiMonID`) REFERENCES `loaimon` (`LoaiMonID`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `nhanvien`
--
ALTER TABLE `nhanvien`
  ADD CONSTRAINT `FK_NhanVien_VaiTro` FOREIGN KEY (`VaiTroID`) REFERENCES `vaitro` (`VaiTroID`);

--
-- Các ràng buộc cho bảng `phucvu`
--
ALTER TABLE `phucvu`
  ADD CONSTRAINT `FK_PV_Ban` FOREIGN KEY (`BanID`) REFERENCES `ban` (`BanID`),
  ADD CONSTRAINT `FK_PV_NhanVien` FOREIGN KEY (`NhanVienID`) REFERENCES `nhanvien` (`NhanVienID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
