/**
 * ============================================================================
 * KHU VUC SERVICE - QUẢN LÝ KHU VỰC
 * ============================================================================
 */

import api from './api'
import type { KhuVuc } from './ban.service'

const khuVucService = {
  /**
   * Lấy danh sách khu vực
   */
  async getAll(params?: { nha_hang_id?: number }) {
    const response = await api.get<{ success: boolean; data: KhuVuc[] }>('/khu-vuc', { params })
    return response.data
  },

  /**
   * Lấy chi tiết khu vực
   */
  async getById(id: number) {
    const response = await api.get<{ success: boolean; data: KhuVuc }>(`/khu-vuc/${id}`)
    return response.data
  },

  /**
   * Tạo khu vực mới
   */
  async create(data: Partial<KhuVuc>) {
    const response = await api.post<{ success: boolean; data: KhuVuc; message: string }>('/khu-vuc', data)
    return response.data
  },

  /**
   * Cập nhật khu vực
   */
  async update(id: number, data: Partial<KhuVuc>) {
    const response = await api.put<{ success: boolean; data: KhuVuc; message: string }>(`/khu-vuc/${id}`, data)
    return response.data
  },

  /**
   * Xóa khu vực
   */
  async delete(id: number) {
    const response = await api.delete<{ success: boolean; message: string }>(`/khu-vuc/${id}`)
    return response.data
  }
}

export default khuVucService
