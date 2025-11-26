-- Thêm cột MatKhau vào bảng khachhang
ALTER TABLE khachhang 
ADD COLUMN MatKhau VARCHAR(255) NULL AFTER Email;

-- Thêm cột LoaiKH nếu chưa có (để phân biệt đăng ký local/google)
ALTER TABLE khachhang 
ADD COLUMN LoaiKH VARCHAR(20) DEFAULT 'local' AFTER MatKhau;
