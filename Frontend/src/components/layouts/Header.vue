<template>
  <header class="header">
    <div class="header-container">
      <!-- Phần bên trái: Logo -->
      <div class="header-left">
        <a href="/" class="logo-link">Ăn Ngon</a>
      </div>

      <!-- Phần giữa: Menu buttons -->
      <div class="header-center">
        <button class="menu-btn">Ăn Uống</button>
        <button class="menu-btn">Nhà Hàng Uy Tín</button>
        <button class="menu-btn">Ưu Đãi Hot</button>
        <button class="menu-btn">Mới Nhất</button>
      </div>

      <!-- Phần bên phải: Auth buttons -->
      <div class="header-right">
        <button class="auth-btn btn-register" @click="goToRegister">Đăng Ký</button>
        <button class="auth-btn btn-login" @click="goToLogin">Đăng Nhập</button>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const goToRegister = () => {
  router.push('/register')
}

const goToLogin = () => {
  router.push('/login')
}
</script>

<style scoped>
.header {
  height: 85px;
  width: 100%;
  max-width: 100%;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.header-container {
  height: 100%;
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

/* Phần bên trái - Logo */
.header-left {
  flex-shrink: 0;
}

.logo-link {
  text-decoration: none;
  font-size: 1.8rem;
  font-weight: bold;
  color: #e74c3c;
  white-space: nowrap;
}

.logo-link:hover {
  color: #c0392b;
}

/* Phần giữa - Menu buttons (rộng nhất) */
.header-center {
  flex: 1;
  display: flex;
  justify-content: center;
  gap: 15px;
  max-width: 700px;
}

.menu-btn {
  padding: 10px 20px;
  background: #fff;
  border: 2px solid #e74c3c;
  color: #e74c3c;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.menu-btn:hover {
  background: #e74c3c;
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(231, 76, 60, 0.3);
}

/* Phần bên phải - Auth buttons */
.header-right {
  flex-shrink: 0;
  display: flex;
  gap: 10px;
}

.auth-btn {
  padding: 10px 25px;
  font-size: 0.95rem;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  white-space: nowrap;
}

.btn-register {
  background: #fff;
  border: 2px solid #3498db;
  color: #3498db;
}

.btn-register:hover {
  background: #3498db;
  color: #fff;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(52, 152, 219, 0.3);
}

.btn-login {
  background: #e74c3c;
  border: 2px solid #e74c3c;
  color: #fff;
}

.btn-login:hover {
  background: #c0392b;
  border-color: #c0392b;
  transform: translateY(-2px);
  box-shadow: 0 4px 8px rgba(231, 76, 60, 0.4);
}

/* Responsive */
@media (max-width: 1024px) {
  .header-center {
    gap: 10px;
  }
  
  .menu-btn {
    padding: 8px 15px;
    font-size: 0.85rem;
  }
  
  .auth-btn {
    padding: 8px 20px;
    font-size: 0.85rem;
  }
}

@media (max-width: 768px) {
  .header {
    height: auto;
    min-height: 90px;
  }
  
  .header-container {
    flex-wrap: wrap;
    padding: 15px 20px;
  }
  
  .header-left {
    order: 1;
  }
  
  .header-right {
    order: 2;
  }
  
  .header-center {
    order: 3;
    width: 100%;
    margin-top: 10px;
    max-width: 100%;
  }
  
  .menu-btn {
    flex: 1;
    padding: 8px 10px;
    font-size: 0.8rem;
  }
}
</style>
