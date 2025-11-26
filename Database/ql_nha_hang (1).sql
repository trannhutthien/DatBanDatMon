-- Tạo database
CREATE DATABASE IF NOT EXISTS ql_nha_hang
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Sử dụng database
USE ql_nha_hang;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- ===========================
-- BẢNG ban
-- ===========================
CREATE TABLE `ban` (
  `BanID` int(11) NOT NULL,
  `SoBan` varchar(20) NOT NULL,
  `SoGhe` int(11) NOT NULL,
  `KhuVuc` varchar(50) DEFAULT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================
-- BẢNG chitiethoadon
-- ===========================
CREATE TABLE `chitiethoadon` (
  `ChiTietID` int(11) NOT NULL,
  `HoaDonID` int(11) NOT NULL,
  `MonAnID` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `DonGia` decimal(12,2) NOT NULL,
  `ThanhTien` decimal(14,2) GENERATED ALWAYS AS (`SoLuong` * `DonGia`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================
-- BẢNG datban
-- ===========================
CREATE TABLE `datban` (
  `DatBanID` int(11) NOT NULL,
  `KhachHangID` int(11) NOT NULL,
  `BanID` int(11) NOT NULL,
  `ThoiGianDen` datetime NOT NULL,
  `SoNguoi` int(11) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================
-- BẢNG datmon
-- ===========================
CREATE TABLE `datmon` (
  `DatMonID` int(11) NOT NULL,
  `DatBanID` int(11) NOT NULL,
  `MonAnID` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL,
  `GiaLucDat` decimal(12,2) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================
-- BẢNG hoadon
-- ===========================
CREATE TABLE `hoadon` (
  `HoaDonID` int(11) NOT NULL,
  `DatBanID` int(11) NOT NULL,
  `NgayLap` datetime NOT NULL DEFAULT current_timestamp(),
  `GiamGia` decimal(12,2) NOT NULL DEFAULT 0.00,
  `Thue` decimal(5,2) NOT NULL DEFAULT 0.00,
  `TongTien` decimal(14,2) NOT NULL DEFAULT 0.00,
  `PhuongThucTT` tinyint(4) NOT NULL DEFAULT 1,
  `TrangThai` tinyint(4) NOT NULL DEFAULT 1,
  `TaoLuc` datetime NOT NULL DEFAULT current_timestamp(),
  `CapNhatLuc` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ===========================
-- BẢNG khachhang
-- =================
