<template>
  <div class="auth-callback">
    <div class="callback-card">
      <div v-if="loading" class="loading">
        <div class="spinner"></div>
        <p>Đang xử lý đăng nhập...</p>
      </div>
      
      <div v-else-if="error" class="error">
        <span class="error-icon">❌</span>
        <h2>Đăng nhập thất bại</h2>
        <p>{{ error }}</p>
        <button @click="goHome" class="btn-home">Về trang chủ</button>
      </div>
      
      <div v-else class="success">
        <span class="success-icon">✅</span>
        <h2>Đăng nhập thành công!</h2>
        <p>Xin chào, {{ userName }}!</p>
        <p class="redirect-text">Đang chuyển hướng...</p>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import authService from '@/services/auth.service'

const router = useRouter()
const route = useRoute()

const loading = ref(true)
const error = ref('')
const userName = ref('')

onMounted(() => {
  processCallback()
})

const processCallback = () => {
  try {
    const token = route.query.token as string
    const userStr = route.query.user as string
    const errorMsg = route.query.error as string

    if (errorMsg) {
      error.value = decodeURIComponent(errorMsg)
      loading.value = false
      return
    }

    if (token && userStr) {
      const user = JSON.parse(decodeURIComponent(userStr))

      // Lưu token và user vào localStorage
      localStorage.setItem('auth_token', token)
      localStorage.setItem('user', JSON.stringify(user))

      // Dispatch custom event để Header cập nhật trạng thái
      window.dispatchEvent(new Event('auth-changed'))

      userName.value = user.HoTen || 'Người dùng'
      loading.value = false

      // Redirect về trang chủ sau 1.5 giây
      setTimeout(() => {
        router.push('/')
      }, 1500)
    } else {
      error.value = 'Không nhận được thông tin xác thực'
      loading.value = false
    }
  } catch (e) {
    console.error('Callback error:', e)
    error.value = 'Có lỗi xảy ra khi xử lý đăng nhập'
    loading.value = false
  }
}

const goHome = () => {
  router.push('/')
}
</script>

<style scoped>
.auth-callback {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.callback-card {
  background: white;
  border-radius: 20px;
  padding: 40px;
  text-align: center;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
  max-width: 400px;
  width: 100%;
}

.loading, .error, .success {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.spinner {
  width: 50px;
  height: 50px;
  border: 4px solid #f3f3f3;
  border-top: 4px solid #667eea;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.error-icon, .success-icon {
  font-size: 48px;
}

h2 {
  margin: 0;
  color: #1f2937;
}

p {
  color: #6b7280;
  margin: 0;
}

.redirect-text {
  font-size: 14px;
  color: #9ca3af;
}

.btn-home {
  margin-top: 16px;
  padding: 12px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
}

.btn-home:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
}
</style>

