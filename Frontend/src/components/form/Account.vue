<template>
  <div class="user-actions">
    <!-- Notification Bell -->
    <button class="action-btn notification-btn" @click="toggleNotifications">
      <span class="action-icon">🔔</span>
      <span v-if="notificationCount > 0" class="notification-badge">{{ notificationCount }}</span>
    </button>

    <!-- User Account -->
    <div class="user-menu" ref="userMenuRef">
      <button class="action-btn user-btn" @click="toggleUserMenu">
        <span class="user-avatar">{{ userInitial }}</span>
      </button>

      <!-- Dropdown Menu -->
      <div v-if="showUserMenu" class="user-dropdown">
        <div class="dropdown-header">
          <span class="user-name">{{ userName }}</span>
          <span class="user-email">{{ userEmail }}</span>
        </div>
        <div class="dropdown-divider"></div>
        <!-- Admin Menu Items -->
        <template v-if="isAdmin">
          <router-link to="/admin/users" class="dropdown-item admin-item" @click="showUserMenu = false">
            <span>👥</span> Quản lý người dùng
          </router-link>
          <a href="#" class="dropdown-item admin-item">
            <span>🍽️</span> Quản lý món ăn
          </a>
          <router-link to="/admin/restaurants" class="dropdown-item admin-item" @click="showUserMenu = false">
            <span>🏪</span> Quản lý nhà hàng
          </router-link>
          <div class="dropdown-divider"></div>
        </template>

        <!-- User Menu Items -->
        <a href="#" class="dropdown-item">
          <span>👤</span> Tài khoản
        </a>
        <a href="#" class="dropdown-item">
          <span>📦</span> Đơn hàng
        </a>
        <a href="#" class="dropdown-item">
          <span>❤️</span> Yêu thích
        </a>
        <div class="dropdown-divider"></div>
        <button class="dropdown-item logout-btn" @click="handleLogout">
          <span>🚪</span> Đăng xuất
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted } from 'vue'
import authService from '@/services/auth.service'

// Props
const props = defineProps<{
  user: any
  notificationCount?: number
}>()

// Emits
const emit = defineEmits<{
  (e: 'logout'): void
}>()

// State
const showUserMenu = ref(false)
const userMenuRef = ref<HTMLElement | null>(null)

// Computed
const userName = computed(() => props.user?.HoTen || 'Người dùng')
const userEmail = computed(() => props.user?.Email || '')
const userInitial = computed(() => {
  const name = props.user?.HoTen || 'U'
  return name.charAt(0).toUpperCase()
})

// Check if user is admin (LoaiNguoiDung: 1 = Admin, 2 = Nhân viên, 3 = Khách hàng)
const isAdmin = computed(() => props.user?.LoaiNguoiDung === 1)

// Methods
const toggleNotifications = () => {
  alert('Bạn có ' + (props.notificationCount || 0) + ' thông báo mới!')
}

const toggleUserMenu = () => {
  showUserMenu.value = !showUserMenu.value
}

const handleLogout = () => {
  authService.logout()
  showUserMenu.value = false
  // Dispatch event để Header cập nhật
  window.dispatchEvent(new Event('auth-changed'))
  emit('logout')
}

// Click outside to close
const handleClickOutside = (event: Event) => {
  const target = event.target as HTMLElement
  if (userMenuRef.value && !userMenuRef.value.contains(target)) {
    showUserMenu.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.user-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.action-btn {
  position: relative;
  width: 44px;
  height: 44px;
  border-radius: 50%;
  border: none;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.action-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.action-icon {
  font-size: 20px;
}

.notification-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  font-size: 11px;
  font-weight: 700;
  min-width: 18px;
  height: 18px;
  border-radius: 9px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 4px;
  border: 2px solid white;
}

.user-menu {
  position: relative;
}

.user-avatar {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
  font-size: 14px;
  font-weight: 700;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.user-dropdown {
  position: absolute;
  top: calc(100% + 8px);
  right: 0;
  min-width: 220px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
  padding: 8px 0;
  z-index: 1000;
  animation: fadeIn 0.2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-10px); }
  to { opacity: 1; transform: translateY(0); }
}

.dropdown-header {
  padding: 12px 16px;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.user-name {
  font-size: 15px;
  font-weight: 600;
  color: #1f2937;
}

.user-email {
  font-size: 13px;
  color: #6b7280;
}

.dropdown-divider {
  height: 1px;
  background: #e5e7eb;
  margin: 4px 0;
}

.dropdown-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 16px;
  font-size: 14px;
  color: #374151;
  text-decoration: none;
  transition: all 0.2s ease;
  cursor: pointer;
  background: none;
  border: none;
  width: 100%;
  text-align: left;
}

.dropdown-item:hover {
  background: linear-gradient(135deg, rgba(229, 57, 53, 0.08) 0%, rgba(255, 111, 0, 0.08) 100%);
  color: #e53935;
}

.admin-item {
  color: #7c3aed;
}

.admin-item:hover {
  background: linear-gradient(135deg, rgba(124, 58, 237, 0.08) 0%, rgba(139, 92, 246, 0.08) 100%);
  color: #7c3aed;
}

.logout-btn {
  color: #dc2626;
}

.logout-btn:hover {
  background: rgba(220, 38, 38, 0.1);
  color: #dc2626;
}

/* Responsive */
@media (max-width: 900px) {
  .user-actions {
    gap: 8px;
  }

  .action-btn {
    width: 40px;
    height: 40px;
  }

  .user-dropdown {
    right: -10px;
  }
}
</style>

