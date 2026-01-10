/**
 * ============================================================================
 * HÓA ĐƠN SERVICE - GỌI API ĐẶT HÀNG
 * ============================================================================
 */

import apiClient from './api'

export interface ChiTietHoaDon {
  MonAnID: number
  SoLuong: number
  DonGia: number
}

export interface CreateHoaDonRequest {
  NguoiDungID: number
  NhaHangID: number
  HoTen: string
  SDT: string
  DiaChi: string
  GhiChu?: string
  PhuongThucTT: 'cash' | 'banking' | 'momo'
  PhiGiaoHang?: number
  items: ChiTietHoaDon[]
}

export interface HoaDon {
  HoaDonID: number
  NguoiDungID: number
  NhaHangID: number
  NgayLap: string
  TongTien: number
  PhuongThucTT: string
  TrangThai: number
  TaoLuc: string
  CapNhatLuc: string
  chi_tiet_hoa_don?: any[]
  nguoi_dung?: any
  nha_hang?: any
}

class HoaDonService {
  /**
   * Tạo hóa đơn mới (đặt hàng)
   */
  async create(data: CreateHoaDonRequest): Promise<{ success: boolean; message: string; data: any }> {
    const response = await apiClient.post('/hoa-don', data)
    return response.data
  }

  /**
   * Lấy danh sách hóa đơn của user
   */
  async getByUser(nguoiDungId: number, params?: { trang_thai?: number; per_page?: number }): Promise<any> {
    const response = await apiClient.get('/hoa-don', {
      params: { nguoi_dung_id: nguoiDungId, ...params }
    })
    return response.data
  }

  /**
   * Lấy chi tiết hóa đơn
   */
  async getById(id: number): Promise<{ success: boolean; data: HoaDon }> {
    const response = await apiClient.get(`/hoa-don/${id}`)
    return response.data
  }

  /**
   * Cập nhật trạng thái hóa đơn
   */
  async updateStatus(id: number, trangThai: number): Promise<{ success: boolean; message: string }> {
    const response = await apiClient.put(`/hoa-don/${id}/status`, { TrangThai: trangThai })
    return response.data
  }
}

const hoaDonService = new HoaDonService()
export default hoaDonService
