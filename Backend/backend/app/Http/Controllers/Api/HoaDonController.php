<?php
/**
 * ============================================================================
 * HÓA ĐƠN CONTROLLER - API QUẢN LÝ HÓA ĐƠN
 * ============================================================================
 */

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HoaDon;
use App\Models\ChiTietHoaDon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class HoaDonController extends Controller
{
    /**
     * Tạo hóa đơn mới (đặt hàng)
     * POST /api/hoa-don
     */
    public function store(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'NguoiDungID' => 'required|integer|exists:nguoidung,NguoiDungID',
            'NhaHangID' => 'required|integer|exists:nhahang,NhaHangID',
            'HoTen' => 'required|string|max:150',
            'SDT' => 'required|string|max:20',
            'DiaChi' => 'required|string|max:255',
            'GhiChu' => 'nullable|string|max:500',
            'PhuongThucTT' => 'required|string|in:cash,banking,momo',
            'PhiGiaoHang' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.MonAnID' => 'required|integer|exists:monan,MonAnID',
            'items.*.SoLuong' => 'required|integer|min:1',
            'items.*.DonGia' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Map phương thức thanh toán
            $phuongThucMap = [
                'cash' => 'Tiền mặt',
                'banking' => 'Chuyển khoản',
                'momo' => 'MoMo'
            ];

            // Tính tổng tiền
            $tongTienMon = 0;
            foreach ($request->items as $item) {
                $tongTienMon += $item['DonGia'] * $item['SoLuong'];
            }
            $phiGiaoHang = $request->PhiGiaoHang ?? 20000;
            $tongTien = $tongTienMon + $phiGiaoHang;

            // Tạo hóa đơn
            $hoaDon = HoaDon::create([
                'NguoiDungID' => $request->NguoiDungID,
                'NhaHangID' => $request->NhaHangID,
                'NgayLap' => now(),
                'TongTien' => $tongTien,
                'PhuongThucTT' => $phuongThucMap[$request->PhuongThucTT] ?? 'Tiền mặt',
                'TrangThai' => 1, // Chờ xác nhận
                'TaoLuc' => now(),
                'CapNhatLuc' => now(),
                // Lưu thông tin giao hàng vào GhiChu hoặc tạo bảng riêng
            ]);

            // Tạo chi tiết hóa đơn
            foreach ($request->items as $item) {
                ChiTietHoaDon::create([
                    'HoaDonID' => $hoaDon->HoaDonID,
                    'MonAnID' => $item['MonAnID'],
                    'SoLuong' => $item['SoLuong'],
                    'DonGia' => $item['DonGia'],
                ]);
            }

            DB::commit();

            // Load relationships
            $hoaDon->load(['chiTietHoaDon.monAn', 'nguoiDung', 'nhaHang']);

            return response()->json([
                'success' => true,
                'message' => 'Đặt hàng thành công!',
                'data' => [
                    'hoaDon' => $hoaDon,
                    'thongTinGiaoHang' => [
                        'HoTen' => $request->HoTen,
                        'SDT' => $request->SDT,
                        'DiaChi' => $request->DiaChi,
                        'GhiChu' => $request->GhiChu,
                    ]
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đặt hàng',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách hóa đơn của user
     * GET /api/hoa-don
     */
    public function index(Request $request)
    {
        $query = HoaDon::with(['chiTietHoaDon.monAn', 'nhaHang']);

        // Filter theo user
        if ($request->has('nguoi_dung_id')) {
            $query->where('NguoiDungID', $request->nguoi_dung_id);
        }

        // Filter theo trạng thái
        if ($request->has('trang_thai')) {
            $query->where('TrangThai', $request->trang_thai);
        }

        // Sắp xếp mới nhất
        $query->orderBy('TaoLuc', 'desc');

        // Phân trang
        $perPage = $request->per_page ?? 10;
        $hoaDons = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $hoaDons
        ]);
    }

    /**
     * Lấy chi tiết hóa đơn
     * GET /api/hoa-don/{id}
     */
    public function show($id)
    {
        $hoaDon = HoaDon::with(['chiTietHoaDon.monAn', 'nguoiDung', 'nhaHang'])
            ->find($id);

        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $hoaDon
        ]);
    }

    /**
     * Cập nhật trạng thái hóa đơn
     * PUT /api/hoa-don/{id}/status
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'TrangThai' => 'required|integer|in:1,2,3'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $hoaDon = HoaDon::find($id);
        if (!$hoaDon) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy hóa đơn'
            ], 404);
        }

        $hoaDon->TrangThai = $request->TrangThai;
        $hoaDon->CapNhatLuc = now();
        $hoaDon->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật trạng thái thành công',
            'data' => $hoaDon
        ]);
    }
}
