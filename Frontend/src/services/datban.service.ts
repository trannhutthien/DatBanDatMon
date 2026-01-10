/**
 * ============================================================================
 * ĐẶT BÀN SERVICE - QUẢN LÝ ĐẶT BÀN
 * ============================================================================
 */

import api from './api'

export interface DatBanDatMon {
  ID: number
  DatBanID: number
  MonAnID: number
  SoLuong: number
  DonGia: number
  GhiChu?: string
  TaoLuc?: string
  mon_an?: {
    MonAnID: number
    TenMon: string
    DonGia: number
    HinhAnh?: string
  }
}

export interface DatBan {
  DatBanID: number
  NguoiDungID: number
  NhaHangID: number
  BanID: number
  ThoiGianDen: string
  SoNguoi: number
  GhiChu?: string
  TrangThai: number
  TaoLuc?: string
  CapNhatLuc?: string
  nguoi_dung?: any
  nha_hang?: any
  ban?: any
  chi_tiet_mon?: DatBanDatMon[]
}

export interface CreateDatBanRequest {
  NguoiDungID: number
  NhaHangID: number
  BanID: number
  ThoiGianDen: string
  SoNguoi: number
  GhiChu?: string
  items?: {
    MonAnID: number
    SoLuong: number
    DonGia: number
    GhiChu?: string
  }[]
}

// Trạng thái đặt bàn
export const TRANG_THAI_DAT_BAN = {
  CHO_XAC_NHAN: 1,
  DA_XAC_NHAN: 2,
  DA_DEN: 3,
  HUY: 4,
  HOAN_THANH: 5
}

export const TRANG_THAI_DAT_BAN_TEXT: Record<number, string> = {
  1: 'Chờ xác nhận',
  2: 'Đã xác nhận',
  3: 'Đã đến',
  4: 'Đã hủy',
  5: 'Hoàn thành'
}

const datBanService = {
  /**
   * Lấy danh sách đặt bàn
   */
  async getAll(params?: { nguoi_dung_id?: number; nha_hang_id?: number; trang_thai?: number }) {
    const response = await api.get<{ success: boolean; data: DatBan[] }>('/dat-ban', { params })
    return response.data
  },

  /**
   * Lấy chi tiết đặt bàn
   */
  async getById(id: number) {
    const response = await api.get<{ success: boolean; data: DatBan }>(`/dat-ban/${id}`)
    return response.data
  },

  /**
   * Tạo đơn đặt bàn mới
   */
  async create(data: CreateDatBanRequest) {
    const response = await api.post<{ success: boolean; data: DatBan; message: string }>('/dat-ban', data)
    return response.data
  },

  /**
   * Cập nhật trạng thái đặt bàn
   */
  async updateStatus(id: number, trangThai: number) {
    const response = await api.put<{ success: boolean; data: DatBan; message: string }>(`/dat-ban/${id}/status`, {
      TrangThai: trangThai
    })
    return response.data
  },

  /**
   * Hủy đặt bàn
   */
  async cancel(id: number) {
    const response = await api.put<{ success: boolean; message: string }>(`/dat-ban/${id}/cancel`)
    return response.data
  },

  /**
   * Lấy danh sách đặt bàn theo user
   */
  async getByUser(nguoiDungId: number) {
    const response = await api.get<{ success: boolean; data: DatBan[] }>('/dat-ban', {
      params: { nguoi_dung_id: nguoiDungId }
    })
    return response.data
  }
}

export default datBanService
