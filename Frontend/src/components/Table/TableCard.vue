<template>
  <div 
    class="table-card" 
    :class="statusClass"
    @click="handleClick"
  >
    <div class="table-icon">
      <span class="icon">🪑</span>
      <span class="table-number">{{ table.SoBan }}</span>
    </div>
    
    <div class="table-info">
      <div class="seats">
        <span class="seat-icon">👤</span>
        <span>{{ table.SoGhe }} ghế</span>
      </div>
      <div class="status-badge" :style="{ background: statusColor }">
        {{ statusText }}
      </div>
    </div>

    <!-- Hiển thị tổng tiền nếu đang dùng -->
    <div v-if="table.TrangThai === 3 && currentBill" class="current-bill">
      💰 {{ formatPrice(currentBill) }}
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { Ban } from '@/services/ban.service'
import { TRANG_THAI_BAN_TEXT, TRANG_THAI_BAN_COLOR } from '@/services/ban.service'

interface Props {
  table: Ban
  currentBill?: number
}

const props = defineProps<Props>()

const emit = defineEmits<{
  click: [table: Ban]
}>()

const statusText = computed(() => TRANG_THAI_BAN_TEXT[props.table.TrangThai] || 'Không xác định')
const statusColor = computed(() => TRANG_THAI_BAN_COLOR[props.table.TrangThai] || '#6b7280')

const statusClass = computed(() => {
  switch (props.table.TrangThai) {
    case 1: return 'status-available'
    case 2: return 'status-reserved'
    case 3: return 'status-occupied'
    default: return ''
  }
})

const formatPrice = (price: number) => {
  return price.toLocaleString('vi-VN') + 'đ'
}

const handleClick = () => {
  emit('click', props.table)
}
</script>

<style scoped>
.table-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 3px solid transparent;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 12px;
  min-width: 140px;
}

.table-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.table-card.status-available {
  border-color: #22c55e;
  background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.table-card.status-available:hover {
  border-color: #16a34a;
}

.table-card.status-reserved {
  border-color: #f59e0b;
  background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.table-card.status-reserved:hover {
  border-color: #d97706;
}

.table-card.status-occupied {
  border-color: #ef4444;
  background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.table-card.status-occupied:hover {
  border-color: #dc2626;
}

.table-icon {
  position: relative;
  font-size: 3rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.table-number {
  position: absolute;
  font-size: 1rem;
  font-weight: 800;
  color: #1f2937;
  background: white;
  padding: 2px 8px;
  border-radius: 20px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
  top: -5px;
  right: -15px;
}

.table-info {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.seats {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 0.9rem;
  color: #6b7280;
}

.seat-icon {
  font-size: 0.85rem;
}

.status-badge {
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  color: white;
}

.current-bill {
  font-size: 0.95rem;
  font-weight: 700;
  color: #dc2626;
  background: #fef2f2;
  padding: 6px 12px;
  border-radius: 8px;
  border: 1px solid #fecaca;
}
</style>
