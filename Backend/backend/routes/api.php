<?php
/**
 * ============================================================================
 * API ROUTES - ĐỊNH NGHĨA CÁC ĐƯỜNG DẪN API
 * ============================================================================
 *
 * File này định nghĩa tất cả routes cho API (prefix /api).
 *
 * CẤU TRÚC URL:
 * - Base URL: http://localhost:8000/api
 * - Ví dụ: POST http://localhost:8000/api/auth/login
 *
 * MIDDLEWARE MẶC ĐỊNH:
 * - Tất cả routes trong file này tự động có middleware 'api'
 * - Được load bởi RouteServiceProvider
 *
 * CÁCH TEST:
 * - Dùng Postman, Insomnia, hoặc curl
 * - Gửi JSON trong body với header: Content-Type: application/json
 * ============================================================================
 */

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;      // Controller xử lý xác thực
use App\Http\Controllers\Api\MonAnController;     // Controller xử lý món ăn
use App\Http\Controllers\Api\NguoiDungController; // Controller quản lý người dùng
use App\Http\Controllers\Api\NhaHangController;   // Controller quản lý nhà hàng
use App\Http\Controllers\Api\HoaDonController;    // Controller quản lý hóa đơn
use App\Http\Controllers\Api\BanController;       // Controller quản lý bàn
use App\Http\Controllers\Api\KhuVucController;    // Controller quản lý khu vực
use App\Http\Controllers\Api\DanhMucMonController; // Controller quản lý danh mục món
use App\Http\Controllers\Api\DatBanController;    // Controller quản lý đặt bàn
use App\Http\Controllers\Api\DatMonMangVeController; // Controller quản lý đặt món mang về

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ============================================================================
// AUTH ROUTES (Public - Không cần đăng nhập)
// ============================================================================
// Route::prefix('auth') tạo nhóm routes với prefix /api/auth
// Tất cả routes trong group này sẽ có URL: /api/auth/...
Route::prefix('auth')->group(function () {

    /**
     * ĐĂNG KÝ TÀI KHOẢN
     * URL: POST /api/auth/register
     * Body: { HoTen, Email, MatKhau, MatKhau_confirmation, SDT? }
     * Response: { success, message, data: { user, token, token_type } }
     */
    Route::post('/register', [AuthController::class, 'register']);

    /**
     * ĐĂNG NHẬP BẰNG EMAIL/MẬT KHẨU
     * URL: POST /api/auth/login
     * Body: { Email, MatKhau }
     * Response: { success, message, data: { user, token, token_type } }
     */
    Route::post('/login', [AuthController::class, 'login']);

    // ========== GOOGLE OAUTH ==========

    /**
     * REDIRECT ĐẾN GOOGLE (Phương thức 1 - Server-side flow)
     * URL: GET /api/auth/google
     * Chuyển hướng user đến trang đăng nhập Google
     * Sau khi đăng nhập, Google redirect về /api/auth/google/callback
     */
    Route::get('/google', [AuthController::class, 'redirectToGoogle']);

    /**
     * CALLBACK TỪ GOOGLE (Phương thức 1)
     * URL: GET /api/auth/google/callback
     * Google gọi URL này sau khi user đăng nhập
     * Xử lý và redirect về frontend với token
     */
    Route::get('/google/callback', [AuthController::class, 'handleGoogleCallback']);

    /**
     * ĐĂNG NHẬP BẰNG GOOGLE ID TOKEN (Phương thức 2 - One Tap / Sign In Button)
     * URL: POST /api/auth/google/token
     * Body: { credential: "eyJhbGciOiJSUzI1NiIs..." }
     * Dùng khi frontend sử dụng Google Sign-In button
     * Frontend gửi ID Token (JWT) lên để backend verify
     */
    Route::post('/google/token', [AuthController::class, 'loginWithGoogleToken']);

    /**
     * ĐĂNG NHẬP BẰNG GOOGLE AUTHORIZATION CODE (Phương thức 3 - Popup flow)
     * URL: POST /api/auth/google/code
     * Body: { code: "4/0AX4XfWh..." }
     * Dùng khi frontend mở popup Google OAuth
     * Frontend gửi authorization code lên để backend đổi lấy token
     */
    Route::post('/google/code', [AuthController::class, 'loginWithGoogleCode']);
});

// ============================================================================
// PROTECTED ROUTES (Cần đăng nhập - Phải có token)
// ============================================================================
// Route::middleware('auth:sanctum') yêu cầu token hợp lệ
// Token gửi trong header: Authorization: Bearer <token>
// Nếu không có token hoặc token sai -> trả về 401 Unauthorized
Route::middleware('auth:sanctum')->group(function () {

    /**
     * LẤY THÔNG TIN USER HIỆN TẠI
     * URL: GET /api/user
     * Header: Authorization: Bearer <token>
     * Response: { success, data: { user } }
     */
    Route::get('/user', [AuthController::class, 'me']);

    /**
     * ĐĂNG XUẤT (Xóa token hiện tại)
     * URL: POST /api/logout
     * Header: Authorization: Bearer <token>
     * Response: { success, message }
     * Chỉ xóa token đang dùng, các token khác vẫn còn
     */
    Route::post('/logout', [AuthController::class, 'logout']);

    /**
     * ĐĂNG XUẤT TẤT CẢ THIẾT BỊ (Xóa tất cả token)
     * URL: POST /api/logout-all
     * Header: Authorization: Bearer <token>
     * Response: { success, message }
     * Xóa tất cả token của user
     */
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
});

// ============================================================================
// MÓN ĂN ROUTES (Public - Không cần đăng nhập)
// ============================================================================
Route::prefix('mon-an')->group(function () {
    /**
     * LẤY DANH SÁCH MÓN ĂN
     * URL: GET /api/mon-an
     * Query: per_page, nha_hang_id, search
     */
    Route::get('/', [MonAnController::class, 'index']);

    /**
     * LẤY MÓN ĂN PHỔ BIẾN
     * URL: GET /api/mon-an/popular
     * Query: limit (mặc định 6)
     */
    Route::get('/popular', [MonAnController::class, 'popular']);

    /**
     * LẤY MÓN ĂN MỚI NHẤT
     * URL: GET /api/mon-an/latest
     * Query: limit (mặc định 10)
     * Sắp xếp theo ngày thêm mới nhất (TaoLuc DESC)
     */
    Route::get('/latest', [MonAnController::class, 'latest']);

    /**
     * LẤY CHI TIẾT MÓN ĂN
     * URL: GET /api/mon-an/{id}
     */
    Route::get('/{id}', [MonAnController::class, 'show']);
});

// ============================================================================
// NGƯỜI DÙNG ROUTES (Admin - Quản lý người dùng)
// ============================================================================
Route::prefix('nguoi-dung')->group(function () {
    /**
     * THỐNG KÊ NGƯỜI DÙNG
     * URL: GET /api/nguoi-dung/stats
     */
    Route::get('/stats', [NguoiDungController::class, 'stats']);

    /**
     * LẤY DANH SÁCH NGƯỜI DÙNG
     * URL: GET /api/nguoi-dung
     * Query: per_page, search, loai
     */
    Route::get('/', [NguoiDungController::class, 'index']);

    /**
     * TẠO NGƯỜI DÙNG MỚI
     * URL: POST /api/nguoi-dung
     */
    Route::post('/', [NguoiDungController::class, 'store']);

    /**
     * LẤY CHI TIẾT NGƯỜI DÙNG
     * URL: GET /api/nguoi-dung/{id}
     */
    Route::get('/{id}', [NguoiDungController::class, 'show']);

    /**
     * CẬP NHẬT NGƯỜI DÙNG
     * URL: PUT /api/nguoi-dung/{id}
     */
    Route::put('/{id}', [NguoiDungController::class, 'update']);

    /**
     * XÓA NGƯỜI DÙNG
     * URL: DELETE /api/nguoi-dung/{id}
     */
    Route::delete('/{id}', [NguoiDungController::class, 'destroy']);
});


// ============================================================================
// NHÀ HÀNG ROUTES (Admin - Quản lý nhà hàng)
// ============================================================================
Route::prefix('nha-hang')->group(function () {
    /**
     * THỐNG KÊ NHÀ HÀNG
     * URL: GET /api/nha-hang/stats
     */
    Route::get('/stats', [NhaHangController::class, 'stats']);

    /**
     * LẤY DANH SÁCH NHÀ HÀNG
     * URL: GET /api/nha-hang
     */
    Route::get('/', [NhaHangController::class, 'index']);

    /**
     * TẠO NHÀ HÀNG MỚI
     * URL: POST /api/nha-hang
     */
    Route::post('/', [NhaHangController::class, 'store']);

    /**
     * LẤY CHI TIẾT NHÀ HÀNG
     * URL: GET /api/nha-hang/{id}
     */
    Route::get('/{id}', [NhaHangController::class, 'show']);

    /**
     * CẬP NHẬT NHÀ HÀNG
     * URL: PUT /api/nha-hang/{id}
     */
    Route::put('/{id}', [NhaHangController::class, 'update']);

    /**
     * XÓA NHÀ HÀNG
     * URL: DELETE /api/nha-hang/{id}
     */
    Route::delete('/{id}', [NhaHangController::class, 'destroy']);
});

// ============================================================================
// HÓA ĐƠN ROUTES (Đặt hàng)
// ============================================================================
Route::prefix('hoa-don')->group(function () {
    /**
     * TẠO HÓA ĐƠN MỚI (ĐẶT HÀNG)
     * URL: POST /api/hoa-don
     */
    Route::post('/', [HoaDonController::class, 'store']);

    /**
     * LẤY DANH SÁCH HÓA ĐƠN
     * URL: GET /api/hoa-don
     */
    Route::get('/', [HoaDonController::class, 'index']);

    /**
     * LẤY CHI TIẾT HÓA ĐƠN
     * URL: GET /api/hoa-don/{id}
     */
    Route::get('/{id}', [HoaDonController::class, 'show']);

    /**
     * CẬP NHẬT TRẠNG THÁI HÓA ĐƠN
     * URL: PUT /api/hoa-don/{id}/status
     */
    Route::put('/{id}/status', [HoaDonController::class, 'updateStatus']);
});

// ============================================================================
// BÀN ROUTES (Quản lý bàn)
// ============================================================================
Route::prefix('ban')->group(function () {
    /**
     * THỐNG KÊ BÀN
     * URL: GET /api/ban/stats
     */
    Route::get('/stats', [BanController::class, 'stats']);

    /**
     * LẤY DANH SÁCH BÀN
     * URL: GET /api/ban
     * Query: khu_vuc_id, trang_thai, nha_hang_id
     */
    Route::get('/', [BanController::class, 'index']);

    /**
     * TẠO BÀN MỚI
     * URL: POST /api/ban
     */
    Route::post('/', [BanController::class, 'store']);

    /**
     * LẤY CHI TIẾT BÀN
     * URL: GET /api/ban/{id}
     */
    Route::get('/{id}', [BanController::class, 'show']);

    /**
     * CẬP NHẬT BÀN
     * URL: PUT /api/ban/{id}
     */
    Route::put('/{id}', [BanController::class, 'update']);

    /**
     * CẬP NHẬT TRẠNG THÁI BÀN
     * URL: PUT /api/ban/{id}/status
     */
    Route::put('/{id}/status', [BanController::class, 'updateStatus']);

    /**
     * XÓA BÀN
     * URL: DELETE /api/ban/{id}
     */
    Route::delete('/{id}', [BanController::class, 'destroy']);
});

// ============================================================================
// KHU VỰC ROUTES (Quản lý khu vực)
// ============================================================================
Route::prefix('khu-vuc')->group(function () {
    /**
     * LẤY DANH SÁCH KHU VỰC
     * URL: GET /api/khu-vuc
     */
    Route::get('/', [KhuVucController::class, 'index']);

    /**
     * TẠO KHU VỰC MỚI
     * URL: POST /api/khu-vuc
     */
    Route::post('/', [KhuVucController::class, 'store']);

    /**
     * LẤY CHI TIẾT KHU VỰC
     * URL: GET /api/khu-vuc/{id}
     */
    Route::get('/{id}', [KhuVucController::class, 'show']);

    /**
     * CẬP NHẬT KHU VỰC
     * URL: PUT /api/khu-vuc/{id}
     */
    Route::put('/{id}', [KhuVucController::class, 'update']);

    /**
     * XÓA KHU VỰC
     * URL: DELETE /api/khu-vuc/{id}
     */
    Route::delete('/{id}', [KhuVucController::class, 'destroy']);
});

// ============================================================================
// DANH MỤC MÓN ROUTES (Quản lý danh mục món ăn)
// ============================================================================
Route::prefix('danh-muc-mon')->group(function () {
    /**
     * LẤY DANH SÁCH DANH MỤC MÓN
     * URL: GET /api/danh-muc-mon
     */
    Route::get('/', [DanhMucMonController::class, 'index']);

    /**
     * TẠO DANH MỤC MỚI
     * URL: POST /api/danh-muc-mon
     */
    Route::post('/', [DanhMucMonController::class, 'store']);

    /**
     * LẤY CHI TIẾT DANH MỤC
     * URL: GET /api/danh-muc-mon/{id}
     */
    Route::get('/{id}', [DanhMucMonController::class, 'show']);

    /**
     * CẬP NHẬT DANH MỤC
     * URL: PUT /api/danh-muc-mon/{id}
     */
    Route::put('/{id}', [DanhMucMonController::class, 'update']);

    /**
     * XÓA DANH MỤC
     * URL: DELETE /api/danh-muc-mon/{id}
     */
    Route::delete('/{id}', [DanhMucMonController::class, 'destroy']);
});


// ============================================================================
// ĐẶT BÀN ROUTES (Quản lý đặt bàn)
// ============================================================================
Route::prefix('dat-ban')->group(function () {
    /**
     * LẤY DANH SÁCH ĐẶT BÀN
     * URL: GET /api/dat-ban
     * Query: nguoi_dung_id, nha_hang_id, trang_thai
     */
    Route::get('/', [DatBanController::class, 'index']);

    /**
     * TẠO ĐƠN ĐẶT BÀN MỚI
     * URL: POST /api/dat-ban
     */
    Route::post('/', [DatBanController::class, 'store']);

    /**
     * LẤY CHI TIẾT ĐẶT BÀN
     * URL: GET /api/dat-ban/{id}
     */
    Route::get('/{id}', [DatBanController::class, 'show']);

    /**
     * CẬP NHẬT TRẠNG THÁI ĐẶT BÀN
     * URL: PUT /api/dat-ban/{id}/status
     */
    Route::put('/{id}/status', [DatBanController::class, 'updateStatus']);

    /**
     * HỦY ĐẶT BÀN
     * URL: PUT /api/dat-ban/{id}/cancel
     */
    Route::put('/{id}/cancel', [DatBanController::class, 'cancel']);
});


// ============================================================================
// ĐẶT MÓN MANG VỀ ROUTES (Takeaway)
// ============================================================================
Route::prefix('dat-mon-mang-ve')->group(function () {
    /**
     * LẤY DANH SÁCH ĐƠN ĐẶT MÓN
     * URL: GET /api/dat-mon-mang-ve
     * Query: nguoi_dung_id, nha_hang_id, trang_thai
     */
    Route::get('/', [DatMonMangVeController::class, 'index']);

    /**
     * TẠO ĐƠN ĐẶT MÓN MỚI
     * URL: POST /api/dat-mon-mang-ve
     */
    Route::post('/', [DatMonMangVeController::class, 'store']);

    /**
     * LẤY CHI TIẾT ĐƠN ĐẶT MÓN
     * URL: GET /api/dat-mon-mang-ve/{id}
     */
    Route::get('/{id}', [DatMonMangVeController::class, 'show']);

    /**
     * CẬP NHẬT TRẠNG THÁI ĐƠN
     * URL: PUT /api/dat-mon-mang-ve/{id}/status
     */
    Route::put('/{id}/status', [DatMonMangVeController::class, 'updateStatus']);

    /**
     * HỦY ĐƠN ĐẶT MÓN
     * URL: PUT /api/dat-mon-mang-ve/{id}/cancel
     */
    Route::put('/{id}/cancel', [DatMonMangVeController::class, 'cancel']);
});
