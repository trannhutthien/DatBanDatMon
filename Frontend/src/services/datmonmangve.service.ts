/**
 * ============================================================================
 * ĐẶT MÓN MANG VỀ SERVICE
 * ============================================================================
 */

import axios from 'axios'

const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

export interface OrderItem {
  MonAnID: number
  TenMon?: string
  SoLuong: number
  DonGia: number
  GhiChu?: string
}

export interface TakeawayRequest {
  NguoiDungID?: number
  NhaHangID: number
  HoTen: string
  SDT: string
  DiaChi: string
  ThoiGianLay: string
  GhiChu?: string
  items: OrderItem[]
}

export interface TakeawayResponse {
  success: boolean
  message?: string
  data?: any
}

export const datMonMangVeService = {
  /**
   * Tạo đơn đặt món mang về
   */
  async create(data: TakeawayRequest): Promise<TakeawayResponse> {
    const response = await axios.post(`${API_URL}/dat-mon-mang-ve`, data)
    return response.data
  },

  /**
   * Lấy danh sách đơn đặt món
   */
  async getAll(params?: {
    nguoi_dung_id?: number
    nha_hang_id?: number
    trang_thai?: number
  }): Promise<TakeawayResponse> {
    const response = await axios.get(`${API_URL}/dat-mon-mang-ve`, { params })
    return response.data
  },

  /**
   * Lấy chi tiết đơn đặt món
   */
  async getById(id: number): Promise<TakeawayResponse> {
    const response = await axios.get(`${API_URL}/dat-mon-mang-ve/${id}`)
    return response.data
  },

  /**
   * Cập nhật trạng thái đơn
   */
  async updateStatus(id: number, trangThai: number): Promise<TakeawayResponse> {
    const response = await axios.put(`${API_URL}/dat-mon-mang-ve/${id}/status`, {
      TrangThai: trangThai
    })
    return response.data
  },

  /**
   * Hủy đơn đặt món
   */
  async cancel(id: number): Promise<TakeawayResponse> {
    const response = await axios.put(`${API_URL}/dat-mon-mang-ve/${id}/cancel`)
    return response.data
  }
}

export default datMonMangVeService
