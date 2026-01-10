/**
 * ============================================================================
 * BAN SERVICE - QUẢN LÝ BÀN
 * ============================================================================
 */

import api from './api'

export interface Ban {
  BanID: number
  NhaHangID: number
  KhuVucID: number | null
  SoBan: number
  SoGhe: number
  TrangThai: number // 1: Trống, 2: Đã đặt, 3: Đang dùng
  TaoLuc?: string
  CapNhatLuc?: string
  khu_vuc?: KhuVuc
  nha_hang?: any
}

export interface KhuVuc {
  KhuVucID: number
  NhaHangID: number
  TenKhuVuc: string
  MoTa?: string
  bans?: Ban[]
}

export interface BanStats {
  total: number
  trong: number
  daDat: number
  dangDung: number
}

// Trạng thái bàn
export const TRANG_THAI_BAN = {
  TRONG: 1,
  DA_DAT: 2,
  DANG_DUNG: 3
}

export const TRANG_THAI_BAN_TEXT: Record<number, string> = {
  1: 'Trống',
  2: 'Đã đặt',
  3: 'Đang dùng'
}

export const TRANG_THAI_BAN_COLOR: Record<number, string> = {
  1: '#22c55e', // green
  2: '#f59e0b', // yellow
  3: '#ef4444'  // red
}

const banService = {
  /**
   * Lấy danh sách bàn
   */
  async getAll(params?: { khu_vuc_id?: number; trang_thai?: number; nha_hang_id?: number }) {
    const response = await api.get<{ success: boolean; data: Ban[] }>('/ban', { params })
    return response.data
  },

  /**
   * Lấy chi tiết bàn
   */
  async getById(id: number) {
    const response = await api.get<{ success: boolean; data: Ban }>(`/ban/${id}`)
    return response.data
  },

  /**
   * Tạo bàn mới
   */
  async create(data: Partial<Ban>) {
    const response = await api.post<{ success: boolean; data: Ban; message: string }>('/ban', data)
    return response.data
  },

  /**
   * Cập nhật bàn
   */
  async update(id: number, data: Partial<Ban>) {
    const response = await api.put<{ success: boolean; data: Ban; message: string }>(`/ban/${id}`, data)
    return response.data
  },

  /**
   * Cập nhật trạng thái bàn
   */
  async updateStatus(id: number, trangThai: number) {
    const response = await api.put<{ success: boolean; data: Ban; message: string }>(`/ban/${id}/status`, {
      TrangThai: trangThai
    })
    return response.data
  },

  /**
   * Xóa bàn
   */
  async delete(id: number) {
    const response = await api.delete<{ success: boolean; message: string }>(`/ban/${id}`)
    return response.data
  },

  /**
   * Thống kê bàn
   */
  async getStats(nhaHangId?: number) {
    const response = await api.get<{ success: boolean; data: BanStats }>('/ban/stats', {
      params: { nha_hang_id: nhaHangId }
    })
    return response.data
  }
}

export default banService
