/**
 * ============================================================================
 * DANH MỤC MÓN SERVICE - QUẢN LÝ DANH MỤC MÓN ĂN
 * ============================================================================
 */

import api from './api'

export interface DanhMucMon {
  DanhMucID: number
  TenDanhMuc: string
}

const danhMucMonService = {
  /**
   * Lấy danh sách danh mục món
   */
  async getAll() {
    const response = await api.get<{ success: boolean; data: DanhMucMon[] }>('/danh-muc-mon')
    return response.data
  },

  /**
   * Lấy chi tiết danh mục
   */
  async getById(id: number) {
    const response = await api.get<{ success: boolean; data: DanhMucMon }>(`/danh-muc-mon/${id}`)
    return response.data
  }
}

export default danhMucMonService
