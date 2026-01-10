<?php
/**
 * ============================================================================
 * BAN CONTROLLER - QUẢN LÝ BÀN
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ban;
use App\Models\KhuVuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BanController extends Controller
{
    /**
     * Lấy danh sách bàn (có thể lọc theo khu vực, trạng thái)
     */
    public function index(Request $request)
    {
        $query = Ban::with(['khuVuc', 'nhaHang']);

        // Lọc theo khu vực
        if ($request->has('khu_vuc_id')) {
            $query->where('KhuVucID', $request->khu_vuc_id);
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->trang_thai);
        }

        // Lọc theo nhà hàng
        if ($request->has('nha_hang_id')) {
            $query->where('NhaHangID', $request->nha_hang_id);
        }

        $bans = $query->orderBy('KhuVucID')->orderBy('SoBan')->get();

        return response()->json([
            'success' => true,
            'data' => $bans
        ]);
    }

    /**
     * Lấy chi tiết bàn
     */
    public function show($id)
    {
        $ban = Ban::with(['khuVuc', 'nhaHang', 'hoaDons' => function($q) {
            $q->where('TrangThai', 1)->latest();
        }])->find($id);

        if (!$ban) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bàn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $ban
        ]);
    }

    /**
     * Tạo bàn mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NhaHangID' => 'required|exists:nhahang,NhaHangID',
            'KhuVucID' => 'nullable|exists:khuvuc,KhuVucID',
            'SoBan' => 'required|integer|min:1',
            'SoGhe' => 'required|integer|min:1|max:20',
            'TrangThai' => 'nullable|integer|in:1,2,3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $ban = Ban::create([
            'NhaHangID' => $request->NhaHangID,
            'KhuVucID' => $request->KhuVucID,
            'SoBan' => $request->SoBan,
            'SoGhe' => $request->SoGhe,
            'TrangThai' => $request->TrangThai ?? 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo bàn thành công',
            'data' => $ban
        ], 201);
    }

    /**
     * Cập nhật bàn
     */
    public function update(Request $request, $id)
    {
        $ban = Ban::find($id);

        if (!$ban) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bàn'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'KhuVucID' => 'nullable|exists:khuvuc,KhuVucID',
            'SoBan' => 'nullable|integer|min:1',
            'SoGhe' => 'nullable|integer|min:1|max:20',
            'TrangThai' => 'nullable|integer|in:1,2,3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $ban->update($request->only(['KhuVucID', 'SoBan', 'SoGhe', 'TrangThai']));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật bàn thành công',
            'data' => $ban
        ]);
    }

    /**
     * Cập nhật trạng thái bàn
     */
    public function updateStatus(Request $request, $id)
    {
        $ban = Ban::find($id);

        if (!$ban) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bàn'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'TrangThai' => 'required|integer|in:1,2,3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Trạng thái không hợp lệ'
            ], 422);
        }

        $ban->TrangThai = $request->TrangThai;
        $ban->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $ban
        ]);
    }

    /**
     * Xóa bàn
     */
    public function destroy($id)
    {
        $ban = Ban::find($id);

        if (!$ban) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy bàn'
            ], 404);
        }

        $ban->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa bàn thành công'
        ]);
    }

    /**
     * Thống kê bàn
     */
    public function stats(Request $request)
    {
        $nhaHangId = $request->nha_hang_id ?? 1;

        $stats = [
            'total' => Ban::where('NhaHangID', $nhaHangId)->count(),
            'trong' => Ban::where('NhaHangID', $nhaHangId)->where('TrangThai', 1)->count(),
            'daDat' => Ban::where('NhaHangID', $nhaHangId)->where('TrangThai', 2)->count(),
            'dangDung' => Ban::where('NhaHangID', $nhaHangId)->where('TrangThai', 3)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
