<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="modelValue" class="register-overlay" @click.self="close">
        <div class="register-card">
          <button class="close-btn" @click="close">×</button>
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
              <a href="#" class="link" @click.prevent="switchToLogin">Đăng nhập ngay</a>
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
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { reactive, ref } from 'vue'
import FormInput from '@/components/ui/Input.vue'
import FormButton from '@/components/ui/Button.vue'
import { Facebook } from 'lucide-vue-next'
import authService from '@/services/auth.service'

interface Props {
  modelValue: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  success: []
  'switch-to-login': []
}>()

const close = () => {
  emit('update:modelValue', false)
}

const switchToLogin = () => {
  close()
  emit('switch-to-login')
}

const isLoading = ref(false)
const isGoogleLoading = ref(false)

const formData = reactive({
  fullName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  agreeTerms: false
})

const errors = reactive({
  fullName: '',
  email: '',
  phone: '',
  password: '',
  confirmPassword: '',
  agreeTerms: ''
})

const validateForm = (): boolean => {
  let isValid = true
  
  Object.keys(errors).forEach(key => {
    errors[key as keyof typeof errors] = ''
  })

  if (!formData.fullName.trim()) {
    errors.fullName = 'Vui lòng nhập họ và tên'
    isValid = false
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!formData.email.trim()) {
    errors.email = 'Vui lòng nhập email'
    isValid = false
  } else if (!emailRegex.test(formData.email)) {
    errors.email = 'Email không hợp lệ'
    isValid = false
  }

  const phoneRegex = /^[0-9]{10}$/
  if (!formData.phone.trim()) {
    errors.phone = 'Vui lòng nhập số điện thoại'
    isValid = false
  } else if (!phoneRegex.test(formData.phone)) {
    errors.phone = 'Số điện thoại không hợp lệ (10 chữ số)'
    isValid = false
  }

  if (!formData.password) {
    errors.password = 'Vui lòng nhập mật khẩu'
    isValid = false
  } else if (formData.password.length < 6) {
    errors.password = 'Mật khẩu phải có ít nhất 6 ký tự'
    isValid = false
  }

  if (!formData.confirmPassword) {
    errors.confirmPassword = 'Vui lòng xác nhận mật khẩu'
    isValid = false
  } else if (formData.password !== formData.confirmPassword) {
    errors.confirmPassword = 'Mật khẩu không khớp'
    isValid = false
  }

  if (!formData.agreeTerms) {
    errors.agreeTerms = 'Vui lòng đồng ý với điều khoản dịch vụ'
    isValid = false
  }

  return isValid
}

const handleRegister = async () => {
  if (!validateForm()) return

  isLoading.value = true

  try {
    const response = await authService.register({
      full_name: formData.fullName,
      email: formData.email,
      phone: formData.phone,
      password: formData.password,
    })
    
    if (response.success) {
      // Dispatch event để Header cập nhật trạng thái
      window.dispatchEvent(new Event('auth-changed'))
      alert('Đăng ký thành công!')
      emit('success')
      close()
    }
  } catch (error: any) {
    console.error('Register error:', error)
    alert(error.response?.data?.message || 'Đăng ký thất bại. Vui lòng thử lại!')
  } finally {
    isLoading.value = false
  }
}

const handleGoogleLogin = () => {
  // Redirect đến backend để xử lý Google OAuth
  // Backend sẽ redirect về frontend với token sau khi xác thực thành công
  window.location.href = 'http://localhost:8000/api/auth/google'
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
.register-overlay {
  position: fixed;
  inset: 0;
  z-index: 1000;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  background: rgba(0, 0, 0, 0.5);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
}

.register-card {
  position: relative;
  background: #fff;
  border-radius: 20px;
  padding: 2.5rem;
  width: 100%;
  max-width: 480px;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 
    0 25px 50px -12px rgba(0, 0, 0, 0.4),
    0 0 0 1px rgba(255, 255, 255, 0.1);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
}

.close-btn {
  position: absolute;
  top: 16px;
  right: 16px;
  width: 36px;
  height: 36px;
  border: none;
  background: rgba(0, 0, 0, 0.05);
  border-radius: 50%;
  font-size: 24px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #666;
  transition: all 0.2s ease;
  z-index: 10;
}

.close-btn:hover {
  background: rgba(0, 0, 0, 0.1);
  transform: rotate(90deg);
}

/* Transition */
.fade-enter-active,
.fade-leave-active {
  transition: all 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.fade-enter-from .register-card,
.fade-leave-to .register-card {
  transform: translateY(30px) scale(0.95);
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
