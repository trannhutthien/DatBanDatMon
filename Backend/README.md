# Restaurant Backend API

Backend API cho hệ thống đặt bàn đặt món ăn

## Cấu trúc thư mục

```
Backend/
├── config/           # Configuration files
├── public/           # Public directory (index.php)
├── routes/           # Route definitions
├── src/
│   ├── Core/        # Core classes (Router, Database, etc.)
│   ├── Controllers/ # Controllers
│   ├── Models/      # Models
│   └── Middleware/  # Middleware
└── vendor/          # Composer dependencies
```

## Yêu cầu

- PHP >= 8.0
- MySQL/MariaDB
- Composer (optional)

## Cài đặt

1. Cấu hình database trong `config/database.php`
2. Import database schema
3. Chạy server: `php -S localhost:8000 -t public`

## API Endpoints

- GET `/api/health` - Health check
- GET `/api/test` - Test route
