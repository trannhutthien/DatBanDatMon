<?php
/**
 * Application Configuration
 */
return [
    'app_name' => 'Restaurant API',
    'app_env' => 'development', // production, development
    'app_debug' => true,
    'app_url' => 'http://localhost',
    'api_version' => 'v1',
    
    // CORS Settings
    'cors' => [
        'allowed_origins' => ['http://localhost:5173', 'http://localhost:3000'],
        'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
        'allowed_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        'expose_headers' => [],
        'max_age' => 3600,
        'supports_credentials' => true,
    ],
    
    // JWT Settings
    'jwt' => [
        'secret_key' => 'your-secret-key-here-change-in-production',
        'algorithm' => 'HS256',
        'expiration' => 3600 * 24, // 24 hours
    ],
    
    // Upload Settings
    'upload' => [
        'max_size' => 5 * 1024 * 1024, // 5MB
        'allowed_types' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
        'upload_path' => __DIR__ . '/../public/uploads/',
    ],
];
