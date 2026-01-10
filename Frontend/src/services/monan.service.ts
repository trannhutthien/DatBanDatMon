/**
 * ============================================================================
 * MÓN ĂN SERVICE - GỌI API MÓN ĂN
 * ============================================================================
 * 
 * Service này xử lý tất cả API calls liên quan đến món ăn.
 * 
 * SỬ DỤNG:
 * - import { monAnService } from '@/services/monan.service'
 * - const data = await monAnService.getPopular()
 * ============================================================================
 */

import axios from 'axios'

// Base URL của API
const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'

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
}

/**
 * Interface cho Món ăn
 */
export interface MonAn {
  MonAnID: number
  TenMon: string
  NhaHangID: number | null
  DanhMucID: number | null
  DonGia: number
  HinhAnh: string | null
  MoTa: string | null
  TrangThai: number
  TaoLuc: string
  CapNhatLuc: string
  nha_hang?: NhaHang  // Quan hệ với nhà hàng (eager loaded)
  danh_muc?: {        // Quan hệ với danh mục
    DanhMucID: number
    TenDanhMuc: string
  }
}

/**
 * Interface cho response phân trang
 */
export interface PaginatedResponse<T> {
  success: boolean
  data: {
    items: T[]
    pagination: {
      current_page: number
      last_page: number
      per_page: number
      total: number
    }
  }
}

/**
 * Interface cho response danh sách
 */
export interface ListResponse<T> {
  success: boolean
  data: T[]
}

/**
 * Interface cho response chi tiết
 */
export interface DetailResponse<T> {
  success: boolean
  data: T
}

/**
 * Món ăn Service
 */
export const monAnService = {
  /**
   * Lấy danh sách món ăn (có phân trang)
   * 
   * @param params - Query parameters
   * @returns Promise<PaginatedResponse<MonAn>>
   * 
   * @example
   * const result = await monAnService.getAll({ per_page: 12, search: 'phở' })
   */
  async getAll(params?: {
    per_page?: number
    nha_hang_id?: number
    search?: string
    page?: number
  }): Promise<PaginatedResponse<MonAn>> {
    const response = await axios.get(`${API_URL}/mon-an`, { params })
    return response.data
  },

  /**
   * Lấy món ăn phổ biến
   * 
   * @param limit - Số món trả về (mặc định 6)
   * @returns Promise<ListResponse<MonAn>>
   * 
   * @example
   * const result = await monAnService.getPopular(6)
   * const dishes = result.data
   */
  async getPopular(limit: number = 6): Promise<ListResponse<MonAn>> {
    const response = await axios.get(`${API_URL}/mon-an/popular`, {
      params: { limit }
    })
    return response.data
  },

  /**
   * Lấy món ăn mới nhất
   * 
   * @param limit - Số món trả về (mặc định 10)
   * @returns Promise<ListResponse<MonAn>>
   * 
   * @example
   * const result = await monAnService.getLatest(10)
   * const dishes = result.data
   */
  async getLatest(limit: number = 10): Promise<ListResponse<MonAn>> {
    const response = await axios.get(`${API_URL}/mon-an/latest`, {
      params: { limit }
    })
    return response.data
  },

  /**
   * Lấy chi tiết món ăn
   * 
   * @param id - ID món ăn
   * @returns Promise<DetailResponse<MonAn>>
   * 
   * @example
   * const result = await monAnService.getById(1)
   * const dish = result.data
   */
  async getById(id: number): Promise<DetailResponse<MonAn>> {
    const response = await axios.get(`${API_URL}/mon-an/${id}`)
    return response.data
  }
}

export default monAnService
