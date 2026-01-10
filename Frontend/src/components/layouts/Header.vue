<template>
  <header class="header">
    <div class="header-container">
      <!-- Logo -->
      <a href="/" class="logo">
        <span class="logo-icon">🍜</span>
        <span class="logo-text">Ăn Ngon</span>
      </a>

      <!-- Navigation -->
      <nav class="nav-menu" :class="{ 'nav-open': isMobileMenuOpen }">
        <router-link to="/order" class="nav-link">
          <span class="nav-icon">🍽️</span>
          Đặt Bàn
        </router-link>
        <router-link to="/booked-tables" class="nav-link">
          <span class="nav-icon"></span>
          Bàn Đã Đặt
        </router-link>
        <a href="#" class="nav-link hot">
          <span class="nav-icon"></span>
          Món Ăn
          <span class="badge">New</span>
        </a>
        <a href="#" class="nav-link">
          <span class="nav-icon">✨</span>
          Mới Nhất
        </a>
      </nav>

      <!-- Auth Buttons - Hiển thị khi chưa đăng nhập -->
      <div v-if="!isLoggedIn" class="auth-buttons">
        <button @click="openRegisterModal" class="btn btn-outline">
          Đăng Ký
        </button>
        <button @click="openLoginModal" class="btn btn-primary">
          Đăng Nhập
        </button>
      </div>

      <!-- User Actions - Hiển thị khi đã đăng nhập -->
      <Account
        v-else
        :user="currentUser"
        :notification-count="notificationCount"
        @logout="handleLogout"
      />

      <!-- Mobile Menu Toggle -->
      <button class="mobile-toggle" @click="toggleMobileMenu">
        <span class="hamburger" :class="{ active: isMobileMenuOpen }"></span>
      </button>
    </div>
  </header>

  <!-- Register Form Popup -->
  <RegisterView
    v-model="showRegisterModal"
    @success="handleRegisterSuccess"
    @switch-to-login="switchToLogin"
  />

  <!-- Login Form Popup -->
  <LoginView
    v-model="showLoginModal"
    @success="handleLoginSuccess"
    @switch-to-register="switchToRegister"
  />
</template>

<script setup lang="ts">
import { ref, defineAsyncComponent, onMounted, onUnmounted } from "vue";
import authService from "@/services/auth.service";
import Account from "@/components/form/Account.vue";

// Lazy load để tránh lỗi
const RegisterView = defineAsyncComponent(
  () => import("@/components/form/RegisterView.vue")
);
const LoginView = defineAsyncComponent(
  () => import("@/components/form/LoginView.vue")
);

const isMobileMenuOpen = ref(false);
const showRegisterModal = ref(false);
const showLoginModal = ref(false);

// User state
const isLoggedIn = ref(false);
const currentUser = ref<any>(null);
const notificationCount = ref(3); // Demo: 3 thông báo

// Check login status
const checkLoginStatus = () => {
  const user = authService.getUser();
  const token = authService.getToken();
  if (user && token) {
    isLoggedIn.value = true;
    currentUser.value = user;
  } else {
    isLoggedIn.value = false;
    currentUser.value = null;
  }
};

// Lắng nghe sự thay đổi localStorage (khi đăng nhập từ tab khác hoặc callback)
const handleStorageChange = (event: StorageEvent) => {
  if (event.key === "auth_token" || event.key === "user") {
    checkLoginStatus();
  }
};

// Custom event listener cho cùng tab (localStorage event chỉ fire ở tab khác)
const handleAuthChange = () => {
  checkLoginStatus();
};

onMounted(() => {
  checkLoginStatus();
  // Lắng nghe storage change từ tab khác
  window.addEventListener("storage", handleStorageChange);
  // Lắng nghe custom event cho cùng tab
  window.addEventListener("auth-changed", handleAuthChange);
});

onUnmounted(() => {
  window.removeEventListener("storage", handleStorageChange);
  window.removeEventListener("auth-changed", handleAuthChange);
});

const openRegisterModal = () => {
  showRegisterModal.value = true;
  showLoginModal.value = false;
};

const openLoginModal = () => {
  showLoginModal.value = true;
  showRegisterModal.value = false;
};

const switchToLogin = () => {
  showRegisterModal.value = false;
  showLoginModal.value = true;
};

const switchToRegister = () => {
  showLoginModal.value = false;
  showRegisterModal.value = true;
};

const handleRegisterSuccess = () => {
  showRegisterModal.value = false;
  checkLoginStatus();
};

const handleLoginSuccess = () => {
  showLoginModal.value = false;
  checkLoginStatus();
};

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleLogout = () => {
  authService.logout();
  isLoggedIn.value = false;
  currentUser.value = null;
};
</script>

<style scoped>
.header {
  position: sticky;
  top: 0;
  z-index: 100;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(20px);
  -webkit-backdrop-filter: blur(20px);
  box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.05);
  border-bottom: 1px solid rgba(255, 255, 255, 0.3);
}

.header-container {
  max-width: 1280px;
  margin: 0 auto;
  padding: 0 24px;
  height: 72px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 32px;
}

/* Logo */
.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  transition: transform 0.3s ease;
}

.logo:hover {
  transform: scale(1.02);
}

.logo-icon {
  font-size: 32px;
  filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
}

.logo-text {
  font-size: 26px;
  font-weight: 800;
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  letter-spacing: -0.5px;
}

/* Navigation */
.nav-menu {
  display: flex;
  align-items: center;
  gap: 8px;
}

.nav-link {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 10px 18px;
  font-size: 15px;
  font-weight: 500;
  color: #374151;
  text-decoration: none;
  border-radius: 12px;
  transition: all 0.25s ease;
  position: relative;
}

.nav-link:hover {
  background: linear-gradient(
    135deg,
    rgba(229, 57, 53, 0.08) 0%,
    rgba(255, 111, 0, 0.08) 100%
  );
  color: #e53935;
  transform: translateY(-2px);
}

.nav-link.hot {
  background: linear-gradient(
    135deg,
    rgba(255, 111, 0, 0.1) 0%,
    rgba(255, 193, 7, 0.1) 100%
  );
  color: #ff6f00;
}

.nav-link.hot:hover {
  background: linear-gradient(
    135deg,
    rgba(255, 111, 0, 0.2) 0%,
    rgba(255, 193, 7, 0.2) 100%
  );
}

.nav-icon {
  font-size: 16px;
}

.badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 20px;
  box-shadow: 0 2px 8px rgba(229, 57, 53, 0.4);
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
}

/* Auth Buttons */
.auth-buttons {
  display: flex;
  align-items: center;
  gap: 12px;
}

.btn {
  padding: 10px 22px;
  font-size: 14px;
  font-weight: 600;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  outline: none;
}

.btn-outline {
  background: transparent;
  color: #6366f1;
  border: 2px solid #6366f1;
  box-shadow: 0 2px 8px rgba(99, 102, 241, 0.15);
}

.btn-outline:hover {
  background: #6366f1;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(99, 102, 241, 0.35);
}

.btn-primary {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
  box-shadow: 0 4px 15px rgba(229, 57, 53, 0.35);
}

.btn-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 30px rgba(229, 57, 53, 0.45);
}

.btn-primary:active {
  transform: translateY(0);
}

/* User Actions - Khi đã đăng nhập */
.user-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.action-btn {
  position: relative;
  width: 44px;
  height: 44px;
  border: none;
  border-radius: 12px;
  background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.action-btn:hover {
  background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.action-icon {
  font-size: 20px;
}

.notification-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  min-width: 18px;
  height: 18px;
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
  font-size: 11px;
  font-weight: 700;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0 5px;
  box-shadow: 0 2px 8px rgba(229, 57, 53, 0.4);
  animation: pulse 2s infinite;
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
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
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
  background: linear-gradient(
    135deg,
    rgba(229, 57, 53, 0.08) 0%,
    rgba(255, 111, 0, 0.08) 100%
  );
  color: #e53935;
}

.logout-btn {
  color: #dc2626;
}

.logout-btn:hover {
  background: rgba(220, 38, 38, 0.1);
  color: #dc2626;
}

/* Mobile Toggle */
.mobile-toggle {
  display: none;
  padding: 8px;
  background: transparent;
  border: none;
  cursor: pointer;
}

.hamburger {
  display: block;
  width: 24px;
  height: 2px;
  background: #374151;
  position: relative;
  transition: all 0.3s ease;
}

.hamburger::before,
.hamburger::after {
  content: "";
  position: absolute;
  width: 24px;
  height: 2px;
  background: #374151;
  transition: all 0.3s ease;
}

.hamburger::before {
  top: -7px;
}
.hamburger::after {
  bottom: -7px;
}

.hamburger.active {
  background: transparent;
}

.hamburger.active::before {
  top: 0;
  transform: rotate(45deg);
}

.hamburger.active::after {
  bottom: 0;
  transform: rotate(-45deg);
}

/* Responsive */
@media (max-width: 900px) {
  .nav-menu {
    position: fixed;
    top: 72px;
    left: 0;
    right: 0;
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    flex-direction: column;
    padding: 20px;
    gap: 8px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    transform: translateY(-100%);
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
  }

  .nav-menu.nav-open {
    transform: translateY(0);
    opacity: 1;
    visibility: visible;
  }

  .nav-link {
    width: 100%;
    justify-content: center;
    padding: 14px 20px;
  }

  .mobile-toggle {
    display: block;
  }

  .auth-buttons {
    display: none;
  }

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

@media (max-width: 640px) {
  .header-container {
    padding: 0 16px;
    height: 64px;
  }

  .logo-text {
    font-size: 22px;
  }

  .logo-icon {
    font-size: 28px;
  }
}
</style>
