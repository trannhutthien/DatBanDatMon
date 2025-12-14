<?php
/**
 * ============================================================================
 * NGUOIDUNG CONTROLLER - QUẢN LÝ NGƯỜI DÙNG
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NguoiDung;
use Illuminate\Http\Request;

class NguoiDungController extends Controller
{
    /**
     * Lấy danh sách tất cả người dùng
     * GET /api/nguoi-dung
     */
    public function index(Request $request)
    {
        $query = NguoiDung::query();

        // Tìm kiếm theo tên hoặc email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('HoTen', 'like', "%{$search}%")
                  ->orWhere('Email', 'like', "%{$search}%")
                  ->orWhere('SDT', 'like', "%{$search}%");
            });
        }

        // Lọc theo loại người dùng
        if ($request->has('loai') && $request->loai) {
            $query->where('LoaiNguoiDung', $request->loai);
        }

        // Phân trang
        $perPage = $request->get('per_page', 10);
        $nguoiDungs = $query->orderBy('NguoiDungID', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $nguoiDungs
        ]);
    }

    /**
     * Lấy chi tiết người dùng
     * GET /api/nguoi-dung/{id}
     */
    public function show($id)
    {
        $nguoiDung = NguoiDung::find($id);

        if (!$nguoiDung) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $nguoiDung
        ]);
    }

    /**
     * Tạo người dùng mới
     * POST /api/nguoi-dung
     */
    public function store(Request $request)
    {
        $request->validate([
            'HoTen' => 'required|string|max:255',
            'Email' => 'required|email|unique:nguoidung,Email',
            'MatKhau' => 'required|string|min:6',
            'LoaiNguoiDung' => 'required|integer|in:1,2,3'
        ]);

        $nguoiDung = NguoiDung::create([
            'HoTen' => $request->HoTen,
            'Email' => $request->Email,
            'MatKhau' => $request->MatKhau, // Lưu trực tiếp (demo)
            'SDT' => $request->SDT,
            'LoaiNguoiDung' => $request->LoaiNguoiDung,
            'DiaChi' => $request->DiaChi,
            'GhiChu' => $request->GhiChu
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo người dùng thành công',
            'data' => $nguoiDung
        ], 201);
    }

    /**
     * Cập nhật người dùng
     * PUT /api/nguoi-dung/{id}
     */
    public function update(Request $request, $id)
    {
        $nguoiDung = NguoiDung::find($id);

        if (!$nguoiDung) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        $nguoiDung->update([
            'HoTen' => $request->HoTen ?? $nguoiDung->HoTen,
            'Email' => $request->Email ?? $nguoiDung->Email,
            'SDT' => $request->SDT ?? $nguoiDung->SDT,
            'DiaChi' => $request->DiaChi ?? $nguoiDung->DiaChi,
            'LoaiNguoiDung' => $request->LoaiNguoiDung ?? $nguoiDung->LoaiNguoiDung,
            'GhiChu' => $request->GhiChu ?? $nguoiDung->GhiChu,
            'CapNhatLuc' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thành công',
            'data' => $nguoiDung
        ]);
    }

    /**
     * Xóa người dùng
     * DELETE /api/nguoi-dung/{id}
     */
    public function destroy($id)
    {
        $nguoiDung = NguoiDung::find($id);

        if (!$nguoiDung) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng'
            ], 404);
        }

        $nguoiDung->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa người dùng thành công'
        ]);
    }

    /**
     * Thống kê người dùng
     * GET /api/nguoi-dung/stats
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => NguoiDung::count(),
                'admin' => NguoiDung::admin()->count(),
                'nhan_vien' => NguoiDung::nhanVien()->count(),
                'khach_hang' => NguoiDung::khachHang()->count(),
            ]
        ]);
    }
}

