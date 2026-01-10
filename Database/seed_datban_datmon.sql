-- ============================================================================
-- BẢNG DATBAN_DATMON - CHI TIẾT MÓN ĂN KHI ĐẶT BÀN
-- ============================================================================
-- Chạy file này trong phpMyAdmin hoặc MySQL để tạo bảng

-- Xóa bảng cũ nếu tồn tại (cẩn thận khi chạy trên production)
DROP TABLE IF EXISTS `datban_datmon`;

-- Tạo bảng datban_datmon
CREATE TABLE `datban_datmon` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DatBanID` int(11) NOT NULL,
  `MonAnID` int(11) NOT NULL,
  `SoLuong` int(11) NOT NULL DEFAULT 1,
  `DonGia` decimal(12,2) NOT NULL,
  `GhiChu` varchar(255) DEFAULT NULL,
  `TaoLuc` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `DatBanID` (`DatBanID`),
  KEY `MonAnID` (`MonAnID`),
  CONSTRAINT `datban_datmon_datban_fk` FOREIGN KEY (`DatBanID`) REFERENCES `datban` (`DatBanID`) ON DELETE CASCADE,
  CONSTRAINT `datban_datmon_monan_fk` FOREIGN KEY (`MonAnID`) REFERENCES `monan` (`MonAnID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thông báo hoàn thành
SELECT 'Bảng datban_datmon đã được tạo thành công!' AS 'Thông báo';
