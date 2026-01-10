<?php
/**
 * ============================================================================
 * DANHMUCMON CONTROLLER - QUẢN LÝ DANH MỤC MÓN ĂN
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DanhMucMon;
use Illuminate\Http\Request;

class DanhMucMonController extends Controller
{
    /**
     * Lấy danh sách danh mục món
     */
    public function index()
    {
        $danhMucs = DanhMucMon::all();

        return response()->json([
            'success' => true,
            'data' => $danhMucs
        ]);
    }

    /**
     * Lấy chi tiết danh mục
     */
    public function show($id)
    {
        $danhMuc = DanhMucMon::with('monAn')->find($id);

        if (!$danhMuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $danhMuc
        ]);
    }

    /**
     * Tạo danh mục mới
     */
    public function store(Request $request)
    {
        $request->validate([
            'TenDanhMuc' => 'required|string|max:100'
        ]);

        $danhMuc = DanhMucMon::create([
            'TenDanhMuc' => $request->TenDanhMuc
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo danh mục thành công',
            'data' => $danhMuc
        ], 201);
    }

    /**
     * Cập nhật danh mục
     */
    public function update(Request $request, $id)
    {
        $danhMuc = DanhMucMon::find($id);

        if (!$danhMuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục'
            ], 404);
        }

        $danhMuc->update($request->only(['TenDanhMuc']));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật danh mục thành công',
            'data' => $danhMuc
        ]);
    }

    /**
     * Xóa danh mục
     */
    public function destroy($id)
    {
        $danhMuc = DanhMucMon::find($id);

        if (!$danhMuc) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy danh mục'
            ], 404);
        }

        $danhMuc->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa danh mục thành công'
        ]);
    }
}
