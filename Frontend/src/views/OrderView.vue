<template>
  <div class="order-page">
    <div class="container">
      <!-- Hiển thị tên nhà hàng nếu có -->
      <h1 class="page-title">
        🍽️ {{ currentRestaurant ? `Đặt bàn - ${currentRestaurant.TenNhaHang}` : 'Danh sách bàn' }}
      </h1>
      
      <!-- Nút quay lại nếu đang xem theo nhà hàng -->
      <div v-if="restaurantId" class="back-link">
        <button @click="goBack" class="back-btn">← Quay lại danh sách nhà hàng</button>
      </div>
      
      <!-- Filter khu vực -->
      <AreaFilter 
        :areas="filteredKhuVucs"
        :selected-area="selectedKhuVuc"
        @update:selected-area="handleSelectArea"
      />

      <!-- Loading -->
      <div v-if="loading" class="loading">
        <p>Đang tải danh sách bàn...</p>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="error">
        <p>{{ error }}</p>
        <button @click="fetchBans" class="retry-btn">Thử lại</button>
      </div>

      <!-- Empty -->
      <div v-else-if="filteredBans.length === 0" class="empty">
        <p>Không có bàn nào trong khu vực này</p>
      </div>

      <!-- Danh sách bàn -->
      <div v-else class="tables-grid">
        <Card 
          v-for="ban in filteredBans" 
          :key="ban.BanID"
          :ban="ban"
          @view-detail="handleViewDetail"
          @book-table="openBookingForm"
        />
      </div>
    </div>

    <!-- Form đặt bàn -->
    <BookingForm
      v-model="showBookingForm"
      :ban="selectedBan"
      @submit="handleBookingSubmit"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import Card from '@/components/Home/Card.vue'
import AreaFilter from '@/components/ui/AreaFilter.vue'
import BookingForm, { type BookingData } from '@/components/form/BookingForm.vue'
import banService, { type Ban, type KhuVuc } from '@/services/ban.service'
import khuVucService from '@/services/khuvuc.service'
import datBanService from '@/services/datban.service'
import nhaHangService, { type NhaHang } from '@/services/nhahang.service'
import authService from '@/services/auth.service'

const route = useRoute()
const router = useRouter()

const bans = ref<Ban[]>([])
const khuVucs = ref<KhuVuc[]>([])
const selectedKhuVuc = ref<KhuVuc | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)

// Nhà hàng hiện tại
const restaurantId = computed(() => route.query.restaurant ? Number(route.query.restaurant) : null)
const currentRestaurant = ref<NhaHang | null>(null)

// Booking form state
const showBookingForm = ref(false)
const selectedBan = ref<Ban | null>(null)

// Lọc khu vực theo nhà hàng
const filteredKhuVucs = computed(() => {
  if (!restaurantId.value) {
    return khuVucs.value
  }
  return khuVucs.value.filter(kv => kv.NhaHangID === restaurantId.value)
})

// Lọc bàn theo nhà hàng và khu vực
const filteredBans = computed(() => {
  let result = bans.value
  
  // Debug: log để kiểm tra
  console.log('restaurantId:', restaurantId.value)
  console.log('Total bans:', bans.value.length)
  console.log('Bans NhaHangID:', bans.value.map(b => b.NhaHangID))
  
  // Lọc theo nhà hàng nếu có
  if (restaurantId.value) {
    result = result.filter(ban => Number(ban.NhaHangID) === Number(restaurantId.value))
    console.log('Filtered bans count:', result.length)
  }
  
  // Lọc theo khu vực nếu có chọn
  if (selectedKhuVuc.value) {
    result = result.filter(ban => ban.KhuVucID === selectedKhuVuc.value?.KhuVucID)
  }
  
  return result
})

const fetchRestaurant = async () => {
  if (!restaurantId.value) {
    currentRestaurant.value = null
    return
  }
  
  try {
    const response = await nhaHangService.getById(restaurantId.value)
    currentRestaurant.value = response.data.data || response.data
  } catch (err) {
    console.error('Error fetching restaurant:', err)
  }
}

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

const goBack = () => {
  router.push('/')
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
    `Trạng thái: ${ban.TrangThai === 1 ? 'Trống' : ban.TrangThai === 2 ? 'Đã đặt' : 'Đang dùng'}`
  )
}

const openBookingForm = (ban: Ban) => {
  selectedBan.value = ban
  showBookingForm.value = true
}

const handleBookingSubmit = async (data: BookingData) => {
  try {
    // Lấy thông tin user đang đăng nhập
    const currentUser = authService.getUser()
    if (!currentUser) {
      alert('Vui lòng đăng nhập để đặt bàn!')
      return
    }

    // Chuẩn bị dữ liệu gửi API
    const requestData = {
      NguoiDungID: currentUser.NguoiDungID,
      NhaHangID: selectedBan.value?.NhaHangID || 1,
      BanID: data.BanID,
      ThoiGianDen: data.ThoiGianDen,
      SoNguoi: data.SoNguoi,
      GhiChu: data.GhiChu || '',
      items: data.items.map(item => ({
        MonAnID: item.MonAnID,
        SoLuong: item.SoLuong,
        DonGia: item.DonGia
      }))
    }

    // Gọi API đặt bàn
    const response = await datBanService.create(requestData)

    if (response.success) {
      let message = 
        `🎉 Đặt bàn thành công!\n\n` +
        `Mã đặt bàn: #${response.data.DatBanID}\n` +
        `Bàn: ${selectedBan.value?.SoBan}\n` +
        `Họ tên: ${data.HoTen}\n` +
        `SĐT: ${data.SDT}\n` +
        `Thời gian: ${data.ThoiGianDen}\n` +
        `Số người: ${data.SoNguoi}`
      
      if (data.items.length > 0) {
        message += `\n\n📋 Món đã đặt:\n`
        data.items.forEach(item => {
          message += `- ${item.TenMon} x${item.SoLuong}\n`
        })
        message += `\n💰 Tổng tiền: ${data.TongTien.toLocaleString('vi-VN')}đ`
      }
      
      alert(message)
      showBookingForm.value = false
      // Refresh danh sách bàn
      fetchBans()
    } else {
      alert(`Đặt bàn thất bại: ${response.message}`)
    }
  } catch (err: any) {
    console.error('Error booking table:', err)
    alert(`Đặt bàn thất bại: ${err.response?.data?.message || err.message}`)
  }
}

onMounted(() => {
  fetchRestaurant()
  fetchKhuVucs()
  fetchBans()
})

// Watch route changes để cập nhật khi đổi nhà hàng
watch(() => route.query.restaurant, () => {
  fetchRestaurant()
  selectedKhuVuc.value = null
})
</script>

<style scoped>
.order-page {
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
  margin-bottom: 20px;
}

.back-link {
  text-align: center;
  margin-bottom: 20px;
}

.back-btn {
  padding: 10px 20px;
  background: transparent;
  border: 2px solid #667eea;
  color: #667eea;
  border-radius: 8px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-btn:hover {
  background: #667eea;
  color: white;
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
