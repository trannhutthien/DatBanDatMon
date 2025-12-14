import apiClient from './api'

export interface LoginResponse {
  success: boolean
  message: string
  data: {
    token: string
    token_type: string
    user: {
      NguoiDungID: number
      HoTen: string
      Email: string
      SDT?: string
      LoaiNguoiDung?: number
      DiaChi?: string
      Avatar?: string
    }
  }
}

export interface GoogleAuthPayload {
  credential: string
  client_id: string
}

class AuthService {
  // Login với Google
  async loginWithGoogle(credential: string, clientId: string): Promise<LoginResponse> {
    const response = await apiClient.post<LoginResponse>('/auth/google/token', {
      credential,
      client_id: clientId,
    })
    
    if (response.data.success && response.data.data.token) {
      this.setToken(response.data.data.token)
      this.setUser(response.data.data.user)
    }
    
    return response.data
  }

  // Đăng ký thông thường
  async register(data: any): Promise<any> {
    const response = await apiClient.post('/auth/register', data)
    
    if (response.data.success && response.data.data.token) {
      this.setToken(response.data.data.token)
      this.setUser(response.data.data.user)
    }
    
    return response.data
  }

  // Đăng nhập thông thường
  async login(email: string, password: string): Promise<LoginResponse> {
    const response = await apiClient.post<LoginResponse>('/auth/login', {
      Email: email,
      MatKhau: password,
    })
    
    if (response.data.success && response.data.data.token) {
      this.setToken(response.data.data.token)
      this.setUser(response.data.data.user)
    }
    
    return response.data
  }

  // Đăng xuất
  logout(): void {
    localStorage.removeItem('auth_token')
    localStorage.removeItem('user')
  }

  // Lưu token
  private setToken(token: string): void {
    localStorage.setItem('auth_token', token)
  }

  // Lưu user
  private setUser(user: any): void {
    localStorage.setItem('user', JSON.stringify(user))
  }

  // Lấy user
  getUser(): any {
    const user = localStorage.getItem('user')
    return user ? JSON.parse(user) : null
  }

  // Kiểm tra đăng nhập
  isAuthenticated(): boolean {
    return !!localStorage.getItem('auth_token')
  }

  // Lấy token
  getToken(): string | null {
    return localStorage.getItem('auth_token')
  }
}

export default new AuthService()
