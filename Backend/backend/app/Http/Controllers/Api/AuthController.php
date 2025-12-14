<?php
/**
 * ============================================================================
 * AUTH CONTROLLER - XỬ LÝ XÁC THỰC NGƯỜI DÙNG
 * ============================================================================
 * 
 * File này chứa tất cả logic xác thực người dùng cho API:
 * - Đăng ký tài khoản mới (register)
 * - Đăng nhập bằng email/mật khẩu (login)
 * - Đăng nhập bằng Google OAuth (3 phương thức)
 * - Lấy thông tin user hiện tại (me)
 * - Đăng xuất (logout, logoutAll)
 * 
 * SỬ DỤNG:
 * - Model: NguoiDung (app/Models/NguoiDung.php)
 * - Package: Laravel Sanctum (tạo API token)
 * - Package: Laravel Socialite (Google OAuth)
 * - Package: Google API Client (xác thực Google ID Token)
 * 
 * ROUTES: Định nghĩa trong routes/api.php
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;                          // Model người dùng - bảng 'nguoidung' trong DB
use Illuminate\Http\Request;                       // Đối tượng HTTP Request
use Illuminate\Support\Facades\Hash;               // Mã hóa/kiểm tra mật khẩu (bcrypt)
use Illuminate\Support\Facades\Validator;          // Validate dữ liệu đầu vào
use Laravel\Socialite\Facades\Socialite;           // OAuth với Google, Facebook, etc.

class AuthController extends Controller
{
    /**
     * ========================================================================
     * ĐĂNG KÝ TÀI KHOẢN MỚI
     * ========================================================================
     * 
     * ROUTE: POST /api/auth/register
     * 
     * INPUT (JSON body):
     * {
     *   "HoTen": "Nguyễn Văn A",           // Bắt buộc, tối đa 150 ký tự
     *   "Email": "email@example.com",       // Bắt buộc, phải unique trong DB
     *   "MatKhau": "123456",                // Bắt buộc, tối thiểu 6 ký tự
     *   "MatKhau_confirmation": "123456",   // Bắt buộc, phải khớp với MatKhau
     *   "SDT": "0901234567"                 // Tùy chọn
     * }
     * 
     * OUTPUT (thành công - 201):
     * {
     *   "success": true,
     *   "message": "Đăng ký thành công",
     *   "data": {
     *     "user": { ... thông tin user ... },
     *     "token": "1|abc123...",           // Token để gọi API
     *     "token_type": "Bearer"
     *   }
     * }
     * 
     * OUTPUT (lỗi validation - 422):
     * {
     *   "success": false,
     *   "message": "Dữ liệu không hợp lệ",
     *   "errors": { "Email": ["Email đã được sử dụng"] }
     * }
     * 
     * FLOW:
     * 1. Validate dữ liệu đầu vào
     * 2. Mã hóa mật khẩu bằng Hash::make() (bcrypt)
     * 3. Tạo record mới trong bảng 'nguoidung'
     * 4. Tạo API token bằng Sanctum
     * 5. Trả về user + token
     */
    public function register(Request $request)
    {
        // ====== BƯỚC 1: VALIDATE DỮ LIỆU ĐẦU VÀO ======
        // Validator::make() kiểm tra dữ liệu theo rules
        $validator = Validator::make($request->all(), [
            'HoTen' => 'required|string|max:150',           // Bắt buộc, chuỗi, tối đa 150 ký tự
            'Email' => 'required|email|unique:nguoidung,Email', // Bắt buộc, đúng format email, không trùng trong bảng nguoidung
            'MatKhau' => 'required|string|min:6|confirmed', // Bắt buộc, tối thiểu 6 ký tự, phải có MatKhau_confirmation khớp
            'SDT' => 'nullable|string|max:20',              // Tùy chọn, chuỗi, tối đa 20 ký tự
        ], [
            // Custom error messages bằng tiếng Việt
            'HoTen.required' => 'Họ tên là bắt buộc',
            'Email.required' => 'Email là bắt buộc',
            'Email.email' => 'Email không hợp lệ',
            'Email.unique' => 'Email đã được sử dụng',
            'MatKhau.required' => 'Mật khẩu là bắt buộc',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        // Nếu validate thất bại, trả về lỗi 422
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()  // Chi tiết lỗi từng field
            ], 422);  // HTTP 422 Unprocessable Entity
        }

        // ====== BƯỚC 2: TẠO NGƯỜI DÙNG MỚI ======
        // NguoiDung::create() tạo record mới trong bảng 'nguoidung'
        // Các field phải nằm trong $fillable của Model
        $nguoiDung = NguoiDung::create([
            'HoTen' => $request->HoTen,
            'Email' => $request->Email,
            'MatKhau' => Hash::make($request->MatKhau),  // Mã hóa mật khẩu bằng bcrypt
            'SDT' => $request->SDT,
            'LoaiNguoiDung' => NguoiDung::LOAI_KHACH_HANG,  // Mặc định là khách hàng (=3)
            'TaoLuc' => now(),      // Thời gian hiện tại
            'CapNhatLuc' => now(),
        ]);

        // ====== BƯỚC 3: TẠO API TOKEN ======
        // createToken() là method của Sanctum (HasApiTokens trait)
        // 'auth_token' là tên token (để phân biệt khi có nhiều token)
        // plainTextToken là token dạng text để gửi cho client
        $token = $nguoiDung->createToken('auth_token')->plainTextToken;

        // ====== BƯỚC 4: TRẢ VỀ RESPONSE ======
        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'data' => [
                'user' => $nguoiDung,   // Thông tin user (tự động ẩn MatKhau vì nằm trong $hidden)
                'token' => $token,       // Token để client lưu và gửi kèm các request sau
                'token_type' => 'Bearer' // Loại token (dùng trong header: Authorization: Bearer <token>)
            ]
        ], 201);  // HTTP 201 Created
    }

    /**
     * ========================================================================
     * ĐĂNG NHẬP BẰNG EMAIL VÀ MẬT KHẨU
     * ========================================================================
     * 
     * ROUTE: POST /api/auth/login
     * 
     * INPUT (JSON body):
     * {
     *   "Email": "email@example.com",
     *   "MatKhau": "123456"
     * }
     * 
     * OUTPUT (thành công - 200):
     * {
     *   "success": true,
     *   "message": "Đăng nhập thành công",
     *   "data": { "user": {...}, "token": "...", "token_type": "Bearer" }
     * }
     * 
     * OUTPUT (sai thông tin - 401):
     * {
     *   "success": false,
     *   "message": "Email hoặc mật khẩu không đúng"
     * }
     * 
     * FLOW:
     * 1. Validate email và mật khẩu
     * 2. Tìm user theo email
     * 3. So sánh mật khẩu với Hash::check()
     * 4. Tạo token mới và trả về
     */
    public function login(Request $request)
    {
        // ====== BƯỚC 1: VALIDATE ======
        $validator = Validator::make($request->all(), [
            'Email' => 'required|email',
            'MatKhau' => 'required|string',
        ], [
            'Email.required' => 'Email là bắt buộc',
            'Email.email' => 'Email không hợp lệ',
            'MatKhau.required' => 'Mật khẩu là bắt buộc',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // ====== BƯỚC 2: TÌM USER THEO EMAIL ======
        // where()->first() trả về 1 record hoặc null
        $nguoiDung = NguoiDung::where('Email', $request->Email)->first();

        // ====== BƯỚC 3: KIỂM TRA MẬT KHẨU ======
        // Demo mode: So sánh trực tiếp plaintext (không hash)
        // Production: Nên dùng Hash::check($request->MatKhau, $nguoiDung->MatKhau)
        if (!$nguoiDung || $request->MatKhau !== $nguoiDung->MatKhau) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);  // HTTP 401 Unauthorized
        }

        // ====== BƯỚC 4: TẠO TOKEN VÀ TRẢ VỀ ======
        $token = $nguoiDung->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'user' => $nguoiDung,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ]);
    }

    /**
     * ========================================================================
     * GOOGLE OAUTH - PHƯƠNG THỨC 1: REDIRECT
     * ========================================================================
     * 
     * ROUTE: GET /api/auth/google
     * 
     * Chuyển hướng user đến trang đăng nhập Google.
     * Sau khi user đăng nhập, Google sẽ redirect về callback URL.
     * 
     * FLOW:
     * 1. User click "Đăng nhập Google" -> gọi API này
     * 2. API redirect đến accounts.google.com
     * 3. User đăng nhập Google
     * 4. Google redirect về /api/auth/google/callback
     * 
     * LƯU Ý: stateless() vì API không dùng session
     */
    public function redirectToGoogle()
    {
        // Socialite đọc config từ config/services.php -> 'google'
        // stateless() = không lưu state vào session (cho API)
        return Socialite::driver('google')
            ->stateless()         //
            ->redirect();
    }

    /**
     * ========================================================================
     * GOOGLE OAUTH - PHƯƠNG THỨC 1: CALLBACK
     * ========================================================================
     * 
     * ROUTE: GET /api/auth/google/callback
     * 
     * Google gọi URL này sau khi user đăng nhập thành công.
     * Xử lý thông tin user từ Google và tạo/cập nhật tài khoản.
     * 
     * FLOW:
     * 1. Nhận authorization code từ Google
     * 2. Đổi code lấy access token
     * 3. Lấy thông tin user từ Google
     * 4. Tìm hoặc tạo user trong DB
     * 5. Tạo API token
     * 6. Redirect về frontend với token
     * 
     * BIẾN MÔI TRƯỜNG SỬ DỤNG:
     * - GOOGLE_CLIENT_ID (trong .env)
     * - GOOGLE_CLIENT_SECRET (trong .env)
     * - GOOGLE_REDIRECT_URI (trong .env)
     * - FRONTEND_URL (trong .env)
     */
    public function handleGoogleCallback()
    {
        try {
            // ====== BƯỚC 1: LẤY THÔNG TIN USER TỪ GOOGLE ======
            // Socialite tự động đổi code -> token -> user info
            $googleUser = Socialite::driver('google')->stateless()->user();//

            // ====== BƯỚC 2: TÌM HOẶC TẠO USER ======
            // Tìm theo google_id HOẶC email (trường hợp đã đăng ký bằng email trước)
            $nguoiDung = NguoiDung::where('google_id', $googleUser->getId())
                ->orWhere('Email', $googleUser->getEmail())
                ->first();

            if ($nguoiDung) {
                // User đã tồn tại -> cập nhật thông tin Google
                $nguoiDung->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,  // Access token từ Google
                    'Avatar' => $googleUser->getAvatar(),  // URL ảnh đại diện
                    'CapNhatLuc' => now(),
                ]);
            } else {
                // User mới -> tạo tài khoản
                $nguoiDung = NguoiDung::create([
                    'HoTen' => $googleUser->getName(),
                    'Email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'Avatar' => $googleUser->getAvatar(),
                    'LoaiNguoiDung' => NguoiDung::LOAI_KHACH_HANG,
                    'TaoLuc' => now(),
                    'CapNhatLuc' => now(),
                ]);
            }

            // ====== BƯỚC 3: TẠO API TOKEN ======
            $token = $nguoiDung->createToken('google_auth_token')->plainTextToken;

            // ====== BƯỚC 4: REDIRECT VỀ FRONTEND ======
            // Gửi token qua URL query string
            // Frontend sẽ đọc token và lưu vào localStorage
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            
            return redirect()->away($frontendUrl . '/auth/callback?token=' . $token . '&user=' . urlencode(json_encode([
                'NguoiDungID' => $nguoiDung->NguoiDungID,
                'HoTen' => $nguoiDung->HoTen,
                'Email' => $nguoiDung->Email,
                'Avatar' => $nguoiDung->Avatar,
                'LoaiNguoiDung' => $nguoiDung->LoaiNguoiDung,
            ])));

        } catch (\Exception $e) {
            // Nếu có lỗi, redirect về frontend với error message
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/callback?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * ========================================================================
     * GOOGLE OAUTH - PHƯƠNG THỨC 2: ID TOKEN (One Tap / Sign In Button)
     * ========================================================================
     * 
     * ROUTE: POST /api/auth/google/token
     * 
     * Dùng khi frontend sử dụng Google Sign-In button hoặc One Tap.
     * Frontend gửi ID Token (credential) lên backend để xác thực.
     * 
     * INPUT (JSON body):
     * {
     *   "credential": "eyJhbGciOiJSUzI1NiIs..."  // Google ID Token (JWT)
     * }
     * 
     * FLOW:
     * 1. Frontend hiển thị Google Sign-In button
     * 2. User click và đăng nhập
     * 3. Google trả về ID Token cho frontend
     * 4. Frontend gửi ID Token lên API này
     * 5. Backend verify token với Google
     * 6. Tạo/cập nhật user và trả về API token
     * 
     * PACKAGE SỬ DỤNG: google/apiclient
     */
    public function loginWithGoogleToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'credential' => 'required|string',  // Google ID Token
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // ====== BƯỚC 1: XÁC THỰC GOOGLE ID TOKEN ======
            // Google\Client verify token và trả về payload (thông tin user)
            $client = new \Google\Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->credential);

            // Nếu token không hợp lệ, payload = false
            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google không hợp lệ'
                ], 401);
            }

            // ====== BƯỚC 2: TRÍCH XUẤT THÔNG TIN TỪ PAYLOAD ======
            // Payload chứa: sub (Google ID), email, name, picture, etc.
            $googleId = $payload['sub'];           // Google User ID (unique)
            $email = $payload['email'];
            $name = $payload['name'];
            $avatar = $payload['picture'] ?? null;

            // ====== BƯỚC 3: TÌM HOẶC TẠO USER ======
            $nguoiDung = NguoiDung::where('google_id', $googleId)
                ->orWhere('Email', $email)
                ->first();

            if ($nguoiDung) {
                $nguoiDung->update([
                    'google_id' => $googleId,
                    'Avatar' => $avatar,
                    'CapNhatLuc' => now(),
                ]);
            } else {
                $nguoiDung = NguoiDung::create([
                    'HoTen' => $name,
                    'Email' => $email,
                    'google_id' => $googleId,
                    'Avatar' => $avatar,
                    'LoaiNguoiDung' => NguoiDung::LOAI_KHACH_HANG,
                    'TaoLuc' => now(),
                    'CapNhatLuc' => now(),
                ]);
            }

            // ====== BƯỚC 4: TẠO API TOKEN VÀ TRẢ VỀ ======
            $token = $nguoiDung->createToken('google_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập Google thành công',
                'data' => [
                    'user' => $nguoiDung,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực Google: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================================
     * GOOGLE OAUTH - PHƯƠNG THỨC 3: AUTHORIZATION CODE (Popup Flow)
     * ========================================================================
     * 
     * ROUTE: POST /api/auth/google/code
     * 
     * Dùng khi frontend mở popup Google Sign-In và nhận authorization code.
     * Backend đổi code lấy access token rồi lấy thông tin user.
     * 
     * INPUT (JSON body):
     * {
     *   "code": "4/0AX4XfWh..."  // Authorization code từ Google
     * }
     * 
     * FLOW:
     * 1. Frontend mở popup Google OAuth
     * 2. User đăng nhập và cho phép
     * 3. Google trả về authorization code cho frontend
     * 4. Frontend gửi code lên API này
     * 5. Backend đổi code -> access token -> user info
     * 6. Tạo/cập nhật user và trả về API token
     * 
     * LƯU Ý: redirect_uri phải là 'postmessage' cho popup flow
     */
    public function loginWithGoogleCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',  // Authorization code
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Code không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // ====== BƯỚC 1: CẤU HÌNH GOOGLE CLIENT ======
            $client = new \Google\Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->setRedirectUri('postmessage');  // QUAN TRỌNG: 'postmessage' cho popup flow

            // ====== BƯỚC 2: ĐỔI CODE LẤY ACCESS TOKEN ======
            // fetchAccessTokenWithAuthCode() gọi Google API để đổi code -> token
            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            // Kiểm tra lỗi
            if (isset($token['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xác thực Google: ' . $token['error_description']
                ], 401);
            }

            // ====== BƯỚC 3: LẤY THÔNG TIN USER TỪ GOOGLE ======
            $client->setAccessToken($token);
            $oauth2 = new \Google\Service\Oauth2($client);  // Google OAuth2 Service
            $googleUser = $oauth2->userinfo->get();         // Gọi API lấy user info

            // Trích xuất thông tin
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->getName();
            $avatar = $googleUser->getPicture();

            // ====== BƯỚC 4: TÌM HOẶC TẠO USER ======
            $nguoiDung = NguoiDung::where('google_id', $googleId)
                ->orWhere('Email', $email)
                ->first();

            if ($nguoiDung) {
                $nguoiDung->update([
                    'google_id' => $googleId,
                    'Avatar' => $avatar,
                    'CapNhatLuc' => now(),
                ]);
            } else {
                $nguoiDung = NguoiDung::create([
                    'HoTen' => $name,
                    'Email' => $email,
                    'google_id' => $googleId,
                    'Avatar' => $avatar,
                    'LoaiNguoiDung' => NguoiDung::LOAI_KHACH_HANG,
                    'TaoLuc' => now(),
                    'CapNhatLuc' => now(),
                ]);
            }

            // ====== BƯỚC 5: TẠO API TOKEN VÀ TRẢ VỀ ======
            $token = $nguoiDung->createToken('google_auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập Google thành công',
                'data' => [
                    'user' => $nguoiDung,
                    'token' => $token,
                    'token_type' => 'Bearer'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi xác thực Google: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ========================================================================
     * LẤY THÔNG TIN NGƯỜI DÙNG HIỆN TẠI
     * ========================================================================
     * 
     * ROUTE: GET /api/user
     * MIDDLEWARE: auth:sanctum (phải đăng nhập)
     * 
     * HEADER YÊU CẦU:
     * Authorization: Bearer <token>
     * 
     * OUTPUT:
     * {
     *   "success": true,
     *   "data": {
     *     "user": { ... thông tin user ... }
     *   }
     * }
     * 
     * LƯU Ý: $request->user() trả về user từ token (Sanctum tự động xử lý)
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()  // User hiện tại từ token
            ]
        ]);
    }

    /**
     * ========================================================================
     * ĐĂNG XUẤT (XÓA TOKEN HIỆN TẠI)
     * ========================================================================
     * 
     * ROUTE: POST /api/logout
     * MIDDLEWARE: auth:sanctum
     * 
     * Xóa token đang sử dụng, các token khác vẫn còn hiệu lực.
     * Dùng khi user đăng xuất trên 1 thiết bị.
     */
    public function logout(Request $request)
    {
        // currentAccessToken() = token đang dùng để gọi API này
        // delete() = xóa token khỏi bảng personal_access_tokens
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * ========================================================================
     * ĐĂNG XUẤT TẤT CẢ THIẾT BỊ (XÓA TẤT CẢ TOKEN)
     * ========================================================================
     * 
     * ROUTE: POST /api/logout-all
     * MIDDLEWARE: auth:sanctum
     * 
     * Xóa tất cả token của user, đăng xuất khỏi mọi thiết bị.
     * Dùng khi user muốn bảo mật tài khoản hoặc đổi mật khẩu.
     */
    public function logoutAll(Request $request)
    {
        // tokens() = tất cả token của user
        // delete() = xóa hết
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã đăng xuất khỏi tất cả thiết bị'
        ]);
    }
}
