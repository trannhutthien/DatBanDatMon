/**
 * ============================================================================
 * NHÀ HÀNG SERVICE - Gọi API quản lý nhà hàng
 * ============================================================================
 */

import apiClient from './api'

/**
 * Interface cho Nhà hàng
 */
export interface NhaHang {
  NhaHangID: number
  TenNhaHang: string
  DiaChi: string | null
  SDT: string | null
  Email: string | null
  MoTa: string | null
  HinhAnh: string | null
  TrangThai: number
  TaoLuc?: string
  CapNhatLuc?: string
}

export interface NhaHangResponse {
  success: boolean
  data: {
    data: NhaHang[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface NhaHangStats {
  total: number
  hoat_dong: number
  ngung_hoat_dong: number
}

class NhaHangService {
  /**
   * Lấy danh sách nhà hàng
   */
  async getAll(params?: { search?: string; trang_thai?: number; per_page?: number; page?: number }): Promise<NhaHangResponse> {
    const response = await apiClient.get<NhaHangResponse>('/nha-hang', { params })
    return response.data
  }

  /**
   * Lấy chi tiết nhà hàng
   */
  async getById(id: number): Promise<{ success: boolean; data: NhaHang }> {
    const response = await apiClient.get(`/nha-hang/${id}`)
    return response.data
  }

  /**
   * Tạo nhà hàng mới
   */
  async create(data: { TenNhaHang: string; DiaChi?: string; SDT?: string }): Promise<{ success: boolean; message: string; data: NhaHang }> {
    const response = await apiClient.post('/nha-hang', data)
    return response.data
  }

  /**
   * Cập nhật nhà hàng
   */
  async update(id: number, data: Partial<NhaHang>): Promise<{ success: boolean; message: string; data: NhaHang }> {
    const response = await apiClient.put(`/nha-hang/${id}`, data)
    return response.data
  }

  /**
   * Xóa nhà hàng
   */
  async delete(id: number): Promise<{ success: boolean; message: string }> {
    const response = await apiClient.delete(`/nha-hang/${id}`)
    return response.data
  }

  /**
   * Thống kê nhà hàng
   */
  async getStats(): Promise<{ success: boolean; data: NhaHangStats }> {
    const response = await apiClient.get('/nha-hang/stats')
    return response.data
  }
}

const nhaHangService = new NhaHangService()
export default nhaHangService

