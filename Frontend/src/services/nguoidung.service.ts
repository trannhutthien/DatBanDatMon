import apiClient from './api'

export interface NguoiDung {
  NguoiDungID: number
  HoTen: string
  Email: string
  SDT?: string
  DiaChi?: string
  LoaiNguoiDung: number
  GhiChu?: string
  Avatar?: string
  TaoLuc?: string
  CapNhatLuc?: string
}

export interface NguoiDungResponse {
  success: boolean
  data: {
    data: NguoiDung[]
    current_page: number
    last_page: number
    per_page: number
    total: number
  }
}

export interface StatsResponse {
  success: boolean
  data: {
    total: number
    admin: number
    nhan_vien: number
    khach_hang: number
  }
}

class NguoiDungService {
  /**
   * Lấy danh sách người dùng
   */
  async getAll(params?: { search?: string; loai?: number; per_page?: number; page?: number }): Promise<NguoiDungResponse> {
    const response = await apiClient.get<NguoiDungResponse>('/nguoi-dung', { params })
    return response.data
  }

  /**
   * Lấy chi tiết người dùng
   */
  async getById(id: number): Promise<{ success: boolean; data: NguoiDung }> {
    const response = await apiClient.get(`/nguoi-dung/${id}`)
    return response.data
  }

  /**
   * Tạo người dùng mới
   */
  async create(data: { HoTen: string; Email: string; MatKhau: string; SDT?: string; LoaiNguoiDung: number }): Promise<{ success: boolean; message: string; data: NguoiDung }> {
    const response = await apiClient.post('/nguoi-dung', data)
    return response.data
  }

  /**
   * Cập nhật người dùng
   */
  async update(id: number, data: Partial<NguoiDung>): Promise<{ success: boolean; message: string; data: NguoiDung }> {
    const response = await apiClient.put(`/nguoi-dung/${id}`, data)
    return response.data
  }

  /**
   * Xóa người dùng
   */
  async delete(id: number): Promise<{ success: boolean; message: string }> {
    const response = await apiClient.delete(`/nguoi-dung/${id}`)
    return response.data
  }

  /**
   * Lấy thống kê người dùng
   */
  async getStats(): Promise<StatsResponse> {
    const response = await apiClient.get<StatsResponse>('/nguoi-dung/stats')
    return response.data
  }

  /**
   * Lấy tên loại người dùng
   */
  getLoaiNguoiDungLabel(loai: number): string {
    switch (loai) {
      case 1: return 'Admin'
      case 2: return 'Nhân viên'
      case 3: return 'Khách hàng'
      default: return 'Không xác định'
    }
  }

  /**
   * Lấy màu badge loại người dùng
   */
  getLoaiNguoiDungColor(loai: number): string {
    switch (loai) {
      case 1: return '#7c3aed' // Purple for Admin
      case 2: return '#2563eb' // Blue for Staff
      case 3: return '#16a34a' // Green for Customer
      default: return '#6b7280'
    }
  }
}

export default new NguoiDungService()

