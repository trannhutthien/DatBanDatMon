<template>
  <div class="dish-card">
    <div class="dish-image">
      <img :src="imageUrl" :alt="dish.TenMon" @error="handleImageError" />
      <span v-if="dish.TrangThai === 1" class="dish-badge">Còn món</span>
      <span v-else class="dish-badge sold-out">Hết món</span>
    </div>
    <div class="dish-info">
      <h3 class="dish-name">{{ dish.TenMon }}</h3>
      <p class="dish-desc">{{ dish.MoTa || 'Món ăn ngon' }}</p>
      <div class="dish-footer">
        <span class="dish-price">{{ formatPrice(dish.DonGia) }}đ</span>
        <Button 
          variant="primary" 
          size="small"
          :disabled="dish.TrangThai !== 1"
          @click="handleOrder"
        >
          🛒 Đặt món
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import Button from '@/components/ui/Button.vue'

export interface MonAn {
  MonAnID: number
  NhaHangID: number
  TenMon: string
  DonGia: number
  HinhAnh?: string
  MoTa?: string
  TrangThai: number
  DanhMucID?: number
  TaoLuc?: string
  CapNhatLuc?: string
}

const props = defineProps<{
  dish: MonAn
}>()

const emit = defineEmits<{
  order: [dish: MonAn]
}>()

// Ảnh mặc định khi không có hoặc lỗi
const defaultImage = '/favicon.ico'
const imageError = ref(false)

// Xử lý đường dẫn ảnh
const imageUrl = computed(() => {
  if (imageError.value || !props.dish.HinhAnh) {
    return defaultImage
  }
  // Nếu đường dẫn đã có / ở đầu thì giữ nguyên
  // Nếu không thì thêm / vào đầu
  const path = props.dish.HinhAnh
  if (path.startsWith('http') || path.startsWith('/')) {
    return path
  }
  return '/' + path
})

const handleImageError = () => {
  imageError.value = true
}

const formatPrice = (price: number) => {
  return price.toLocaleString('vi-VN')
}

const handleOrder = () => {
  if (props.dish.TrangThai === 1) {
    emit('order', props.dish)
  }
}
</script>

<style scoped>
.dish-card {
  background: white;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

.dish-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.dish-image {
  position: relative;
  height: 180px;
  overflow: hidden;
}

.dish-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.dish-card:hover .dish-image img {
  transform: scale(1.1);
}

.dish-badge {
  position: absolute;
  top: 12px;
  right: 12px;
  background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
  color: white;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
}

.dish-badge.sold-out {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.dish-info {
  padding: 20px;
}

.dish-name {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 8px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dish-desc {
  font-size: 0.9rem;
  color: #7f8c8d;
  margin-bottom: 15px;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
  line-height: 1.5;
  height: 2.7em;
}

.dish-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.dish-price {
  font-size: 1.3rem;
  font-weight: 800;
  color: #e53935;
}
</style>
