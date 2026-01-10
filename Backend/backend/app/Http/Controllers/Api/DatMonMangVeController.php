<?php
/**
 * ============================================================================
 * DATMON_MANGVE CONTROLLER - QUẢN LÝ ĐẶT MÓN MANG VỀ
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DatMonMangVe;
use App\Models\DatMonMangVeChiTiet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DatMonMangVeController extends Controller
{
    /**
     * Lấy danh sách đơn đặt món
     */
    public function index(Request $request)
    {
        $query = DatMonMangVe::with(['nguoiDung', 'nhaHang', 'chiTiet.monAn']);

        // Lọc theo người dùng
        if ($request->has('nguoi_dung_id')) {
            $query->where('NguoiDungID', $request->nguoi_dung_id);
        }

        // Lọc theo nhà hàng
        if ($request->has('nha_hang_id')) {
            $query->where('NhaHangID', $request->nha_hang_id);
        }

        // Lọc theo trạng thái
        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->trang_thai);
        }

        $datMons = $query->orderBy('TaoLuc', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $datMons
        ]);
    }

    /**
     * Lấy chi tiết đơn đặt món
     */
    public function show($id)
    {
        $datMon = DatMonMangVe::with(['nguoiDung', 'nhaHang', 'chiTiet.monAn'])->find($id);

        if (!$datMon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn đặt món'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $datMon
        ]);
    }

    /**
     * Tạo đơn đặt món mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NhaHangID' => 'required|exists:nhahang,NhaHangID',
            'HoTen' => 'required|string|max:150',
            'SDT' => 'required|string|max:20',
            'DiaChi' => 'required|string|max:255',
            'ThoiGianLay' => 'required|date',
            'GhiChu' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.MonAnID' => 'required|exists:monan,MonAnID',
            'items.*.SoLuong' => 'required|integer|min:1',
            'items.*.DonGia' => 'required|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Tính tổng tiền
            $tongTien = 0;
            foreach ($request->items as $item) {
                $tongTien += $item['SoLuong'] * $item['DonGia'];
            }

            // Tạo đơn đặt món
            $datMon = DatMonMangVe::create([
                'NguoiDungID' => $request->NguoiDungID ?? null,
                'NhaHangID' => $request->NhaHangID,
                'HoTen' => $request->HoTen,
                'SDT' => $request->SDT,
                'DiaChi' => $request->DiaChi,
                'ThoiGianLay' => $request->ThoiGianLay,
                'TongTien' => $tongTien,
                'GhiChu' => $request->GhiChu,
                'TrangThai' => DatMonMangVe::TRANG_THAI_CHO_XAC_NHAN,
                'TaoLuc' => now(),
                'CapNhatLuc' => now()
            ]);

            // Lưu chi tiết món
            foreach ($request->items as $item) {
                DatMonMangVeChiTiet::create([
                    'DatMonID' => $datMon->DatMonID,
                    'MonAnID' => $item['MonAnID'],
                    'SoLuong' => $item['SoLuong'],
                    'DonGia' => $item['DonGia'],
                    'GhiChu' => $item['GhiChu'] ?? null,
                    'TaoLuc' => now()
                ]);
            }

            DB::commit();

            // Load lại với relations
            $datMon->load(['nguoiDung', 'nhaHang', 'chiTiet.monAn']);

            return response()->json([
                'success' => true,
                'message' => 'Đặt món thành công',
                'data' => $datMon
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Đặt món thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái đơn
     */
    public function updateStatus(Request $request, $id)
    {
        $datMon = DatMonMangVe::find($id);

        if (!$datMon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn đặt món'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'TrangThai' => 'required|integer|in:1,2,3,4,5,6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Trạng thái không hợp lệ'
            ], 422);
        }

        $datMon->TrangThai = $request->TrangThai;
        $datMon->CapNhatLuc = now();
        $datMon->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $datMon
        ]);
    }

    /**
     * Hủy đơn đặt món
     */
    public function cancel($id)
    {
        $datMon = DatMonMangVe::find($id);

        if (!$datMon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn đặt món'
            ], 404);
        }

        if ($datMon->TrangThai == DatMonMangVe::TRANG_THAI_HUY) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đã bị hủy trước đó'
            ], 400);
        }

        if ($datMon->TrangThai >= DatMonMangVe::TRANG_THAI_DANG_CHUAN_BI) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể hủy đơn đang được chuẩn bị'
            ], 400);
        }

        $datMon->TrangThai = DatMonMangVe::TRANG_THAI_HUY;
        $datMon->CapNhatLuc = now();
        $datMon->save();

        return response()->json([
            'success' => true,
            'message' => 'Hủy đơn thành công'
        ]);
    }
}
