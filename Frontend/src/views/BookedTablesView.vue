<template>
  <div class="booked-tables-page">
    <div class="container">
      <h1 class="page-title">📋 Danh sách bàn đã đặt</h1>
      
      <!-- Filter khu vực -->
      <AreaFilter 
        :areas="khuVucs"
        :selected-area="selectedKhuVuc"
        @update:selected-area="handleSelectArea"
      />

      <!-- Loading -->
      <div v-if="loading" class="loading">
        <p>Đang tải danh sách bàn đã đặt...</p>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="error">
        <p>{{ error }}</p>
        <button @click="fetchBans" class="retry-btn">Thử lại</button>
      </div>

      <!-- Empty -->
      <div v-else-if="filteredBans.length === 0" class="empty">
        <p>Không có bàn nào đã được đặt trong khu vực này</p>
      </div>

      <!-- Danh sách bàn đã đặt -->
      <div v-else class="tables-grid">
        <Card 
          v-for="ban in filteredBans" 
          :key="ban.BanID"
          :ban="ban"
          @view-detail="handleViewDetail"
          @book-table="handleViewBooking"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import Card from '@/components/Home/Card.vue'
import AreaFilter from '@/components/ui/AreaFilter.vue'
import banService, { type Ban, type KhuVuc } from '@/services/ban.service'
import khuVucService from '@/services/khuvuc.service'

const bans = ref<Ban[]>([])
const khuVucs = ref<KhuVuc[]>([])
const selectedKhuVuc = ref<KhuVuc | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)

// Lọc bàn theo khu vực đã chọn (chỉ lấy bàn có TrangThai = 2)
const filteredBans = computed(() => {
  // Chỉ lấy bàn đã đặt (TrangThai = 2)
  let bookedBans = bans.value.filter(ban => ban.TrangThai === 2)
  
  // Lọc thêm theo khu vực nếu có chọn
  if (selectedKhuVuc.value) {
    bookedBans = bookedBans.filter(ban => ban.KhuVucID === selectedKhuVuc.value?.KhuVucID)
  }
  
  return bookedBans
})

const fetchKhuVucs = async () => {
  try {
    const response = await khuVucService.getAll()
    khuVucs.value = response.data
  } catch (err) {
    console.error('Error fetching khu vucs:', err)
  }
}

const fetchBans = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await banService.getAll()
    bans.value = response.data
  } catch (err: any) {
    console.error('Error fetching bans:', err)
    error.value = 'Không thể tải danh sách bàn. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

const handleSelectArea = (area: KhuVuc | null) => {
  selectedKhuVuc.value = area
}

const handleViewDetail = (ban: Ban) => {
  const khuVuc = khuVucs.value.find(k => k.KhuVucID === ban.KhuVucID)
  alert(
    `Chi tiết Bàn ${ban.SoBan}\n\n` +
    `ID: ${ban.BanID}\n` +
    `Số ghế: ${ban.SoGhe}\n` +
    `Khu vực: ${khuVuc?.TenKhuVuc || 'Chưa phân khu'}\n` +
    `Trạng thái: Đã đặt`
  )
}

const handleViewBooking = (ban: Ban) => {
  // Xem thông tin đặt bàn
  alert(`Xem thông tin đặt bàn của Bàn ${ban.SoBan}`)
}

onMounted(() => {
  fetchKhuVucs()
  fetchBans()
})
</script>

<style scoped>
.booked-tables-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 40px 20px;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
}

.page-title {
  text-align: center;
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 30px;
}

.tables-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 24px;
}

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
</style>
