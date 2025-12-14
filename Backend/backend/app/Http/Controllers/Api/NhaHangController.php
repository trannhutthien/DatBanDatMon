<?php
/**
 * ============================================================================
 * NHAHANG CONTROLLER - QUẢN LÝ NHÀ HÀNG
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NhaHang;
use Illuminate\Http\Request;

class NhaHangController extends Controller
{
    /**
     * Lấy danh sách tất cả nhà hàng
     * GET /api/nha-hang
     */
    public function index(Request $request)
    {
        $query = NhaHang::query();

        // Tìm kiếm theo tên, địa chỉ, SĐT
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('TenNhaHang', 'like', "%{$search}%")
                  ->orWhere('DiaChi', 'like', "%{$search}%")
                  ->orWhere('SDT', 'like', "%{$search}%");
            });
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai') && $request->trang_thai !== null) {
            $query->where('TrangThai', $request->trang_thai);
        }

        // Phân trang
        $perPage = $request->get('per_page', 10);
        $nhaHangs = $query->orderBy('NhaHangID', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $nhaHangs
        ]);
    }

    /**
     * Lấy chi tiết nhà hàng
     * GET /api/nha-hang/{id}
     */
    public function show($id)
    {
        $nhaHang = NhaHang::find($id);

        if (!$nhaHang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhà hàng'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $nhaHang
        ]);
    }

    /**
     * Tạo nhà hàng mới
     * POST /api/nha-hang
     */
    public function store(Request $request)
    {
        $request->validate([
            'TenNhaHang' => 'required|string|max:200',
            'DiaChi' => 'nullable|string|max:500',
            'SDT' => 'nullable|string|max:20'
        ]);

        try {
            $nhaHang = NhaHang::create([
                'TenNhaHang' => $request->TenNhaHang,
                'DiaChi' => $request->DiaChi,
                'SDT' => $request->SDT,
                'TrangThai' => 1
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo nhà hàng: ' . $e->getMessage()
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tạo nhà hàng thành công',
            'data' => $nhaHang
        ], 201);
    }

    /**
     * Cập nhật nhà hàng
     * PUT /api/nha-hang/{id}
     */
    public function update(Request $request, $id)
    {
        $nhaHang = NhaHang::find($id);

        if (!$nhaHang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhà hàng'
            ], 404);
        }

        $nhaHang->update([
            'TenNhaHang' => $request->TenNhaHang ?? $nhaHang->TenNhaHang,
            'DiaChi' => $request->DiaChi ?? $nhaHang->DiaChi,
            'SDT' => $request->SDT ?? $nhaHang->SDT,
            'Email' => $request->Email ?? $nhaHang->Email,
            'MoTa' => $request->MoTa ?? $nhaHang->MoTa,
            'TrangThai' => $request->TrangThai ?? $nhaHang->TrangThai,
            'CapNhatLuc' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật nhà hàng thành công',
            'data' => $nhaHang
        ]);
    }

    /**
     * Xóa nhà hàng
     * DELETE /api/nha-hang/{id}
     */
    public function destroy($id)
    {
        $nhaHang = NhaHang::find($id);

        if (!$nhaHang) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nhà hàng'
            ], 404);
        }

        $nhaHang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa nhà hàng thành công'
        ]);
    }

    /**
     * Thống kê nhà hàng
     * GET /api/nha-hang/stats
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => NhaHang::count(),
                'hoat_dong' => NhaHang::where('TrangThai', 1)->count(),
                'ngung_hoat_dong' => NhaHang::where('TrangThai', 0)->count()
            ]
        ]);
    }
}

