# Hướng dẫn cài đặt Google Login

## Bước 1: Tạo Google OAuth Client ID

1. Truy cập [Google Cloud Console](https://console.cloud.google.com/)
2. Tạo project mới hoặc chọn project có sẵn
3. Vào **APIs & Services** → **Credentials**
4. Click **Create Credentials** → **OAuth client ID**
5. Chọn **Web application**
6. Thêm **Authorized JavaScript origins**:
   - `http://localhost:5173`
   - `http://localhost:3000`
7. Thêm **Authorized redirect URIs**:
   - `http://localhost:5173`
   - `http://localhost:8000/api/auth/google/callback`
8. Copy **Client ID** và dán vào file `.env`

## Bước 2: Cấu hình Frontend

Mở file `.env` và thêm:
```
VITE_GOOGLE_CLIENT_ID=your-google-client-id-here
```

## Bước 3: Import Database

```bash
mysql -u root -p < database/schema.sql
```

Hoặc import qua phpMyAdmin

## Bước 4: Cập nhật config database

Mở file `config/database.php` và cập nhật thông tin database của bạn

## Bước 5: Chạy Backend

```bash
cd Backend
php -S localhost:8000 -t public
```

## Bước 6: Chạy Frontend

```bash
cd Frontend
npm run dev
```

## Flow hoạt động

1. User click nút "Đăng nhập Google"
2. Vue gọi `googleTokenLogin()` để lấy access token từ Google
3. Vue gửi access token lên backend `/api/auth/google`
4. Backend verify token với Google API
5. Backend tạo/cập nhật user trong database
6. Backend tạo JWT token
7. Backend trả token về Vue
8. Vue lưu token vào localStorage
9. User đăng nhập thành công

## Test API

### Register
```bash
POST http://localhost:8000/api/auth/register
Content-Type: application/json

{
  "full_name": "Test User",
  "email": "test@example.com",
  "password": "123456",
  "phone": "0123456789"
}
```

### Login
```bash
POST http://localhost:8000/api/auth/login
Content-Type: application/json

{
  "email": "test@example.com",
  "password": "123456"
}
```
