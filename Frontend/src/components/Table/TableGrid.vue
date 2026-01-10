<template>
  <div class="table-grid-container">
    <!-- Stats Bar -->
    <div class="stats-bar">
      <div class="stat-item available">
        <span class="stat-icon">✅</span>
        <span class="stat-value">{{ stats.trong }}</span>
        <span class="stat-label">Trống</span>
      </div>
      <div class="stat-item reserved">
        <span class="stat-icon">📋</span>
        <span class="stat-value">{{ stats.daDat }}</span>
        <span class="stat-label">Đã đặt</span>
      </div>
      <div class="stat-item occupied">
        <span class="stat-icon">🍽️</span>
        <span class="stat-value">{{ stats.dangDung }}</span>
        <span class="stat-label">Đang dùng</span>
      </div>
      <div class="stat-item total">
        <span class="stat-icon">🪑</span>
        <span class="stat-value">{{ stats.total }}</span>
        <span class="stat-label">Tổng bàn</span>
      </div>
    </div>

    <!-- Filter by Zone -->
    <div v-if="zones.length > 0" class="zone-filter">
      <button 
        class="zone-btn" 
        :class="{ active: selectedZone === null }"
        @click="selectedZone = null"
      >
        Tất cả
      </button>
      <button 
        v-for="zone in zones" 
        :key="zone.KhuVucID"
        class="zone-btn"
        :class="{ active: selectedZone === zone.KhuVucID }"
        @click="selectedZone = zone.KhuVucID"
      >
        {{ zone.TenKhuVuc }}
      </button>
    </div>

    <!-- Loading -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Đang tải sơ đồ bàn...</p>
    </div>

    <!-- Tables Grid -->
    <div v-else class="tables-grid">
      <TableCard
        v-for="table in filteredTables"
        :key="table.BanID"
        :table="table"
        @click="handleTableClick"
      />
    </div>

    <!-- Empty State -->
    <div v-if="!loading && filteredTables.length === 0" class="empty-state">
      <span class="empty-icon">🪑</span>
      <p>Chưa có bàn nào</p>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import TableCard from './TableCard.vue'
import banService, { type Ban, type BanStats } from '@/services/ban.service'
import khuVucService, { type KhuVuc } from '@/services/khuvuc.service'

const emit = defineEmits<{
  'table-click': [table: Ban]
}>()

const loading = ref(true)
const tables = ref<Ban[]>([])
const zones = ref<KhuVuc[]>([])
const selectedZone = ref<number | null>(null)
const stats = ref<BanStats>({
  total: 0,
  trong: 0,
  daDat: 0,
  dangDung: 0
})

const filteredTables = computed(() => {
  if (selectedZone.value === null) {
    return tables.value
  }
  return tables.value.filter(t => t.KhuVucID === selectedZone.value)
})

const fetchData = async () => {
  loading.value = true
  try {
    const [tablesRes, zonesRes, statsRes] = await Promise.all([
      banService.getAll({ nha_hang_id: 1 }),
      khuVucService.getAll({ nha_hang_id: 1 }),
      banService.getStats(1)
    ])
    
    tables.value = tablesRes.data
    zones.value = zonesRes.data
    stats.value = statsRes.data
  } catch (error) {
    console.error('Error fetching tables:', error)
  } finally {
    loading.value = false
  }
}

const handleTableClick = (table: Ban) => {
  emit('table-click', table)
}

// Refresh data
const refresh = () => {
  fetchData()
}

defineExpose({ refresh })

onMounted(() => {
  fetchData()
})
</script>

<style scoped>
.table-grid-container {
  padding: 20px;
}

.stats-bar {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
  flex-wrap: wrap;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 15px 25px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
  flex: 1;
  min-width: 150px;
}

.stat-item.available {
  border-left: 4px solid #22c55e;
}

.stat-item.reserved {
  border-left: 4px solid #f59e0b;
}

.stat-item.occupied {
  border-left: 4px solid #ef4444;
}

.stat-item.total {
  border-left: 4px solid #6366f1;
}

.stat-icon {
  font-size: 1.5rem;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 800;
  color: #1f2937;
}

.stat-label {
  font-size: 0.9rem;
  color: #6b7280;
}

.zone-filter {
  display: flex;
  gap: 10px;
  margin-bottom: 25px;
  flex-wrap: wrap;
}

.zone-btn {
  padding: 10px 20px;
  border: 2px solid #e5e7eb;
  background: white;
  border-radius: 25px;
  font-size: 0.95rem;
  font-weight: 500;
  color: #4b5563;
  cursor: pointer;
  transition: all 0.3s ease;
}

.zone-btn:hover {
  border-color: #6366f1;
  color: #6366f1;
}

.zone-btn.active {
  background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
  border-color: transparent;
  color: white;
}

.tables-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 20px;
}

.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px;
  color: #6b7280;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e5e7eb;
  border-top-color: #6366f1;
  border-radius: 50%;
  animation: spin 1s linear infinite;
  margin-bottom: 15px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.empty-state {
  text-align: center;
  padding: 60px;
  color: #9ca3af;
}

.empty-icon {
  font-size: 4rem;
  display: block;
  margin-bottom: 15px;
  opacity: 0.5;
}

@media (max-width: 768px) {
  .stats-bar {
    gap: 10px;
  }
  
  .stat-item {
    padding: 12px 15px;
    min-width: 120px;
  }
  
  .stat-value {
    font-size: 1.4rem;
  }
  
  .tables-grid {
    grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
    gap: 15px;
  }
}
</style>
