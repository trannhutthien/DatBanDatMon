<template>
  <div class="restaurant-card" @click="emit('click', props.restaurant)">
    <!-- Header với hình ảnh -->
    <div class="card-header">
      <div class="restaurant-image">
        <img v-if="props.restaurant.HinhAnh" :src="props.restaurant.HinhAnh" :alt="props.restaurant.TenNhaHang" />
        <div v-else class="placeholder-image">
          <span class="restaurant-icon">🏪</span>
        </div>
      </div>
      
      <!-- Badge trạng thái -->
      <div class="status-badge" :class="{ active: props.restaurant.TrangThai === 1 }">
        {{ props.restaurant.TrangThai === 1 ? '🟢 Đang mở' : '🔴 Đóng cửa' }}
      </div>
      
      <!-- Badge uy tín -->
      <div class="trust-badge">
        <span class="badge-icon">⭐</span>
        <span class="badge-text">Uy tín</span>
      </div>
    </div>

    <!-- Nội dung -->
    <div class="card-body">
      <h3 class="restaurant-name">{{ props.restaurant.TenNhaHang }}</h3>
      
      <div class="info-row">
        <span class="info-icon">📍</span>
        <span class="info-text">{{ props.restaurant.DiaChi || 'Chưa cập nhật địa chỉ' }}</span>
      </div>
      
      <div v-if="props.restaurant.SDT" class="info-row">
        <span class="info-icon">📞</span>
        <span class="info-text">{{ props.restaurant.SDT }}</span>
      </div>
      
      <p v-if="props.restaurant.MoTa" class="description">{{ truncateText(props.restaurant.MoTa, 80) }}</p>
      
      <!-- Stats giả lập -->
      <div class="stats-row">
        <div class="stat-item">
          <span class="stat-value">{{ randomRating }}</span>
          <span class="stat-label">⭐ Đánh giá</span>
        </div>
        <div class="stat-item">
          <span class="stat-value">{{ randomOrders }}+</span>
          <span class="stat-label">🛒 Đơn hàng</span>
        </div>
        <div class="stat-item">
          <span class="stat-value">{{ randomTime }} phút</span>
          <span class="stat-label">🚚 Giao hàng</span>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="card-footer">
      <button class="view-menu-btn" @click.stop="emit('view-menu', props.restaurant)">
        <span>📋</span> Xem thực đơn
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { NhaHang } from '@/services/nhahang.service'

const props = defineProps<{
  restaurant: NhaHang
}>()

const emit = defineEmits<{
  click: [restaurant: NhaHang]
  'view-menu': [restaurant: NhaHang]
}>()

// Random stats (sau này thay bằng data thật)
const randomRating = computed(() => (4.2 + Math.random() * 0.8).toFixed(1))
const randomOrders = computed(() => Math.floor(100 + Math.random() * 900))
const randomTime = computed(() => Math.floor(15 + Math.random() * 30))

const truncateText = (text: string, maxLength: number): string => {
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}
</script>

<style scoped>
.restaurant-card {
  background: white;
  border-radius: 24px;
  overflow: hidden;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  cursor: pointer;
  position: relative;
}

.restaurant-card:hover {
  transform: translateY(-12px);
  box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
}

/* Header */
.card-header {
  position: relative;
  height: 180px;
  overflow: hidden;
}

.restaurant-image {
  width: 100%;
  height: 100%;
}

.restaurant-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.restaurant-card:hover .restaurant-image img {
  transform: scale(1.1);
}

.placeholder-image {
  width: 100%;
  height: 100%;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.restaurant-icon {
  font-size: 4rem;
  filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.2));
}

/* Badges */
.status-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(10px);
  color: white;
  padding: 8px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.status-badge.active {
  background: rgba(46, 125, 50, 0.9);
}

.trust-badge {
  position: absolute;
  top: 15px;
  right: 15px;
  background: linear-gradient(135deg, #ffd700 0%, #ffb300 100%);
  color: #5d4037;
  padding: 8px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
  display: flex;
  align-items: center;
  gap: 4px;
  box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
}

.badge-icon {
  font-size: 0.9rem;
}

/* Body */
.card-body {
  padding: 24px;
}

.restaurant-name {
  font-size: 1.4rem;
  font-weight: 800;
  color: #1a1a2e;
  margin-bottom: 16px;
  line-height: 1.3;
}

.info-row {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  margin-bottom: 10px;
  color: #64748b;
  font-size: 0.9rem;
}

.info-icon {
  flex-shrink: 0;
}

.info-text {
  line-height: 1.4;
}

.description {
  color: #94a3b8;
  font-size: 0.85rem;
  line-height: 1.5;
  margin: 16px 0;
  padding: 12px;
  background: #f8fafc;
  border-radius: 12px;
  border-left: 3px solid #667eea;
}

/* Stats */
.stats-row {
  display: flex;
  justify-content: space-between;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #f1f5f9;
}

.stat-item {
  text-align: center;
  flex: 1;
}

.stat-value {
  display: block;
  font-size: 1.1rem;
  font-weight: 800;
  color: #1a1a2e;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.75rem;
  color: #94a3b8;
}

/* Footer */
.card-footer {
  padding: 0 24px 24px;
}

.view-menu-btn {
  width: 100%;
  padding: 14px 24px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 14px;
  font-size: 1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.view-menu-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

.view-menu-btn:active {
  transform: translateY(0);
}

/* Hover shine effect */
.restaurant-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.3),
    transparent
  );
  transition: left 0.6s ease;
  z-index: 10;
  pointer-events: none;
}

.restaurant-card:hover::before {
  left: 100%;
}

/* Responsive */
@media (max-width: 640px) {
  .card-header {
    height: 150px;
  }
  
  .restaurant-icon {
    font-size: 3rem;
  }
  
  .card-body {
    padding: 20px;
  }
  
  .restaurant-name {
    font-size: 1.2rem;
  }
  
  .stats-row {
    flex-wrap: wrap;
    gap: 15px;
  }
  
  .stat-item {
    flex: 0 0 calc(50% - 10px);
  }
}
</style>
