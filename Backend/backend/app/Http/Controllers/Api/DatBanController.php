<?php
/**
 * ============================================================================
 * DATBAN CONTROLLER - QUẢN LÝ ĐẶT BÀN
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DatBan;
use App\Models\DatBanDatMon;
use App\Models\Ban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DatBanController extends Controller
{
    /**
     * Lấy danh sách đặt bàn
     */
    public function index(Request $request)
    {
        $query = DatBan::with(['nguoiDung', 'nhaHang', 'ban', 'chiTietMon.monAn']);

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

        $datBans = $query->orderBy('TaoLuc', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $datBans
        ]);
    }

    /**
     * Lấy chi tiết đặt bàn
     */
    public function show($id)
    {
        $datBan = DatBan::with(['nguoiDung', 'nhaHang', 'ban', 'chiTietMon.monAn'])->find($id);

        if (!$datBan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn đặt bàn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $datBan
        ]);
    }

    /**
     * Tạo đơn đặt bàn mới
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NguoiDungID' => 'required|exists:nguoidung,NguoiDungID',
            'NhaHangID' => 'required|exists:nhahang,NhaHangID',
            'BanID' => 'required|exists:ban,BanID',
            'ThoiGianDen' => 'required|date',
            'SoNguoi' => 'required|integer|min:1',
            'GhiChu' => 'nullable|string|max:255',
            'items' => 'nullable|array',
            'items.*.MonAnID' => 'required_with:items|exists:monan,MonAnID',
            'items.*.SoLuong' => 'required_with:items|integer|min:1',
            'items.*.DonGia' => 'required_with:items|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        // Kiểm tra bàn có trống không
        $ban = Ban::find($request->BanID);
        if ($ban->TrangThai != 1) {
            return response()->json([
                'success' => false,
                'message' => 'Bàn này hiện không trống'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Tạo đơn đặt bàn
            $datBan = DatBan::create([
                'NguoiDungID' => $request->NguoiDungID,
                'NhaHangID' => $request->NhaHangID,
                'BanID' => $request->BanID,
                'ThoiGianDen' => $request->ThoiGianDen,
                'SoNguoi' => $request->SoNguoi,
                'GhiChu' => $request->GhiChu,
                'TrangThai' => DatBan::TRANG_THAI_CHO_XAC_NHAN,
                'TaoLuc' => now(),
                'CapNhatLuc' => now()
            ]);

            // Lưu các món ăn đặt trước (nếu có)
            if ($request->has('items') && is_array($request->items)) {
                foreach ($request->items as $item) {
                    DatBanDatMon::create([
                        'DatBanID' => $datBan->DatBanID,
                        'MonAnID' => $item['MonAnID'],
                        'SoLuong' => $item['SoLuong'],
                        'DonGia' => $item['DonGia'],
                        'GhiChu' => $item['GhiChu'] ?? null,
                        'TaoLuc' => now()
                    ]);
                }
            }

            // Cập nhật trạng thái bàn thành "Đã đặt"
            $ban->TrangThai = 2;
            $ban->save();

            DB::commit();

            // Load lại với relations
            $datBan->load(['nguoiDung', 'nhaHang', 'ban', 'chiTietMon.monAn']);

            return response()->json([
                'success' => true,
                'message' => 'Đặt bàn thành công',
                'data' => $datBan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Đặt bàn thất bại: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cập nhật trạng thái đặt bàn
     */
    public function updateStatus(Request $request, $id)
    {
        $datBan = DatBan::find($id);

        if (!$datBan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn đặt bàn'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'TrangThai' => 'required|integer|in:1,2,3,4,5'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Trạng thái không hợp lệ'
            ], 422);
        }

        $oldStatus = $datBan->TrangThai;
        $newStatus = $request->TrangThai;

        $datBan->TrangThai = $newStatus;
        $datBan->CapNhatLuc = now();
        $datBan->save();

        // Cập nhật trạng thái bàn
        $ban = $datBan->ban;
        if ($ban) {
            if ($newStatus == DatBan::TRANG_THAI_HUY || $newStatus == DatBan::TRANG_THAI_HOAN_THANH) {
                // Hủy hoặc hoàn thành -> bàn trống
                $ban->TrangThai = 1;
            } elseif ($newStatus == DatBan::TRANG_THAI_DA_DEN) {
                // Khách đến -> bàn đang dùng
                $ban->TrangThai = 3;
            } elseif ($newStatus == DatBan::TRANG_THAI_DA_XAC_NHAN) {
                // Xác nhận -> bàn đã đặt
                $ban->TrangThai = 2;
            }
            $ban->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $datBan
        ]);
    }

    /**
     * Hủy đặt bàn
     */
    public function cancel($id)
    {
        $datBan = DatBan::find($id);

        if (!$datBan) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn đặt bàn'
            ], 404);
        }

        if ($datBan->TrangThai == DatBan::TRANG_THAI_HUY) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn đặt bàn đã bị hủy trước đó'
            ], 400);
        }

        $datBan->TrangThai = DatBan::TRANG_THAI_HUY;
        $datBan->CapNhatLuc = now();
        $datBan->save();

        // Cập nhật bàn về trạng thái trống
        $ban = $datBan->ban;
        if ($ban) {
            $ban->TrangThai = 1;
            $ban->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Hủy đặt bàn thành công'
        ]);
    }
}
