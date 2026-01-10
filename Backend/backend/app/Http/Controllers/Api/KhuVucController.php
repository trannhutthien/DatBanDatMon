<?php
/**
 * ============================================================================
 * KHU VUC CONTROLLER - QUẢN LÝ KHU VỰC
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KhuVuc;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KhuVucController extends Controller
{
    /**
     * Lấy danh sách khu vực
     */
    public function index(Request $request)
    {
        $query = KhuVuc::with('bans');

        if ($request->has('nha_hang_id')) {
            $query->where('NhaHangID', $request->nha_hang_id);
        }

        $khuVucs = $query->get();

        return response()->json([
            'success' => true,
            'data' => $khuVucs
        ]);
    }

    /**
     * Lấy chi tiết khu vực
     */
    public function show($id)
    {
        $khuVuc = KhuVuc::with('bans')->find($id);

        if (!$khuVuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khu vực'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $khuVuc
        ]);
    }

    /**
     * Tạo khu vực mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NhaHangID' => 'required|exists:nhahang,NhaHangID',
            'TenKhuVuc' => 'required|string|max:100',
            'MoTa' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $khuVuc = KhuVuc::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tạo khu vực thành công',
            'data' => $khuVuc
        ], 201);
    }

    /**
     * Cập nhật khu vực
     */
    public function update(Request $request, $id)
    {
        $khuVuc = KhuVuc::find($id);

        if (!$khuVuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khu vực'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'TenKhuVuc' => 'nullable|string|max:100',
            'MoTa' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $khuVuc->update($request->only(['TenKhuVuc', 'MoTa']));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật khu vực thành công',
            'data' => $khuVuc
        ]);
    }

    /**
     * Xóa khu vực
     */
    public function destroy($id)
    {
        $khuVuc = KhuVuc::find($id);

        if (!$khuVuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy khu vực'
            ], 404);
        }

        $khuVuc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa khu vực thành công'
        ]);
    }
}
