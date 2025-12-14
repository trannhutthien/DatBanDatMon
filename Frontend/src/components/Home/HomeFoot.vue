<template>
  <section class="popular-dishes">
    <div class="container">
      <h2 class="section-title">Món ăn mới nhất</h2>
      
      <!-- Loading State -->
      <div v-if="loading" class="loading-grid">
        <div v-for="n in 10" :key="n" class="skeleton-card">
          <div class="skeleton-image"></div>
          <div class="skeleton-text"></div>
          <div class="skeleton-text short"></div>
        </div>
      </div>
      
      <!-- Error State -->
      <div v-else-if="error" class="error-state">
        <p>{{ error }}</p>
        <button @click="fetchDishes" class="retry-btn">Thử lại</button>
      </div>
      
      <!-- Dishes Grid -->
      <div v-else class="dishes-grid">
        <Card 
          v-for="dish in dishes" 
          :key="dish.MonAnID"
          :dish="transformDish(dish)"
          @add-to-cart="handleAddToCart"
        />
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
/**
 * ============================================================================
 * HOME FOOT COMPONENT
 * ============================================================================
 * 
 * Component hiển thị danh sách 10 món ăn mới nhất ở cuối trang chủ.
 * Gọi API từ backend để lấy dữ liệu thật.
 * 
 * SỬ DỤNG:
 * - monAnService.getLatest(10) để lấy 10 món ăn mới nhất
 * - Card component để hiển thị từng món
 * ============================================================================
 */

import { ref, onMounted } from 'vue'
import Card from './Card.vue'
import { monAnService, type MonAn } from '@/services/monan.service'

// State
const dishes = ref<MonAn[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

/**
 * Chuyển đổi dữ liệu từ API sang format của Card component
 * 
 * @param dish - Món ăn từ API
 * @returns Object phù hợp với Card props
 */
const transformDish = (dish: MonAn) => {
  return {
    id: dish.MonAnID,
    name: dish.TenMon,
    restaurant: dish.nha_hang?.TenNhaHang || 'Chưa có nhà hàng',
    restaurantAddress: dish.nha_hang?.DiaChi || '',
    price: Number(dish.DonGia),
    image: dish.HinhAnh || undefined,
    // Tạo emoji dựa trên tên món (fallback)
    emoji: getEmojiForDish(dish.TenMon),
    // Có thể thêm rating, discount từ API sau
    rating: 4.5 + Math.random() * 0.5,  // Random 4.5-5.0 (tạm thời)
  }
}

/**
 * Lấy emoji phù hợp với tên món
 */
const getEmojiForDish = (name: string): string => {
  const lower = name.toLowerCase()
  if (lower.includes('phở') || lower.includes('bún') || lower.includes('mì')) return '🍜'
  if (lower.includes('cơm')) return '🍚'
  if (lower.includes('gà')) return '🍗'
  if (lower.includes('bò') || lower.includes('thịt')) return '🥩'
  if (lower.includes('cá') || lower.includes('hải sản')) return '🐟'
  if (lower.includes('bánh mì')) return '🥖'
  if (lower.includes('bánh')) return '🍰'
  if (lower.includes('chè') || lower.includes('kem')) return '🍨'
  if (lower.includes('gỏi') || lower.includes('salad')) return '🥗'
  if (lower.includes('nước') || lower.includes('trà') || lower.includes('cà phê')) return '🥤'
  return '🍽️'
}

/**
 * Gọi API lấy 10 món ăn mới nhất
 * Sắp xếp theo ngày thêm (TaoLuc) giảm dần
 */
const fetchDishes = async () => {
  loading.value = true
  error.value = null
  
  try {
    // Gọi API lấy 10 món ăn mới nhất
    const response = await monAnService.getLatest(10)
    dishes.value = response.data
  } catch (err: any) {
    console.error('Error fetching dishes:', err)
    error.value = 'Không thể tải danh sách món ăn. Vui lòng thử lại.'
    
    // Fallback data nếu API lỗi
    dishes.value = []
  } finally {
    loading.value = false
  }
}

/**
 * Xử lý thêm vào giỏ hàng
 */
const handleAddToCart = (dish: any) => {
  console.log('Added to cart:', dish)
  alert(`Đã thêm "${dish.name}" vào giỏ hàng!`)
}

// Gọi API khi component mount
onMounted(() => {
  fetchDishes()
})
</script>

<style scoped>
.popular-dishes {
  padding: 80px 20px;
  background: #f8f9fa;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.section-title {
  font-size: 2.5rem;
  text-align: center;
  color: #2c3e50;
  margin-bottom: 3rem;
  font-weight: 800;
  position: relative;
}

.section-title::after {
  content: '';
  position: absolute;
  bottom: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  border-radius: 2px;
}

.dishes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 30px;
}

/* Loading Skeleton */
.loading-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 30px;
}

.skeleton-card {
  background: white;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.skeleton-image {
  width: 100%;
  height: 180px;
  border-radius: 16px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

.skeleton-text {
  height: 20px;
  border-radius: 4px;
  margin-top: 15px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

.skeleton-text.short {
  width: 60%;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* Error State */
.error-state {
  text-align: center;
  padding: 60px 20px;
  color: #7f8c8d;
}

.retry-btn {
  margin-top: 20px;
  padding: 12px 30px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Responsive */
@media (max-width: 768px) {
  .popular-dishes {
    padding: 60px 20px;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .dishes-grid,
  .loading-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
  }
}

@media (max-width: 480px) {
  .dishes-grid,
  .loading-grid {
    grid-template-columns: 1fr;
  }
}
</style>
