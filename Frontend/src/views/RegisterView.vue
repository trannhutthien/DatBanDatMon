<template>
  <div class="register-page">
    <div class="register-container">
      <div class="register-card">
        <div class="register-header">
          <h1>Đăng Ký Tài Khoản</h1>
          <p>Tạo tài khoản để trải nghiệm dịch vụ của chúng tôi</p>
        </div>

        <form @submit.prevent="handleRegister" class="register-form">
          <FormInput
            v-model="formData.fullName"
            label="Họ và tên"
            placeholder="Nhập họ và tên của bạn"
            :required="true"
            :error="errors.fullName"
            prefixIcon="👤"
          />

          <FormInput
            v-model="formData.email"
            type="email"
            label="Email"
            placeholder="example@email.com"
            :required="true"
            :error="errors.email"
            prefixIcon="📧"
          />

          <FormInput
            v-model="formData.phone"
            type="tel"
            label="Số điện thoại"
            placeholder="0123456789"
            :required="true"
            :error="errors.phone"
            prefixIcon="📱"
          />

          <FormInput
            v-model="formData.password"
            type="password"
            label="Mật khẩu"
            placeholder="Nhập mật khẩu"
            :required="true"
            :error="errors.password"
            prefixIcon="🔒"
            helperText="Mật khẩu tối thiểu 6 ký tự"
          />

          <FormInput
            v-model="formData.confirmPassword"
            type="password"
            label="Xác nhận mật khẩu"
            placeholder="Nhập lại mật khẩu"
            :required="true"
            :error="errors.confirmPassword"
            prefixIcon="🔒"
          />

          

          <FormTextarea
            v-model="formData.address"
            label="Địa chỉ"
            placeholder="Nhập địa chỉ của bạn"
            :rows="3"
            :maxLength="200"
            :error="errors.address"
          />

          <div class="terms-checkbox">
            <input
              type="checkbox"
              id="terms"
              v-model="formData.agreeTerms"
              class="checkbox-input"
            />
            <label for="terms" class="checkbox-label">
              Tôi đồng ý với <a href="#" class="link">Điều khoản dịch vụ</a> và
              <a href="#" class="link">Chính sách bảo mật</a>
            </label>
          </div>
          <span v-if="errors.agreeTerms" class="error-text">{{ errors.agreeTerms }}</span>

          <FormButton
            type="submit"
            variant="primary"
            size="large"
            :block="true"
            :loading="isLoading"
          >
            Đăng Ký
          </FormButton>

          <div class="login-link">
            <p>
              Đã có tài khoản?
              <router-link to="/login" class="link">Đăng nhập ngay</router-link>
            </p>
          </div>
        </form>

        <div class="divider">
          <span>Hoặc đăng ký với</span>
        </div>

        <div class="social-login">
          <FormButton 
            variant="outline" 
            @click="handleSocialLogin('google')" 
            class="social-btn google-btn"
            :loading="isGoogleLoading"
            :disabled="isGoogleLoading"
          >
            <svg v-if="!isGoogleLoading" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20">
              <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
              <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
              <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
              <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
            </svg>
            <span>{{ isGoogleLoading ? 'Đang xử lý...' : 'Google' }}</span>
          </FormButton>
          <FormButton variant="outline" @click="handleSocialLogin('facebook')" class="social-btn facebook-btn">
            <Facebook :size="20" />
            <span>Facebook</span>
          </FormButton>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { decodeCredential, googleSdkLoaded } from 'vue3-google-login'
import FormInput from '@/components/form/Input.vue'
import FormButton from '@/components/form/Button.vue'
import FormTextarea from '@/components/form/Textarea.vue'
import FormSelect from '@/components/form/Select.vue'
import { Facebook } from 'lucide-vue-next'
import authService from '@/services/auth.service'

const router = useRouter()
const isLoading = ref(false)
const isGoogleLoading = ref(false)

const formData = reactive({
  fullName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  gender: '',
  address: '',
  agreeTerms: false
})

const errors = reactive({
  fullName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  gender: '',
  address: '',
  agreeTerms: ''
})

const genderOptions = [
  { label: 'Nam', value: 'male' },
  { label: 'Nữ', value: 'female' },
  { label: 'Khác', value: 'other' }
]

const validateForm = (): boolean => {
  let isValid = true
  
  // Reset errors
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })

  // Validate full name
  if (!formData.fullName.trim()) {
    errors.fullName = 'Vui lòng nhập họ và tên'
    isValid = false
  }

  // Validate email
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!formData.email.trim()) {
    errors.email = 'Vui lòng nhập email'
    isValid = false
  } else if (!emailRegex.test(formData.email)) {
    errors.email = 'Email không hợp lệ'
    isValid = false
  }

  // Validate phone
  const phoneRegex = /^[0-9]{10}$/
  if (!formData.phone.trim()) {
    errors.phone = 'Vui lòng nhập số điện thoại'
    isValid = false
  } else if (!phoneRegex.test(formData.phone)) {
    errors.phone = 'Số điện thoại không hợp lệ (10 chữ số)'
    isValid = false
  }

  // Validate password
  if (!formData.password) {
    errors.password = 'Vui lòng nhập mật khẩu'
    isValid = false
  } else if (formData.password.length < 6) {
    errors.password = 'Mật khẩu phải có ít nhất 6 ký tự'
    isValid = false
  }

  // Validate confirm password
  if (!formData.confirmPassword) {
    errors.confirmPassword = 'Vui lòng xác nhận mật khẩu'
    isValid = false
  } else if (formData.password !== formData.confirmPassword) {
    errors.confirmPassword = 'Mật khẩu không khớp'
    isValid = false
  }

  // Validate terms
  if (!formData.agreeTerms) {
    errors.agreeTerms = 'Vui lòng đồng ý với điều khoản dịch vụ'
    isValid = false
  }

  return isValid
}

const handleRegister = async () => {
  if (!validateForm()) {
    return
  }

  isLoading.value = true

  try {
    const response = await authService.register({
      full_name: formData.fullName,
      email: formData.email,
      phone: formData.phone,
      password: formData.password,
      gender: formData.gender,
      address: formData.address,
    })
    
    if (response.success) {
      alert('Đăng ký thành công!')
      router.push('/')
    }
  } catch (error: any) {
    console.error('Register error:', error)
    const message = error.response?.data?.message || 'Đăng ký thất bại. Vui lòng thử lại!'
    alert(message)
  } finally {
    isLoading.value = false
  }
}

const handleGoogleLogin = () => {
  isGoogleLoading.value = true
  
  googleSdkLoaded(google => {
    google.accounts.oauth2
      .initCodeClient({
        client_id: import.meta.env.VITE_GOOGLE_CLIENT_ID,
        scope: 'email profile openid',
        ux_mode: 'popup',
        callback: async (response: any) => {
          if (response.code) {
            try {
              // Gửi authorization code lên backend
              const authResponse = await authService.loginWithGoogleCode(response.code)
              
              if (authResponse.success) {
                alert('Đăng nhập Google thành công!')
                router.push('/')
              }
            } catch (error: any) {
              console.error('Google login error:', error)
              const message = error.response?.data?.message || 'Đăng nhập Google thất bại!'
              alert(message)
            }
          }
          isGoogleLoading.value = false
        },
      })
      .requestCode()
  })
}

const handleSocialLogin = (provider: string) => {
  if (provider === 'google') {
    handleGoogleLogin()
  } else if (provider === 'facebook') {
    alert('Đăng ký với Facebook - Tính năng đang phát triển')
  }
}
</script>

<style scoped>
.register-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 2rem 1rem;
}

.register-container {
  width: 100%;
  max-width: 600px;
}

.register-card {
  background: #fff;
  border-radius: 16px;
  padding: 3rem;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.register-header {
  text-align: center;
  margin-bottom: 2rem;
}

.register-header h1 {
  font-size: 2rem;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}

.register-header p {
  color: #7f8c8d;
  margin: 0;
  font-size: 0.95rem;
}

.register-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.terms-checkbox {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.checkbox-input {
  margin-top: 4px;
  cursor: pointer;
  width: 18px;
  height: 18px;
}

.checkbox-label {
  font-size: 0.9rem;
  color: #2c3e50;
  cursor: pointer;
  line-height: 1.5;
}

.error-text {
  font-size: 0.85rem;
  color: #e74c3c;
  margin-top: -10px;
}

.link {
  color: #3498db;
  text-decoration: none;
  font-weight: 600;
}

.link:hover {
  text-decoration: underline;
}

.login-link {
  text-align: center;
  margin-top: 1rem;
}

.login-link p {
  color: #7f8c8d;
  margin: 0;
}

.divider {
  text-align: center;
  margin: 2rem 0 1.5rem;
  position: relative;
}

.divider::before {
  content: '';
  position: absolute;
  left: 0;
  top: 50%;
  width: 100%;
  height: 1px;
  background: #dfe6e9;
}

.divider span {
  background: #fff;
  padding: 0 1rem;
  color: #95a5a6;
  font-size: 0.9rem;
  position: relative;
}

.social-login {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}

.social-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.google-btn:hover {
  border-color: #db4437 !important;
  color: #db4437 !important;
}

.facebook-btn:hover {
  border-color: #1877f2 !important;
  color: #1877f2 !important;
}

@media (max-width: 640px) {
  .register-card {
    padding: 2rem 1.5rem;
  }

  .register-header h1 {
    font-size: 1.5rem;
  }

  .social-login {
    grid-template-columns: 1fr;
  }
}
</style>
