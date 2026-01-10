<template>
  <div class="dishes-page">
    <div class="container">
      <!-- Hiển thị tên nhà hàng nếu có -->
      <h1 class="page-title">
        🍜 {{ currentRestaurant ? `Thực đơn - ${currentRestaurant.TenNhaHang}` : 'Danh sách món ăn' }}
      </h1>
      
      <!-- Nút quay lại và đặt món -->
      <div class="action-bar">
        <button v-if="restaurantId" @click="goBack" class="back-btn">
          ← Quay lại
        </button>
        <button @click="openTakeawayForm" class="takeaway-btn">
          🥡 Đặt món mang đi
        </button>
      </div>
      
      <!-- Filter danh mục -->
      <div class="category-filter">
        <button 
          class="filter-btn"
          :class="{ active: selectedCategory === null }"
          @click="selectedCategory = null"
        >
          Tất cả
        </button>
        <button 
          v-for="cat in categories" 
          :key="cat.DanhMucID"
          class="filter-btn"
          :class="{ active: selectedCategory === cat.DanhMucID }"
          @click="selectedCategory = cat.DanhMucID"
        >
          {{ cat.TenDanhMuc }}
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading">
        <p>Đang tải danh sách món ăn...</p>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="error">
        <p>{{ error }}</p>
        <button @click="fetchDishes" class="retry-btn">Thử lại</button>
      </div>

      <!-- Empty -->
      <div v-else-if="filteredDishes.length === 0" class="empty">
        <p>Không có món ăn nào</p>
      </div>

      <!-- Danh sách món ăn -->
      <div v-else class="dishes-grid">
        <DishCard 
          v-for="dish in filteredDishes" 
          :key="dish.MonAnID"
          :dish="dish"
          @order="handleOrder"
        />
      </div>
    </div>

    <!-- Form đặt món mang đi -->
    <TakeawayForm
      v-model="showTakeawayForm"
      :restaurant="currentRestaurant"
      :dishes="restaurantDishes"
      @submit="handleTakeawaySubmit"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import DishCard from '@/components/Home/DishCard.vue'
import TakeawayForm, { type TakeawayData } from '@/components/form/TakeawayForm.vue'
import monAnService, { type MonAn } from '@/services/monan.service'
import danhMucMonService from '@/services/danhmucmon.service'
import nhaHangService, { type NhaHang } from '@/services/nhahang.service'
import datMonMangVeService from '@/services/datmonmangve.service'
import authService from '@/services/auth.service'

interface DanhMuc {
  DanhMucID: number
  TenDanhMuc: string
}

const route = useRoute()
const router = useRouter()

const dishes = ref<MonAn[]>([])
const categories = ref<DanhMuc[]>([])
const selectedCategory = ref<number | null>(null)
const loading = ref(true)
const error = ref<string | null>(null)

// Nhà hàng hiện tại
const restaurantId = computed(() => route.query.restaurant ? Number(route.query.restaurant) : null)
const currentRestaurant = ref<NhaHang | null>(null)

// Takeaway form state
const showTakeawayForm = ref(false)

// Món ăn thuộc nhà hàng (dùng cho form)
const restaurantDishes = computed(() => {
  if (!restaurantId.value) {
    return dishes.value
  }
  return dishes.value.filter(dish => Number(dish.NhaHangID) === Number(restaurantId.value))
})

// Lọc món ăn theo nhà hàng và danh mục
const filteredDishes = computed(() => {
  let result = dishes.value
  
  // Lọc theo nhà hàng nếu có
  if (restaurantId.value) {
    result = result.filter(dish => Number(dish.NhaHangID) === Number(restaurantId.value))
  }
  
  // Lọc theo danh mục nếu có chọn
  if (selectedCategory.value) {
    result = result.filter(dish => dish.DanhMucID === selectedCategory.value)
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

const fetchCategories = async () => {
  try {
    const response = await danhMucMonService.getAll()
    categories.value = response.data
  } catch (err) {
    console.error('Error fetching categories:', err)
  }
}

const fetchDishes = async () => {
  loading.value = true
  error.value = null
  
  try {
    const response = await monAnService.getAll({ per_page: 100 })
    dishes.value = response.data.items || response.data as any
  } catch (err: any) {
    console.error('Error fetching dishes:', err)
    error.value = 'Không thể tải danh sách món ăn. Vui lòng thử lại.'
  } finally {
    loading.value = false
  }
}

const goBack = () => {
  router.push('/')
}

const openTakeawayForm = () => {
  showTakeawayForm.value = true
}

const handleOrder = (dish: MonAn) => {
  // Mở form đặt món khi click vào nút đặt món trên card
  showTakeawayForm.value = true
}

const handleTakeawaySubmit = async (data: TakeawayData) => {
  try {
    // Lấy thông tin user đang đăng nhập (nếu có)
    const currentUser = authService.getUser()

    // Chuẩn bị dữ liệu gửi API
    const requestData = {
      NguoiDungID: currentUser?.NguoiDungID,
      NhaHangID: data.NhaHangID || currentRestaurant.value?.NhaHangID || 1,
      HoTen: data.HoTen,
      SDT: data.SDT,
      DiaChi: data.DiaChi,
      ThoiGianLay: data.ThoiGianLay,
      GhiChu: data.GhiChu || '',
      items: data.items.map(item => ({
        MonAnID: item.MonAnID,
        SoLuong: item.SoLuong,
        DonGia: item.DonGia
      }))
    }

    // Gọi API đặt món
    const response = await datMonMangVeService.create(requestData)

    if (response.success) {
      let message = 
        `🎉 Đặt món thành công!\n\n` +
        `Mã đơn: #${response.data.DatMonID}\n` +
        `Họ tên: ${data.HoTen}\n` +
        `SĐT: ${data.SDT}\n` +
        `Địa chỉ: ${data.DiaChi}\n` +
        `Thời gian lấy: ${data.ThoiGianLay}\n`
      
      if (data.items.length > 0) {
        message += `\n📋 Món đã đặt:\n`
        data.items.forEach(item => {
          message += `- ${item.TenMon} x${item.SoLuong}\n`
        })
        message += `\n💰 Tổng tiền: ${data.TongTien.toLocaleString('vi-VN')}đ`
      }
      
      if (data.GhiChu) {
        message += `\n\n📝 Ghi chú: ${data.GhiChu}`
      }
      
      alert(message)
      showTakeawayForm.value = false
    } else {
      alert(`Đặt món thất bại: ${response.message}`)
    }
  } catch (err: any) {
    console.error('Error creating takeaway order:', err)
    alert(`Đặt món thất bại: ${err.response?.data?.message || err.message}`)
  }
}

onMounted(() => {
  fetchRestaurant()
  fetchCategories()
  fetchDishes()
})

// Watch route changes để cập nhật khi đổi nhà hàng
watch(() => route.query.restaurant, () => {
  fetchRestaurant()
  selectedCategory.value = null
})
</script>

<style scoped>
.dishes-page {
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

.action-bar {
  display: flex;
  justify-content: center;
  gap: 15px;
  margin-bottom: 25px;
}

.back-btn {
  padding: 12px 24px;
  background: transparent;
  border: 2px solid #667eea;
  color: #667eea;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.back-btn:hover {
  background: #667eea;
  color: white;
}

.takeaway-btn {
  padding: 12px 24px;
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  border: none;
  color: white;
  border-radius: 10px;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(229, 57, 53, 0.3);
}

.takeaway-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(229, 57, 53, 0.4);
}

.category-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: center;
  margin-bottom: 30px;
}

.filter-btn {
  padding: 10px 20px;
  border: 2px solid #e2e8f0;
  border-radius: 25px;
  background: white;
  color: #64748b;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.filter-btn:hover {
  border-color: #e53935;
  color: #e53935;
}

.filter-btn.active {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  border-color: transparent;
  color: white;
}

.dishes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
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
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}

.retry-btn:hover {
  transform: translateY(-2px);
}

@media (max-width: 768px) {
  .dishes-grid {
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  }
  
  .filter-btn {
    padding: 8px 16px;
    font-size: 0.85rem;
  }
}
</style>
