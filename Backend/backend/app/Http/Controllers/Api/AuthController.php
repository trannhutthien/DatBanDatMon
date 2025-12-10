<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Đăng ký tài khoản mới
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'HoTen' => 'required|string|max:150',
            'Email' => 'required|email|unique:nguoidung,Email',
            'MatKhau' => 'required|string|min:6|confirmed',
            'SDT' => 'nullable|string|max:20',
        ], [
            'HoTen.required' => 'Họ tên là bắt buộc',
            'Email.required' => 'Email là bắt buộc',
            'Email.email' => 'Email không hợp lệ',
            'Email.unique' => 'Email đã được sử dụng',
            'MatKhau.required' => 'Mật khẩu là bắt buộc',
            'MatKhau.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'MatKhau.confirmed' => 'Xác nhận mật khẩu không khớp',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $nguoiDung = NguoiDung::create([
            'HoTen' => $request->HoTen,
            'Email' => $request->Email,
            'MatKhau' => Hash::make($request->MatKhau),
            'SDT' => $request->SDT,
            'LoaiNguoiDung' => NguoiDung::LOAI_KHACH_HANG,
            'TaoLuc' => now(),
            'CapNhatLuc' => now(),
        ]);

        $token = $nguoiDung->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Đăng ký thành công',
            'data' => [
                'user' => $nguoiDung,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    /**
     * Đăng nhập bằng email và mật khẩu
     */
    public function login(Request $request)
    {
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

        $nguoiDung = NguoiDung::where('Email', $request->Email)->first();

        if (!$nguoiDung || !Hash::check($request->MatKhau, $nguoiDung->MatKhau)) {
            return response()->json([
                'success' => false,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

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
     * Redirect đến Google để đăng nhập
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirect();
    }

    /**
     * Xử lý callback từ Google
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Tìm hoặc tạo người dùng
            $nguoiDung = NguoiDung::where('google_id', $googleUser->getId())
                ->orWhere('Email', $googleUser->getEmail())
                ->first();

            if ($nguoiDung) {
                // Cập nhật thông tin Google nếu đã có tài khoản
                $nguoiDung->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'Avatar' => $googleUser->getAvatar(),
                    'CapNhatLuc' => now(),
                ]);
            } else {
                // Tạo tài khoản mới
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

            // Tạo token API
            $token = $nguoiDung->createToken('google_auth_token')->plainTextToken;

            // Redirect về frontend với token
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            
            return redirect()->away($frontendUrl . '/auth/callback?token=' . $token . '&user=' . urlencode(json_encode([
                'NguoiDungID' => $nguoiDung->NguoiDungID,
                'HoTen' => $nguoiDung->HoTen,
                'Email' => $nguoiDung->Email,
                'Avatar' => $nguoiDung->Avatar,
                'LoaiNguoiDung' => $nguoiDung->LoaiNguoiDung,
            ])));

        } catch (\Exception $e) {
            $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
            return redirect()->away($frontendUrl . '/auth/callback?error=' . urlencode($e->getMessage()));
        }
    }

    /**
     * Đăng nhập Google bằng token từ frontend (ID Token)
     */
    public function loginWithGoogleToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'credential' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Xác thực Google ID Token
            $client = new \Google\Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
            $payload = $client->verifyIdToken($request->credential);

            if (!$payload) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google không hợp lệ'
                ], 401);
            }

            $googleId = $payload['sub'];
            $email = $payload['email'];
            $name = $payload['name'];
            $avatar = $payload['picture'] ?? null;

            // Tìm hoặc tạo người dùng
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
     * Đăng nhập Google bằng Authorization Code từ frontend
     */
    public function loginWithGoogleCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Code không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Đổi authorization code lấy access token
            $client = new \Google\Client();
            $client->setClientId(env('GOOGLE_CLIENT_ID'));
            $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
            $client->setRedirectUri('postmessage'); // Quan trọng cho popup flow

            $token = $client->fetchAccessTokenWithAuthCode($request->code);

            if (isset($token['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Lỗi xác thực Google: ' . $token['error_description']
                ], 401);
            }

            // Lấy thông tin user từ Google
            $client->setAccessToken($token);
            $oauth2 = new \Google\Service\Oauth2($client);
            $googleUser = $oauth2->userinfo->get();

            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();
            $name = $googleUser->getName();
            $avatar = $googleUser->getPicture();

            // Tìm hoặc tạo người dùng
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
     * Lấy thông tin người dùng hiện tại
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $request->user()
            ]
        ]);
    }

    /**
     * Đăng xuất
     */
    public function logout(Request $request)
    {
        // Xóa token hiện tại
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Đăng xuất tất cả thiết bị
     */
    public function logoutAll(Request $request)
    {
        // Xóa tất cả token
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Đã đăng xuất khỏi tất cả thiết bị'
        ]);
    }
}
