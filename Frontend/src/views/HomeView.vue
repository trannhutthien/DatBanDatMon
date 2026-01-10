<template>
  <div class="home-page">
    <!-- Banner Slider -->
    <Banner />

    <!-- Nhà hàng uy tín -->
    <TrustedRestaurants @view-all="goToRestaurants" />

    <!-- Popular Dishes - Lấy từ API -->
    <HomeFoot />
  </div>
</template>

<script setup lang="ts">
/**
 * ============================================================================
 * HOME VIEW - TRANG CHỦ
 * ============================================================================
 * 
 * Trang chủ của ứng dụng, bao gồm:
 * - Banner slider
 * - TrustedRestaurants (nhà hàng uy tín)
 * - HomeFoot (món ăn phổ biến - lấy từ API)
 * ============================================================================
 */

import { useRouter } from 'vue-router'
import Banner from '@/components/Home/Banner.vue'
import TrustedRestaurants from '@/components/Home/TrustedRestaurants.vue'
import HomeFoot from '@/components/Home/HomeFoot.vue'

const router = useRouter()

const goToRestaurants = () => {
  // Có thể navigate đến trang danh sách nhà hàng
  router.push('/order')
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.home-page {
  min-height: 100vh;
  background: #f8f9fa;
}

/* Hero */
.hero {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 100px 20px;
  text-align: center;
  color: white;
}

.hero-content h1 {
  font-size: 3.5rem;
  font-weight: 800;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.brand {
  background: linear-gradient(135deg, #ffd200 0%, #ff6f00 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.subtitle {
  font-size: 1.25rem;
  opacity: 0.95;
  margin-bottom: 2.5rem;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.btn-hero {
  padding: 16px 40px;
  font-size: 1.1rem;
  font-weight: 600;
  border: none;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-hero.primary {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
  box-shadow: 0 4px 20px rgba(229, 57, 53, 0.4);
}

.btn-hero.primary:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 30px rgba(229, 57, 53, 0.5);
}

.btn-hero.secondary {
  background: white;
  color: #667eea;
  box-shadow: 0 4px 20px rgba(255, 255, 255, 0.3);
}

.btn-hero.secondary:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 30px rgba(255, 255, 255, 0.4);
}

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Features */
.features {
  padding: 80px 20px;
  background: white;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 2.5rem;
}

.feature-card {
  text-align: center;
  padding: 2.5rem;
  background: #f8f9fa;
  border-radius: 20px;
  transition: all 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  font-size: 4rem;
  margin-bottom: 1.5rem;
}

.feature-card h3 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin-bottom: 1rem;
}

.feature-card p {
  color: #7f8c8d;
  font-size: 1rem;
  line-height: 1.6;
}

/* Popular section handled by PopularDishes component */

/* Responsive */
@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 2.5rem;
  }
  
  .subtitle {
    font-size: 1rem;
  }
  
  .feature-grid,
  .dishes-grid {
    grid-template-columns: 1fr;
  }
}
</style>
