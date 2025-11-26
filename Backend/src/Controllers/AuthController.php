<?php
/**
 * Auth Controller
 * Xử lý đăng ký, đăng nhập
 */

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Core\Validator;

class AuthController
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Đăng nhập bằng Google
     */
    public function googleLogin(): void
    {
        $request = new Request();
        $credential = $request->get('credential');
        $clientId = $request->get('client_id');

        if (!$credential) {
            Response::error('Google credential is required', 400);
            return;
        }

        try {
            // Verify Google token
            $userInfo = $this->verifyGoogleToken($credential);
            
            if (!$userInfo) {
                Response::error('Invalid Google token', 401);
                return;
            }

            // Kiểm tra user đã tồn tại chưa
            $user = $this->db->fetch(
                'SELECT * FROM khachhang WHERE Email = ? AND LoaiKH = ?',
                [$userInfo['email'], 'google']
            );

            if (!$user) {
                // Tạo user mới
                $this->db->query(
                    'INSERT INTO khachhang (HoTen, Email, LoaiKH, TaoLuc) VALUES (?, ?, ?, NOW())',
                    [
                        $userInfo['name'],
                        $userInfo['email'],
                        'google'
                    ]
                );
                
                $userId = $this->db->lastInsertId();
                
                $user = $this->db->fetch('SELECT * FROM khachhang WHERE KhachHangID = ?', [$userId]);
            }

            // Tạo JWT token
            $token = $this->generateToken($user);

            Response::success([
                'token' => $token,
                'user' => [
                    'id' => $user['KhachHangID'],
                    'name' => $user['HoTen'],
                    'email' => $user['Email'],
                    'avatar' => null,
                ]
            ], 'Đăng nhập Google thành công');

        } catch (\Exception $e) {
            Response::serverError('Google login failed: ' . $e->getMessage());
        }
    }

    /**
     * Đăng ký thông thường
     */
    public function register(): void
    {
        $request = new Request();
        $validator = new Validator();

        $rules = [
            'full_name' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:6',
            'phone' => 'required|min:10',
        ];

        if (!$validator->validate($request->all(), $rules)) {
            Response::error('Validation failed', 422, $validator->errors());
            return;
        }

        $data = $request->all();

        // Kiểm tra email đã tồn tại
        $existingUser = $this->db->fetch(
            'SELECT KhachHangID FROM khachhang WHERE Email = ?',
            [$data['email']]
        );

        if ($existingUser) {
            Response::error('Email already exists', 400);
            return;
        }

        try {
            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

            // Insert user
            $this->db->query(
                'INSERT INTO khachhang (HoTen, Email, MatKhau, SoDienThoai, GioiTinh, DiaChi, LoaiKH, TaoLuc) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, NOW())',
                [
                    $data['full_name'],
                    $data['email'],
                    $hashedPassword,
                    $data['phone'] ?? null,
                    $data['gender'] ?? null,
                    $data['address'] ?? null,
                    'local'
                ]
            );

            $userId = $this->db->lastInsertId();
            $user = $this->db->fetch('SELECT * FROM khachhang WHERE KhachHangID = ?', [$userId]);

            // Generate token
            $token = $this->generateToken($user);

            Response::success([
                'token' => $token,
                'user' => [
                    'id' => $user['KhachHangID'],
                    'name' => $user['HoTen'],
                    'email' => $user['Email'],
                ]
            ], 'Đăng ký khách hàng thành công', 201);

        } catch (\Exception $e) {
            Response::serverError('Registration failed: ' . $e->getMessage());
        }
    }

    /**
     * Đăng nhập thông thường
     */
    public function login(): void
    {
        $request = new Request();
        $email = $request->get('email');
        $password = $request->get('password');

        if (!$email || !$password) {
            Response::error('Email and password are required', 400);
            return;
        }

        $user = $this->db->fetch(
            'SELECT * FROM khachhang WHERE Email = ? AND LoaiKH = ?',
            [$email, 'local']
        );

        if (!$user || !password_verify($password, $user['MatKhau'] ?? '')) {
            Response::error('Invalid credentials', 401);
            return;
        }

        $token = $this->generateToken($user);

        Response::success([
            'token' => $token,
            'user' => [
                'id' => $user['KhachHangID'],
                'name' => $user['HoTen'],
                'email' => $user['Email'],
            ]
        ], 'Login successful');
    }

    /**
     * Verify Google Token bằng cách gọi Google API
     */
    private function verifyGoogleToken(string $token): ?array
    {
        // Gọi Google API để verify token
        $url = 'https://oauth2.googleapis.com/tokeninfo?access_token=' . $token;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) {
            return null;
        }

        $data = json_decode($response, true);

        if (isset($data['error'])) {
            return null;
        }

        // Lấy thông tin user từ Google
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' . $token;
        
        $ch = curl_init($userInfoUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userResponse = curl_exec($ch);
        curl_close($ch);

        if (!$userResponse) {
            return null;
        }

        return json_decode($userResponse, true);
    }

    /**
     * Tạo JWT token đơn giản
     */
    private function generateToken(array $user): string
    {
        $config = require __DIR__ . '/../../config/app.php';
        $secret = $config['jwt']['secret_key'];
        $expiration = time() + $config['jwt']['expiration'];

        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode([
            'user_id' => $user['KhachHangID'],
            'email' => $user['Email'],
            'exp' => $expiration
        ]);

        $base64UrlHeader = $this->base64UrlEncode($header);
        $base64UrlPayload = $this->base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
        $base64UrlSignature = $this->base64UrlEncode($signature);

        return $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
    }

    private function base64UrlEncode($data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
