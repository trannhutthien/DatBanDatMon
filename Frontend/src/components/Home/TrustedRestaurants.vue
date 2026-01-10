<template>
  <section class="trusted-restaurants">
    <div class="container">
      <!-- Section Header -->
      <div class="section-header">
        <div class="header-content">
          <span class="section-badge">🏆 Đối tác chất lượng</span>
          <h2 class="section-title">Nhà Hàng Uy Tín</h2>
          <p class="section-subtitle">
            Những nhà hàng được đánh giá cao nhất, cam kết chất lượng và dịch vụ tốt nhất
          </p>
        </div>
        
        <!-- Filter tabs -->
        <div class="filter-tabs">
          <button 
            v-for="tab in filterTabs" 
            :key="tab.value"
            class="filter-tab"
            :class="{ active: activeFilter === tab.value }"
            @click="activeFilter = tab.value"
          >
            <span class="tab-icon">{{ tab.icon }}</span>
            {{ tab.label }}
          </button>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="loading-container">
        <div class="loading-grid">
          <div v-for="n in 6" :key="n" class="skeleton-card">
            <div class="skeleton-image"></div>
            <div class="skeleton-body">
              <div class="skeleton-title"></div>
              <div class="skeleton-text"></div>
              <div class="skeleton-text short"></div>
              <div class="skeleton-stats">
                <div class="skeleton-stat"></div>
                <div class="skeleton-stat"></div>
                <div class="skeleton-stat"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="error-container">
        <div class="error-content">
          <span class="error-icon">😕</span>
          <h3>Không thể tải dữ liệu</h3>
          <p>{{ error }}</p>
          <button @click="fetchRestaurants" class="retry-btn">
            <span>🔄</span> Thử lại
          </button>
        </div>
      </div>

      <!-- Restaurants Grid -->
      <div v-else class="restaurants-grid">
        <RestaurantCard
          v-for="restaurant in filteredRestaurants"
          :key="restaurant.NhaHangID"
          :restaurant="restaurant"
          @click="handleRestaurantClick"
          @view-menu="handleViewMenu"
        />
      </div>

      <!-- Empty State -->
      <div v-if="!loading && !error && filteredRestaurants.length === 0" class="empty-container">
        <div class="empty-content">
          <span class="empty-icon">🔍</span>
          <h3>Không tìm thấy nhà hàng</h3>
          <p>Thử thay đổi bộ lọc để xem thêm nhà hàng khác</p>
        </div>
      </div>

      <!-- View All Button -->
      <div v-if="!loading && filteredRestaurants.length > 0" class="view-all-container">
        <button class="view-all-btn" @click="$emit('view-all')">
          Xem tất cả nhà hàng
          <span class="arrow">→</span>
        </button>
      </div>
    </div>

    <!-- Decorative elements -->
    <div class="decoration decoration-1"></div>
    <div class="decoration decoration-2"></div>
  </section>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import RestaurantCard from './RestaurantCard.vue'
import nhaHangService, { type NhaHang } from '@/services/nhahang.service'

defineEmits<{
  'view-all': []
}>()

// State
const restaurants = ref<NhaHang[]>([])
const loading = ref(true)
const error = ref<string | null>(null)
const activeFilter = ref('all')

// Filter tabs
const filterTabs = [
  { value: 'all', label: 'Tất cả', icon: '🏪' },
  { value: 'open', label: 'Đang mở', icon: '🟢' },
  { value: 'popular', label: 'Phổ biến', icon: '🔥' },
]

// Computed
const filteredRestaurants = computed(() => {
  let result = restaurants.value

  switch (activeFilter.value) {
    case 'open':
      result = result.filter(r => r.TrangThai === 1)
      break
    case 'popular':
      // Giả lập: lấy 4 nhà hàng đầu tiên là "phổ biến"
      result = result.slice(0, 4)
      break
  }

  return result
})

// Methods
const fetchRestaurants = async () => {
  loading.value = true
  error.value = null

  try {
    const response = await nhaHangService.getAll({ 
      trang_thai: 1, // Chỉ lấy nhà hàng đang hoạt động
      per_page: 8 
    })
    restaurants.value = response.data.data
  } catch (err: any) {
    console.error('Error fetching restaurants:', err)
    error.value = 'Không thể tải danh sách nhà hàng. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

const handleRestaurantClick = (restaurant: NhaHang) => {
  console.log('Restaurant clicked:', restaurant)
}

const handleViewMenu = (restaurant: NhaHang) => {
  console.log('View menu:', restaurant)
  // Có thể navigate đến trang menu của nhà hàng
}

// Lifecycle
onMounted(() => {
  fetchRestaurants()
})
</script>

<style scoped>
.trusted-restaurants {
  position: relative;
  padding: 100px 20px;
  background: linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
  overflow: hidden;
}

.container {
  max-width: 1300px;
  margin: 0 auto;
  position: relative;
  z-index: 1;
}

/* Section Header */
.section-header {
  text-align: center;
  margin-bottom: 60px;
}

.header-content {
  margin-bottom: 40px;
}

.section-badge {
  display: inline-block;
  background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%);
  color: #5d4037;
  padding: 10px 24px;
  border-radius: 30px;
  font-size: 0.9rem;
  font-weight: 700;
  margin-bottom: 20px;
  box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3);
}

.section-title {
  font-size: 3rem;
  font-weight: 900;
  color: #1a1a2e;
  margin-bottom: 16px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.section-subtitle {
  font-size: 1.15rem;
  color: #64748b;
  max-width: 600px;
  margin: 0 auto;
  line-height: 1.7;
}

/* Filter Tabs */
.filter-tabs {
  display: flex;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
}

.filter-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 24px;
  background: white;
  border: 2px solid #e2e8f0;
  border-radius: 30px;
  font-size: 0.95rem;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-tab:hover {
  border-color: #667eea;
  color: #667eea;
}

.filter-tab.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: transparent;
  color: white;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

.tab-icon {
  font-size: 1.1rem;
}

/* Restaurants Grid */
.restaurants-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 30px;
}

/* Loading */
.loading-container {
  padding: 20px 0;
}

.loading-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 30px;
}

.skeleton-card {
  background: white;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
}

.skeleton-image {
  height: 180px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

.skeleton-body {
  padding: 24px;
}

.skeleton-title {
  height: 28px;
  width: 70%;
  border-radius: 8px;
  margin-bottom: 16px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

.skeleton-text {
  height: 16px;
  border-radius: 4px;
  margin-bottom: 10px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

.skeleton-text.short {
  width: 50%;
}

.skeleton-stats {
  display: flex;
  gap: 20px;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #f1f5f9;
}

.skeleton-stat {
  flex: 1;
  height: 40px;
  border-radius: 8px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e8e8e8 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  0% { background-position: -200% 0; }
  100% { background-position: 200% 0; }
}

/* Error State */
.error-container {
  padding: 80px 20px;
}

.error-content {
  text-align: center;
  max-width: 400px;
  margin: 0 auto;
}

.error-icon {
  font-size: 5rem;
  display: block;
  margin-bottom: 20px;
}

.error-content h3 {
  font-size: 1.5rem;
  color: #1a1a2e;
  margin-bottom: 10px;
}

.error-content p {
  color: #64748b;
  margin-bottom: 24px;
}

.retry-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 14px 32px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 30px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

/* Empty State */
.empty-container {
  padding: 80px 20px;
}

.empty-content {
  text-align: center;
}

.empty-icon {
  font-size: 5rem;
  display: block;
  margin-bottom: 20px;
  opacity: 0.5;
}

.empty-content h3 {
  font-size: 1.5rem;
  color: #1a1a2e;
  margin-bottom: 10px;
}

.empty-content p {
  color: #64748b;
}

/* View All Button */
.view-all-container {
  text-align: center;
  margin-top: 50px;
}

.view-all-btn {
  display: inline-flex;
  align-items: center;
  gap: 12px;
  padding: 16px 40px;
  background: white;
  border: 2px solid #667eea;
  border-radius: 30px;
  font-size: 1.1rem;
  font-weight: 700;
  color: #667eea;
  cursor: pointer;
  transition: all 0.3s ease;
}

.view-all-btn:hover {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: transparent;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.view-all-btn .arrow {
  transition: transform 0.3s ease;
}

.view-all-btn:hover .arrow {
  transform: translateX(5px);
}

/* Decorations */
.decoration {
  position: absolute;
  border-radius: 50%;
  opacity: 0.1;
  pointer-events: none;
}

.decoration-1 {
  width: 400px;
  height: 400px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  top: -100px;
  right: -100px;
}

.decoration-2 {
  width: 300px;
  height: 300px;
  background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%);
  bottom: -50px;
  left: -50px;
}

/* Responsive */
@media (max-width: 1024px) {
  .trusted-restaurants {
    padding: 80px 20px;
  }
  
  .section-title {
    font-size: 2.5rem;
  }
}

@media (max-width: 768px) {
  .trusted-restaurants {
    padding: 60px 15px;
  }
  
  .section-title {
    font-size: 2rem;
  }
  
  .section-subtitle {
    font-size: 1rem;
  }
  
  .filter-tabs {
    gap: 8px;
  }
  
  .filter-tab {
    padding: 10px 18px;
    font-size: 0.85rem;
  }
  
  .restaurants-grid,
  .loading-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
}

@media (max-width: 480px) {
  .section-badge {
    font-size: 0.8rem;
    padding: 8px 18px;
  }
  
  .section-title {
    font-size: 1.75rem;
  }
  
  .filter-tab {
    padding: 8px 14px;
    font-size: 0.8rem;
  }
  
  .tab-icon {
    font-size: 1rem;
  }
}
</style>
