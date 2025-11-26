-- Tạo bảng users nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS khachhang (
    MaKH INT AUTO_INCREMENT PRIMARY KEY,
    TenKH VARCHAR(255) NOT NULL,
    Email VARCHAR(255) NOT NULL UNIQUE,
    MatKhau VARCHAR(255) NULL,
    SDT VARCHAR(20) NULL,
    GioiTinh ENUM('Nam', 'Nữ', 'Khác') NULL,
    DiaChi TEXT NULL,
    AnhDaiDien VARCHAR(500) NULL,
    NhaCungCap ENUM('local', 'google', 'facebook') DEFAULT 'local',
    IDNhaCungCap VARCHAR(255) NULL,
    EmailXacNhan TIMESTAMP NULL,
    NgayTao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    NgayCapNhat TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (Email),
    INDEX idx_provider (NhaCungCap)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
