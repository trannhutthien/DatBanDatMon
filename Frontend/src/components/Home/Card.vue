<template>
  <div class="table-card" :class="statusClass">
    <div class="table-icon">🪑</div>
    <div class="table-info">
      <h3 class="table-number">Bàn {{ ban.SoBan }}</h3>
      <p class="table-seats">{{ ban.SoGhe }} ghế</p>
      <span class="table-status" :style="{ background: statusColor }">
        {{ statusText }}
      </span>
    </div>
    <div class="table-actions">
      <Button 
        variant="outline" 
        size="small" 
        :block="true"
        @click="handleViewDetail"
      >
        👁️ Xem chi tiết
      </Button>
      <Button 
        variant="primary" 
        size="small" 
        :block="true"
        :disabled="!isAvailable"
        @click="handleBookTable"
      >
        📅 Đặt bàn
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import Button from '@/components/ui/Button.vue'
import type { Ban } from '@/services/ban.service'
import { TRANG_THAI_BAN, TRANG_THAI_BAN_TEXT, TRANG_THAI_BAN_COLOR } from '@/services/ban.service'

const props = defineProps<{
  ban: Ban
}>()

const emit = defineEmits<{
  viewDetail: [ban: Ban]
  bookTable: [ban: Ban]
}>()

const statusText = computed(() => TRANG_THAI_BAN_TEXT[props.ban.TrangThai] || 'Không xác định')
const statusColor = computed(() => TRANG_THAI_BAN_COLOR[props.ban.TrangThai] || '#999')

// Chỉ cho phép đặt bàn khi bàn đang trống
const isAvailable = computed(() => props.ban.TrangThai === TRANG_THAI_BAN.TRONG)

const statusClass = computed(() => {
  switch (props.ban.TrangThai) {
    case 1: return 'status-available'
    case 2: return 'status-reserved'
    case 3: return 'status-occupied'
    default: return ''
  }
})

const handleViewDetail = () => {
  emit('viewDetail', props.ban)
}

const handleBookTable = () => {
  if (isAvailable.value) {
    emit('bookTable', props.ban)
  }
}
</script>

<style scoped>
.table-card {
  background: white;
  border-radius: 20px;
  padding: 25px 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  text-align: center;
  border: 3px solid transparent;
}

.table-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.table-card.status-available {
  border-color: #22c55e;
}

.table-card.status-reserved {
  border-color: #f59e0b;
}

.table-card.status-occupied {
  border-color: #ef4444;
}

.table-icon {
  font-size: 3.5rem;
  margin-bottom: 12px;
}

.table-info {
  text-align: center;
}

.table-number {
  font-size: 1.4rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 5px;
}

.table-seats {
  font-size: 0.9rem;
  color: #7f8c8d;
  margin-bottom: 12px;
}

.table-status {
  display: inline-block;
  padding: 6px 16px;
  border-radius: 20px;
  color: white;
  font-size: 0.85rem;
  font-weight: 600;
}

.table-actions {
  margin-top: 15px;
  display: flex;
  flex-direction: column;
  gap: 10px;
}
</style>
