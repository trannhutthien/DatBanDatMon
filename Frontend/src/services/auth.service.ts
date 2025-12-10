import apiClient from './api'

export interface LoginResponse {
  success: boolean
  message: string
  data: {
    token: string
    user: {
      id: number
      name: string
      email: string
      avatar?: string
    }
  }
}

export interface GoogleAuthPayload {
  credential: string
}

class AuthService {
  // Login với Google (sử dụng ID Token từ Google One Tap)
  async loginWithGoogle(credential: string): Promise<LoginResponse> {
    const response = await apiClient.post<LoginResponse>('/auth/google/token', {
      credential,
    })

    if (response.data.success && response.data.data.token) {
      this.setToken(response.data.data.token)
      this.setUser(response.data.data.user)
    }

    return response.data
  }

  // Login với Google (sử dụng Authorization Code)
  async loginWithGoogleCode(code: string): Promise<LoginResponse> {
    const response = await apiClient.post<LoginResponse>('/auth/google/code', {
      code,
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
      email,
      password,
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
