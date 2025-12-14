<?php
/**
 * ============================================================================
 * SERVICES CONFIGURATION - CẤU HÌNH DỊCH VỤ BÊN NGOÀI
 * ============================================================================
 * 
 * File này lưu credentials cho các dịch vụ third-party:
 * - Email services (Mailgun, Postmark, SES)
 * - OAuth providers (Google, Facebook, GitHub)
 * - Cloud services (AWS)
 * 
 * TẤT CẢ CREDENTIALS NÊN LƯU TRONG .env, KHÔNG HARDCODE Ở ĐÂY!
 * 
 * CÁCH HOẠT ĐỘNG:
 * - env('KEY') đọc giá trị từ file .env
 * - Nếu không có trong .env, dùng giá trị mặc định (tham số thứ 2)
 * ============================================================================
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    /**
     * MAILGUN - Dịch vụ gửi email
     * 
     * Dùng khi MAIL_MAILER=mailgun trong .env
     * 
     * Cần đăng ký tại: https://www.mailgun.com/
     * 
     * .env cần có:
     * MAILGUN_DOMAIN=mg.yourdomain.com
     * MAILGUN_SECRET=key-xxxxxxxxxxxxxxxx
     */
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    /**
     * POSTMARK - Dịch vụ gửi email
     * 
     * Dùng khi MAIL_MAILER=postmark trong .env
     * 
     * Cần đăng ký tại: https://postmarkapp.com/
     * 
     * .env cần có:
     * POSTMARK_TOKEN=xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
     */
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    /**
     * AWS SES - Amazon Simple Email Service
     * 
     * Dùng khi MAIL_MAILER=ses trong .env
     * 
     * Cần tạo IAM user với quyền SES
     * 
     * .env cần có:
     * AWS_ACCESS_KEY_ID=AKIAXXXXXXXXXXXXXXXX
     * AWS_SECRET_ACCESS_KEY=xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
     * AWS_DEFAULT_REGION=us-east-1
     */
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /**
     * ========================================================================
     * GOOGLE OAUTH - ĐĂNG NHẬP BẰNG GOOGLE
     * ========================================================================
     * 
     * Dùng bởi Laravel Socialite để xác thực với Google.
     * 
     * CÁCH TẠO CREDENTIALS:
     * 1. Vào Google Cloud Console: https://console.cloud.google.com/
     * 2. Tạo project mới hoặc chọn project có sẵn
     * 3. Vào APIs & Services > Credentials
     * 4. Create Credentials > OAuth client ID
     * 5. Application type: Web application
     * 6. Thêm Authorized redirect URIs:
     *    - http://localhost:8000/api/auth/google/callback (development)
     *    - https://yourdomain.com/api/auth/google/callback (production)
     * 7. Copy Client ID và Client Secret
     * 
     * .env CẦN CÓ:
     * GOOGLE_CLIENT_ID=xxxxx.apps.googleusercontent.com
     * GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxxxxx
     * GOOGLE_REDIRECT_URI=http://localhost:8000/api/auth/google/callback
     * 
     * SỬ DỤNG TRONG CODE:
     * - Socialite::driver('google') đọc config từ đây
     * - AuthController dùng cho đăng nhập Google
     */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),           // Google Client ID
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),   // Google Client Secret
        'redirect' => env('GOOGLE_REDIRECT_URI'),         // Callback URL sau khi đăng nhập
    ],

];
