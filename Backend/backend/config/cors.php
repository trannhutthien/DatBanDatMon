<?php
/**
 * ============================================================================
 * CORS CONFIGURATION - CẤU HÌNH CROSS-ORIGIN RESOURCE SHARING
 * ============================================================================
 * 
 * CORS là gì?
 * - Cơ chế bảo mật của trình duyệt ngăn chặn request từ domain khác
 * - Ví dụ: Frontend (localhost:5173) gọi API Backend (localhost:8000)
 * - Nếu không cấu hình CORS, trình duyệt sẽ block request
 * 
 * File này cho phép frontend gọi API từ các domain được chỉ định.
 * 
 * LỖI THƯỜNG GẶP:
 * - "Access to XMLHttpRequest has been blocked by CORS policy"
 * - Nguyên nhân: Domain frontend không nằm trong allowed_origins
 * - Giải pháp: Thêm domain vào allowed_origins
 * ============================================================================
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    /**
     * PATHS - Đường dẫn áp dụng CORS
     * 
     * Chỉ các URL khớp với pattern này mới được CORS
     * - 'api/*' = Tất cả routes bắt đầu bằng /api
     * - 'sanctum/csrf-cookie' = Route lấy CSRF token cho SPA
     */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /**
     * ALLOWED METHODS - Các HTTP method được phép
     * 
     * '*' = Cho phép tất cả methods (GET, POST, PUT, DELETE, PATCH, OPTIONS)
     * Hoặc chỉ định cụ thể: ['GET', 'POST']
     */
    'allowed_methods' => ['*'],

    /**
     * ALLOWED ORIGINS - Các domain được phép gọi API
     * 
     * ĐÂY LÀ CẤU HÌNH QUAN TRỌNG NHẤT!
     * 
     * Chỉ các domain trong list này mới được gọi API từ trình duyệt.
     * 
     * HIỆN TẠI CHO PHÉP:
     * - http://localhost:5173  = Vite dev server (Vue/React)
     * - http://localhost:3000  = Create React App / Next.js
     * - http://127.0.0.1:5173  = Vite với IP thay vì localhost
     * 
     * KHI DEPLOY PRODUCTION:
     * Thêm domain thật vào đây, ví dụ:
     * - 'https://myapp.com'
     * - 'https://www.myapp.com'
     */
    'allowed_origins' => ['http://localhost:5173', 'http://localhost:3000', 'http://127.0.0.1:5173'],

    /**
     * ALLOWED ORIGINS PATTERNS - Pattern regex cho origins
     * 
     * Dùng khi có nhiều subdomain, ví dụ:
     * ['https://*.myapp.com'] = Cho phép tất cả subdomain
     */
    'allowed_origins_patterns' => [],

    /**
     * ALLOWED HEADERS - Các header được phép gửi
     * 
     * '*' = Cho phép tất cả headers
     * Bao gồm: Content-Type, Authorization, X-Requested-With, etc.
     */
    'allowed_headers' => ['*'],

    /**
     * EXPOSED HEADERS - Headers mà frontend có thể đọc
     * 
     * Mặc định trình duyệt chỉ cho đọc một số headers cơ bản.
     * Thêm vào đây nếu cần expose thêm headers.
     */
    'exposed_headers' => [],

    /**
     * MAX AGE - Thời gian cache preflight request (giây)
     * 
     * Preflight = Request OPTIONS trước request thật
     * 0 = Không cache, luôn gửi preflight
     * 86400 = Cache 24 giờ
     */
    'max_age' => 0,

    /**
     * SUPPORTS CREDENTIALS - Cho phép gửi cookies/credentials
     * 
     * true = Cho phép gửi cookies, Authorization header
     * QUAN TRỌNG cho authentication!
     * 
     * Nếu true, frontend phải set:
     * - axios: withCredentials: true
     * - fetch: credentials: 'include'
     */
    'supports_credentials' => true,

];
