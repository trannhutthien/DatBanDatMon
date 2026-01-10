<template>
  <div class="home-page">
    <!-- Hero Banner -->
    <section class="hero-banner">
      <div class="hero-content">
        <h1>🍜 Chào mừng đến với <span class="brand">Ăn Ngon</span></h1>
        <p class="subtitle">Khám phá những nhà hàng ngon nhất, đặt bàn dễ dàng</p>
      </div>
    </section>

    <div class="container">
      <h2 class="section-title">🏪 Danh sách nhà hàng</h2>

      <!-- Loading -->
      <div v-if="loading" class="loading">
        <p>Đang tải danh sách nhà hàng...</p>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="error">
        <p>{{ error }}</p>
        <button @click="fetchRestaurants" class="retry-btn">Thử lại</button>
      </div>

      <!-- Empty -->
      <div v-else-if="restaurants.length === 0" class="empty">
        <p>Chưa có nhà hàng nào</p>
      </div>

      <!-- Danh sách nhà hàng -->
      <div v-else class="restaurants-grid">
        <RestaurantCard 
          v-for="restaurant in restaurants" 
          :key="restaurant.NhaHangID"
          :restaurant="restaurant"
          @click="handleRestaurantClick"
          @view-menu="handleViewMenu"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import RestaurantCard from '@/components/Home/RestaurantCard.vue'
import nhaHangService, { type NhaHang } from '@/services/nhahang.service'

const router = useRouter()

const restaurants = ref<NhaHang[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

const fetchRestaurants = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await nhaHangService.getAll({ per_page: 100 })
    restaurants.value = response.data.data || response.data as any
  } catch (err: any) {
    console.error('Error fetching restaurants:', err)
    error.value = 'Không thể tải danh sách nhà hàng. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

const handleRestaurantClick = (restaurant: NhaHang) => {
  // Chuyển đến trang đặt bàn với filter theo nhà hàng
  router.push({ path: '/order', query: { restaurant: restaurant.NhaHangID.toString() } })
}

const handleViewMenu = (restaurant: NhaHang) => {
  // Chuyển đến trang món ăn
  router.push({ path: '/dishes', query: { restaurant: restaurant.NhaHangID.toString() } })
}

onMounted(() => {
  fetchRestaurants()
})
</script>

<style scoped>
.home-page {
  min-height: 100vh;
  background: #f8f9fa;
}

/* Hero Banner */
.hero-banner {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 60px 20px;
  text-align: center;
  color: white;
}

.hero-content h1 {
  font-size: 2.5rem;
  font-weight: 800;
  margin-bottom: 15px;
}

.brand {
  background: linear-gradient(135deg, #ffd200 0%, #ff6f00 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.subtitle {
  font-size: 1.1rem;
  opacity: 0.9;
}

/* Container */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 40px 20px;
}

.section-title {
  text-align: center;
  font-size: 1.8rem;
  color: #2c3e50;
  margin-bottom: 30px;
}

/* Grid */
.restaurants-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 30px;
}

/* States */
.loading,
.error,
.empty {
  text-align: center;
  padding: 60px 20px;
  color: #7f8c8d;
}

.retry-btn {
  margin-top: 15px;
  padding: 10px 25px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.retry-btn:hover {
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .hero-content h1 {
    font-size: 1.8rem;
  }
  
  .restaurants-grid {
    grid-template-columns: 1fr;
  }
}
</style>
