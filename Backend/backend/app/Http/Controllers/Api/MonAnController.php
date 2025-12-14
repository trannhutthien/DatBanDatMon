<?php
/**
 * ============================================================================
 * MONAN CONTROLLER - QUẢN LÝ MÓN ĂN
 * ============================================================================
 * 
 * Controller xử lý các API liên quan đến món ăn:
 * - Lấy danh sách món ăn (có phân trang)
 * - Lấy món ăn phổ biến
 * - Lấy chi tiết món ăn
 * - Tìm kiếm món ăn
 * 
 * SỬ DỤNG:
 * - Model: MonAn, NhaHang
 * - Routes: routes/api.php
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MonAn;
use Illuminate\Http\Request;

class MonAnController extends Controller
{
    /**
     * ========================================================================
     * LẤY DANH SÁCH MÓN ĂN
     * ========================================================================
     * 
     * ROUTE: GET /api/mon-an
     * 
     * QUERY PARAMS:
     * - per_page: Số món mỗi trang (mặc định 12)
     * - nha_hang_id: Lọc theo nhà hàng
     * - search: Tìm kiếm theo tên
     * 
     * OUTPUT:
     * {
     *   "success": true,
     *   "data": {
     *     "items": [...],
     *     "pagination": { current_page, last_page, per_page, total }
     *   }
     * }
     */
    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 12);

        $query = MonAn::with('nhaHang');  // Eager load nhà hàng

        // Lọc theo trạng thái (cho admin)
        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->trang_thai);
        }

        // Lọc theo nhà hàng
        if ($request->has('nha_hang_id')) {
            $query->where('NhaHangID', $request->nha_hang_id);
        }

        // Tìm kiếm theo tên
        if ($request->has('search')) {
            $query->where('TenMon', 'like', '%' . $request->search . '%');
        }

        $monAn = $query->orderBy('TaoLuc', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $monAn->items(),
                'pagination' => [
                    'current_page' => $monAn->currentPage(),
                    'last_page' => $monAn->lastPage(),
                    'per_page' => $monAn->perPage(),
                    'total' => $monAn->total()
                ]
            ]
        ]);
    }

    /**
     * Thống kê món ăn
     * GET /api/mon-an/stats
     */
    public function stats()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total' => MonAn::count(),
                'con_mon' => MonAn::where('TrangThai', 1)->count(),
                'het_mon' => MonAn::where('TrangThai', 0)->count()
            ]
        ]);
    }

    /**
     * Tạo món ăn mới
     * POST /api/mon-an
     */
    public function store(Request $request)
    {
        $request->validate([
            'TenMon' => 'required|string|max:200',
            'DonGia' => 'required|numeric|min:0',
            'NhaHangID' => 'nullable|integer'
        ]);

        try {
            $monAn = MonAn::create([
                'TenMon' => $request->TenMon,
                'DonGia' => $request->DonGia,
                'NhaHangID' => $request->NhaHangID,
                'HinhAnh' => $request->HinhAnh,
                'MoTa' => $request->MoTa,
                'TrangThai' => 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo món ăn thành công',
                'data' => $monAn
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo món ăn: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật món ăn
     * PUT /api/mon-an/{id}
     */
    public function update(Request $request, $id)
    {
        $monAn = MonAn::find($id);

        if (!$monAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn'
            ], 404);
        }

        $monAn->update([
            'TenMon' => $request->TenMon ?? $monAn->TenMon,
            'DonGia' => $request->DonGia ?? $monAn->DonGia,
            'NhaHangID' => $request->NhaHangID ?? $monAn->NhaHangID,
            'HinhAnh' => $request->HinhAnh ?? $monAn->HinhAnh,
            'MoTa' => $request->MoTa ?? $monAn->MoTa,
            'TrangThai' => $request->TrangThai ?? $monAn->TrangThai
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật món ăn thành công',
            'data' => $monAn
        ]);
    }

    /**
     * Xóa món ăn
     * DELETE /api/mon-an/{id}
     */
    public function destroy($id)
    {
        $monAn = MonAn::find($id);

        if (!$monAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn'
            ], 404);
        }

        $monAn->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa món ăn thành công'
        ]);
    }

    /**
     * ========================================================================
     * LẤY MÓN ĂN PHỔ BIẾN
     * ========================================================================
     * 
     * ROUTE: GET /api/mon-an/popular
     * 
     * QUERY PARAMS:
     * - limit: Số món trả về (mặc định 6)
     * 
     * OUTPUT:
     * {
     *   "success": true,
     *   "data": [
     *     {
     *       "MonAnID": 1,
     *       "TenMon": "Phở Bò",
     *       "DonGia": 65000,
     *       "HinhAnh": "...",
     *       "nha_hang": {
     *         "NhaHangID": 1,
     *         "TenNhaHang": "Phở Hà Nội",
     *         "DiaChi": "123 Nguyễn Huệ"
     *       }
     *     }
     *   ]
     * }
     */
    public function popular(Request $request)
    {
        $limit = $request->get('limit', 6);
        
        // Lấy món ăn phổ biến (có thể dựa trên số lần đặt, rating, etc.)
        // Hiện tại lấy random các món còn
        $monAn = MonAn::with('nhaHang')
            ->conMon()
            ->inRandomOrder()
            ->limit($limit)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $monAn
        ]);
    }

    /**
     * ========================================================================
     * LẤY MÓN ĂN MỚI NHẤT
     * ========================================================================
     * 
     * ROUTE: GET /api/mon-an/latest
     * 
     * QUERY PARAMS:
     * - limit: Số món trả về (mặc định 10)
     * 
     * OUTPUT:
     * {
     *   "success": true,
     *   "data": [...]
     * }
     * 
     * Sắp xếp theo TaoLuc (ngày thêm) giảm dần
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        // Lấy món ăn mới nhất theo ngày tạo
        $monAn = MonAn::with('nhaHang')
            ->conMon()
            ->orderBy('TaoLuc', 'desc')  // Sắp xếp theo ngày tạo mới nhất
            ->limit($limit)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $monAn
        ]);
    }

    /**
     * ========================================================================
     * LẤY CHI TIẾT MÓN ĂN
     * ========================================================================
     * 
     * ROUTE: GET /api/mon-an/{id}
     * 
     * OUTPUT:
     * {
     *   "success": true,
     *   "data": {
     *     "MonAnID": 1,
     *     "TenMon": "Phở Bò",
     *     ...
     *     "nha_hang": { ... }
     *   }
     * }
     */
    public function show($id)
    {
        $monAn = MonAn::with('nhaHang')->find($id);
        
        if (!$monAn) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy món ăn'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => $monAn
        ]);
    }
}
