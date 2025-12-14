<?php
/**
 * ============================================================================
 * AUTH CONFIGURATION - CẤU HÌNH XÁC THỰC NGƯỜI DÙNG
 * ============================================================================
 * 
 * File này cấu hình hệ thống authentication của Laravel:
 * - Guards: Cách xác thực user (session, token, etc.)
 * - Providers: Nguồn dữ liệu user (database, eloquent model)
 * - Passwords: Cấu hình reset mật khẩu
 * 
 * QUAN TRỌNG:
 * - Provider 'users' đã được đổi sang dùng Model NguoiDung
 * - Thay vì Model User mặc định của Laravel
 * ============================================================================
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication "guard" and password
    | reset options for your application. You may change these defaults
    | as required, but they're a perfect start for most applications.
    |
    */

    /**
     * DEFAULTS - Cấu hình mặc định
     * 
     * guard: 'web' = Sử dụng guard 'web' làm mặc định
     * passwords: 'users' = Sử dụng config 'users' cho reset password
     */
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Next, you may define every authentication guard for your application.
    | Of course, a great default configuration has been defined for you
    | here which uses session storage and the Eloquent user provider.
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | Supported: "session"
    |
    */

    /**
     * GUARDS - Các phương thức xác thực
     * 
     * Guard 'web':
     * - driver: 'session' = Lưu trạng thái đăng nhập trong session
     * - provider: 'users' = Lấy user từ provider 'users' (định nghĩa bên dưới)
     * 
     * LƯU Ý: Sanctum tự động thêm guard 'sanctum' cho API authentication
     * Khi dùng middleware 'auth:sanctum', Sanctum sẽ:
     * 1. Kiểm tra token trong header Authorization
     * 2. Tìm token trong bảng personal_access_tokens
     * 3. Lấy user từ provider 'users'
     */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | All authentication drivers have a user provider. This defines how the
    | users are actually retrieved out of your database or other storage
    | mechanisms used by this application to persist your user's data.
    |
    | If you have multiple user tables or models you may configure multiple
    | sources which represent each model / table. These sources may then
    | be assigned to any extra authentication guards you have defined.
    |
    | Supported: "database", "eloquent"
    |
    */

    /**
     * PROVIDERS - Nguồn dữ liệu user
     * 
     * Provider 'users':
     * - driver: 'eloquent' = Sử dụng Eloquent ORM
     * - model: NguoiDung::class = Model đại diện cho user
     * 
     * ĐÃ THAY ĐỔI:
     * - Mặc định Laravel dùng App\Models\User
     * - Đã đổi sang App\Models\NguoiDung để phù hợp với database tiếng Việt
     * 
     * Model NguoiDung phải:
     * - Extend Illuminate\Foundation\Auth\User (Authenticatable)
     * - Có method getAuthPassword() trả về cột mật khẩu
     * - Dùng trait HasApiTokens cho Sanctum
     */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\NguoiDung::class,  // ĐÃ ĐỔI TỪ User SANG NguoiDung
        ],

        // Cấu hình thay thế nếu dùng Query Builder thay vì Eloquent
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | You may specify multiple password reset configurations if you have more
    | than one user table or model in the application and you want to have
    | separate password reset settings based on the specific user types.
    |
    | The expiry time is the number of minutes that each reset token will be
    | considered valid. This security feature keeps tokens short-lived so
    | they have less time to be guessed. You may change this as needed.
    |
    | The throttle setting is the number of seconds a user must wait before
    | generating more password reset tokens. This prevents the user from
    | quickly generating a very large amount of password reset tokens.
    |
    */

    /**
     * PASSWORDS - Cấu hình reset mật khẩu
     * 
     * provider: 'users' = Dùng provider 'users' để tìm user
     * table: 'password_reset_tokens' = Bảng lưu token reset
     * expire: 60 = Token hết hạn sau 60 phút
     * throttle: 60 = Phải đợi 60 giây mới được gửi lại email reset
     */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the amount of seconds before a password confirmation
    | times out and the user is prompted to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    /**
     * PASSWORD TIMEOUT - Thời gian xác nhận mật khẩu
     * 
     * 10800 giây = 3 giờ
     * Sau thời gian này, user phải nhập lại mật khẩu để xác nhận
     * các hành động nhạy cảm (đổi email, xóa tài khoản, etc.)
     */
    'password_timeout' => 10800,

];
