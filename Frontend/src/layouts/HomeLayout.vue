<template>
  <div class="home-layout">
    <!-- Header -->
    <header class="header">
      <div class="container">
        <div class="header-content">
          <div class="logo">
            <h1>Restaurant</h1>
          </div>
          
          <nav class="nav">
            <router-link to="/" class="nav-link">Trang chủ</router-link>
            <router-link to="/menu" class="nav-link">Thực đơn</router-link>
            <router-link to="/booking" class="nav-link">Đặt bàn</router-link>
            <router-link to="/contact" class="nav-link">Liên hệ</router-link>
          </nav>

          <div class="header-actions">
            <button class="btn-cart">
              <span class="cart-icon">🛒</span>
              <span class="cart-count">0</span>
            </button>
            <button class="btn-user">
              <span class="user-icon">👤</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
      <div class="container">
        <router-view />
      </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
      <div class="container">
        <div class="footer-content">
          <div class="footer-section">
            <h3>Về chúng tôi</h3>
            <p>Nhà hàng chuyên phục vụ các món ăn ngon, không gian sang trọng.</p>
          </div>
          
          <div class="footer-section">
            <h3>Liên hệ</h3>
            <p>📞 Điện thoại: 0123-456-789</p>
            <p>📧 Email: info@restaurant.com</p>
            <p>📍 Địa chỉ: 123 Đường ABC, Quận 1, TP.HCM</p>
          </div>
          
          <div class="footer-section">
            <h3>Giờ mở cửa</h3>
            <p>Thứ 2 - Thứ 6: 10:00 - 22:00</p>
            <p>Thứ 7 - CN: 09:00 - 23:00</p>
          </div>
        </div>
        
        <div class="footer-bottom">
          <p>&copy; 2025 Restaurant. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

// Cart count can be managed via Pinia store
const cartCount = ref(0)
</script>

<style scoped>
.home-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

/* Header Styles */
.header {
  background: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  position: sticky;
  top: 0;
  z-index: 1000;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 0;
}

.logo h1 {
  margin: 0;
  font-size: 1.8rem;
  color: #e74c3c;
  font-weight: bold;
}

.nav {
  display: flex;
  gap: 2rem;
}

.nav-link {
  text-decoration: none;
  color: #333;
  font-weight: 500;
  transition: color 0.3s;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #e74c3c;
}

.header-actions {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.btn-cart,
.btn-user {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 1.5rem;
  position: relative;
  padding: 0.5rem;
  transition: transform 0.2s;
}

.btn-cart:hover,
.btn-user:hover {
  transform: scale(1.1);
}

.cart-count {
  position: absolute;
  top: 0;
  right: 0;
  background: #e74c3c;
  color: white;
  font-size: 0.7rem;
  padding: 0.2rem 0.4rem;
  border-radius: 50%;
  min-width: 18px;
  text-align: center;
}

/* Main Content */
.main-content {
  flex: 1;
  padding: 2rem 0;
}

/* Footer Styles */
.footer {
  background: #2c3e50;
  color: #ecf0f1;
  padding: 3rem 0 1rem;
  margin-top: auto;
}

.footer-content {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 2rem;
  margin-bottom: 2rem;
}

.footer-section h3 {
  margin-bottom: 1rem;
  color: #e74c3c;
}

.footer-section p {
  margin: 0.5rem 0;
  line-height: 1.6;
}

.footer-bottom {
  text-align: center;
  padding-top: 2rem;
  border-top: 1px solid #34495e;
}

/* Responsive */
@media (max-width: 768px) {
  .nav {
    display: none;
  }
  
  .header-content {
    justify-content: space-between;
  }
  
  .footer-content {
    grid-template-columns: 1fr;
  }
}
</style>
