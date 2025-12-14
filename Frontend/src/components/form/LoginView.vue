<template>
  <Teleport to="body">
    <Transition name="fade">
      <div v-if="modelValue" class="login-overlay" @click.self="close">
        <div class="login-card">
          <button class="close-btn" @click="close">×</button>
          <div class="login-header">
            <h1>Đăng Nhập</h1>
            <p>Chào mừng bạn quay trở lại!</p>
          </div>

          <form @submit.prevent="handleLogin" class="login-form">
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
              v-model="formData.password"
              type="password"
              label="Mật khẩu"
              placeholder="Nhập mật khẩu"
              :required="true"
              :error="errors.password"
              prefixIcon="🔒"
            />

            <div class="form-options">
              <label class="remember-me">
                <input type="checkbox" v-model="formData.rememberMe" />
                <span>Ghi nhớ đăng nhập</span>
              </label>
              <a href="#" class="forgot-password">Quên mật khẩu?</a>
            </div>

            <FormButton
              type="submit"
              variant="gradient"
              size="large"
              :block="true"
              :loading="isLoading"
            >
              Đăng Nhập
            </FormButton>

            <div class="register-link">
              <p>
                Chưa có tài khoản?
                <a href="#" class="link" @click.prevent="switchToRegister">Đăng ký ngay</a>
              </p>
            </div>
          </form>

          <div class="divider">
            <span>Hoặc đăng nhập với</span>
          </div>

          <div class="social-login">
            <FormButton 
              variant="outline" 
              @click="handleGoogleLogin" 
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
            <FormButton variant="outline" class="social-btn facebook-btn">
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
import { decodeCredential } from 'vue3-google-login'
import FormInput from '@/components/ui/Input.vue'
import FormButton from '@/components/ui/Button.vue'
import { Facebook } from 'lucide-vue-next'
import authService from '@/services/auth.service'

declare const google: any

interface Props {
  modelValue: boolean
}

const props = defineProps<Props>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  success: []
  'switch-to-register': []
}>()

const close = () => {
  emit('update:modelValue', false)
}

const switchToRegister = () => {
  close()
  emit('switch-to-register')
}

const isLoading = ref(false)
const isGoogleLoading = ref(false)

const formData = reactive({
  email: '',
  password: '',
  rememberMe: false
})

const errors = reactive({
  email: '',
  password: ''
})

const validateForm = (): boolean => {
  let isValid = true
  errors.email = ''
  errors.password = ''

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!formData.email.trim()) {
    errors.email = 'Vui lòng nhập email'
    isValid = false
  } else if (!emailRegex.test(formData.email)) {
    errors.email = 'Email không hợp lệ'
    isValid = false
  }

  if (!formData.password) {
    errors.password = 'Vui lòng nhập mật khẩu'
    isValid = false
  }

  return isValid
}

const handleLogin = async () => {
  if (!validateForm()) return

  isLoading.value = true

  try {
    const response = await authService.login(formData.email, formData.password)

    if (response.success) {
      // Dispatch event để Header cập nhật trạng thái
      window.dispatchEvent(new Event('auth-changed'))
      alert('Đăng nhập thành công!')
      emit('success')
      close()
    }
  } catch (error: any) {
    console.error('Login error:', error)
    alert(error.response?.data?.message || 'Đăng nhập thất bại!')
  } finally {
    isLoading.value = false
  }
}

const handleGoogleLogin = () => {
  // Redirect đến backend để xử lý Google OAuth
  // Backend sẽ redirect về frontend với token sau khi xác thực thành công
  window.location.href = 'http://localhost:8000/api/auth/google'
}
</script>


<style scoped>
.login-overlay {
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

.login-card {
  position: relative;
  background: #fff;
  border-radius: 20px;
  padding: 2.5rem;
  width: 100%;
  max-width: 420px;
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

.fade-enter-from .login-card,
.fade-leave-to .login-card {
  transform: translateY(30px) scale(0.95);
}

.login-header {
  text-align: center;
  margin-bottom: 2rem;
}

.login-header h1 {
  font-size: 1.75rem;
  color: #2c3e50;
  margin: 0 0 0.5rem 0;
}

.login-header p {
  color: #7f8c8d;
  margin: 0;
  font-size: 0.95rem;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.25rem;
}

.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.remember-me {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  font-size: 0.85rem;
  color: #2c3e50;
}

.remember-me input {
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.forgot-password {
  font-size: 0.85rem;
  color: #667eea;
  text-decoration: none;
  font-weight: 500;
}

.forgot-password:hover {
  text-decoration: underline;
}

.link {
  color: #667eea;
  text-decoration: none;
  font-weight: 600;
}

.link:hover {
  text-decoration: underline;
}

.register-link {
  text-align: center;
  margin-top: 0.5rem;
}

.register-link p {
  color: #7f8c8d;
  margin: 0;
  font-size: 0.9rem;
}

.divider {
  text-align: center;
  margin: 1.5rem 0;
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
  font-size: 0.85rem;
  position: relative;
}

.social-login {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
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

@media (max-width: 480px) {
  .login-card {
    padding: 2rem 1.5rem;
    margin: 10px;
  }

  .login-header h1 {
    font-size: 1.5rem;
  }

  .social-login {
    grid-template-columns: 1fr;
  }

  .form-options {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }
}
</style>
