<template>
  <div class="history-page">
    <div class="container">
      <h1 class="page-title">📋 Lịch sử đặt hàng</h1>

      <!-- Tabs chuyển đổi -->
      <div class="tabs">
        <button 
          class="tab-btn" 
          :class="{ active: activeTab === 'datban' }"
          @click="activeTab = 'datban'"
        >
          🍽️ Đặt bàn ({{ datBanList.length }})
        </button>
        <button 
          class="tab-btn" 
          :class="{ active: activeTab === 'datmon' }"
          @click="activeTab = 'datmon'"
        >
          🥡 Đặt món mang đi ({{ datMonList.length }})
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="loading">
        <p>Đang tải lịch sử...</p>
      </div>

      <!-- Chưa đăng nhập -->
      <div v-else-if="!currentUser" class="empty">
        <p>Vui lòng đăng nhập để xem lịch sử đặt hàng</p>
      </div>

      <!-- Tab Đặt bàn -->
      <div v-else-if="activeTab === 'datban'" class="tab-content">
        <div v-if="datBanList.length === 0" class="empty">
          <p>Chưa có lịch sử đặt bàn</p>
        </div>
        <div v-else class="order-list">
          <div v-for="order in datBanList" :key="order.DatBanID" class="order-card">
            <div class="order-header">
              <span class="order-id">#{{ order.DatBanID }}</span>
              <span class="order-status" :class="getStatusClass(order.TrangThai)">
                {{ getStatusText(order.TrangThai) }}
              </span>
            </div>
            <div class="order-body">
              <div class="order-info">
                <p><strong>🏪 Nhà hàng:</strong> {{ order.nha_hang?.TenNhaHang || 'N/A' }}</p>
                <p><strong>🪑 Bàn:</strong> {{ order.ban?.SoBan || 'N/A' }}</p>
                <p><strong>📅 Thời gian:</strong> {{ formatDate(order.ThoiGianDen) }}</p>
                <p><strong>👥 Số người:</strong> {{ order.SoNguoi }}</p>
              </div>
              <div v-if="order.chi_tiet_mon && order.chi_tiet_mon.length > 0" class="order-items">
                <p><strong>📋 Món đã đặt:</strong></p>
                <ul>
                  <li v-for="item in order.chi_tiet_mon" :key="item.ID">
                    {{ item.mon_an?.TenMon }} x{{ item.SoLuong }} - {{ formatPrice(item.DonGia * item.SoLuong) }}
                  </li>
                </ul>
              </div>
            </div>
            <div class="order-footer">
              <span class="order-date">{{ formatDate(order.TaoLuc) }}</span>
              <button 
                v-if="order.TrangThai === 1" 
                class="cancel-btn"
                @click="cancelDatBan(order.DatBanID)"
              >
                Hủy đặt bàn
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab Đặt món mang đi -->
      <div v-else-if="activeTab === 'datmon'" class="tab-content">
        <div v-if="datMonList.length === 0" class="empty">
          <p>Chưa có lịch sử đặt món mang đi</p>
        </div>
        <div v-else class="order-list">
          <div v-for="order in datMonList" :key="order.DatMonID" class="order-card">
            <div class="order-header">
              <span class="order-id">#{{ order.DatMonID }}</span>
              <span class="order-status" :class="getTakeawayStatusClass(order.TrangThai)">
                {{ getTakeawayStatusText(order.TrangThai) }}
              </span>
            </div>
            <div class="order-body">
              <div class="order-info">
                <p><strong>🏪 Nhà hàng:</strong> {{ order.nha_hang?.TenNhaHang || 'N/A' }}</p>
                <p><strong>📍 Địa chỉ:</strong> {{ order.DiaChi }}</p>
                <p><strong>📅 Thời gian lấy:</strong> {{ formatDate(order.ThoiGianLay) }}</p>
                <p><strong>📞 SĐT:</strong> {{ order.SDT }}</p>
              </div>
              <div v-if="order.chi_tiet && order.chi_tiet.length > 0" class="order-items">
                <p><strong>📋 Món đã đặt:</strong></p>
                <ul>
                  <li v-for="item in order.chi_tiet" :key="item.ChiTietID">
                    {{ item.mon_an?.TenMon }} x{{ item.SoLuong }} - {{ formatPrice(item.DonGia * item.SoLuong) }}
                  </li>
                </ul>
                <p class="total"><strong>💰 Tổng tiền:</strong> {{ formatPrice(order.TongTien) }}</p>
              </div>
            </div>
            <div class="order-footer">
              <span class="order-date">{{ formatDate(order.TaoLuc) }}</span>
              <button 
                v-if="order.TrangThai <= 2" 
                class="cancel-btn"
                @click="cancelDatMon(order.DatMonID)"
              >
                Hủy đơn
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import authService from '@/services/auth.service'
import datBanService from '@/services/datban.service'
import datMonMangVeService from '@/services/datmonmangve.service'

const activeTab = ref<'datban' | 'datmon'>('datban')
const loading = ref(true)
const currentUser = ref<any>(null)
const datBanList = ref<any[]>([])
const datMonList = ref<any[]>([])

// Trạng thái đặt bàn
const getStatusText = (status: number) => {
  const texts: Record<number, string> = {
    1: 'Chờ xác nhận',
    2: 'Đã xác nhận',
    3: 'Khách đã đến',
    4: 'Hoàn thành',
    5: 'Đã hủy'
  }
  return texts[status] || 'Không xác định'
}

const getStatusClass = (status: number) => {
  const classes: Record<number, string> = {
    1: 'status-pending',
    2: 'status-confirmed',
    3: 'status-arrived',
    4: 'status-completed',
    5: 'status-cancelled'
  }
  return classes[status] || ''
}

// Trạng thái đặt món mang đi
const getTakeawayStatusText = (status: number) => {
  const texts: Record<number, string> = {
    1: 'Chờ xác nhận',
    2: 'Đã xác nhận',
    3: 'Đang chuẩn bị',
    4: 'Sẵn sàng',
    5: 'Đã giao',
    6: 'Đã hủy'
  }
  return texts[status] || 'Không xác định'
}

const getTakeawayStatusClass = (status: number) => {
  const classes: Record<number, string> = {
    1: 'status-pending',
    2: 'status-confirmed',
    3: 'status-preparing',
    4: 'status-ready',
    5: 'status-completed',
    6: 'status-cancelled'
  }
  return classes[status] || ''
}

const formatDate = (dateStr: string) => {
  if (!dateStr) return 'N/A'
  const date = new Date(dateStr)
  return date.toLocaleString('vi-VN')
}

const formatPrice = (price: number) => {
  return Number(price).toLocaleString('vi-VN') + 'đ'
}

const fetchDatBan = async () => {
  if (!currentUser.value) return
  try {
    const response = await datBanService.getByUser(currentUser.value.NguoiDungID)
    datBanList.value = response.data || []
  } catch (err) {
    console.error('Error fetching dat ban:', err)
  }
}

const fetchDatMon = async () => {
  if (!currentUser.value) return
  try {
    const response = await datMonMangVeService.getAll({
      nguoi_dung_id: currentUser.value.NguoiDungID
    })
    datMonList.value = response.data || []
  } catch (err) {
    console.error('Error fetching dat mon:', err)
  }
}

const cancelDatBan = async (id: number) => {
  if (!confirm('Bạn có chắc muốn hủy đặt bàn này?')) return
  try {
    await datBanService.cancel(id)
    alert('Hủy đặt bàn thành công!')
    fetchDatBan()
  } catch (err: any) {
    alert('Hủy thất bại: ' + (err.response?.data?.message || err.message))
  }
}

const cancelDatMon = async (id: number) => {
  if (!confirm('Bạn có chắc muốn hủy đơn này?')) return
  try {
    await datMonMangVeService.cancel(id)
    alert('Hủy đơn thành công!')
    fetchDatMon()
  } catch (err: any) {
    alert('Hủy thất bại: ' + (err.response?.data?.message || err.message))
  }
}

onMounted(async () => {
  currentUser.value = authService.getUser()
  if (currentUser.value) {
    await Promise.all([fetchDatBan(), fetchDatMon()])
  }
  loading.value = false
})
</script>

<style scoped>
.history-page {
  min-height: 100vh;
  background: #f8f9fa;
  padding: 40px 20px;
}

.container {
  max-width: 900px;
  margin: 0 auto;
}

.page-title {
  text-align: center;
  font-size: 2rem;
  color: #2c3e50;
  margin-bottom: 30px;
}

/* Tabs */
.tabs {
  display: flex;
  gap: 10px;
  justify-content: center;
  margin-bottom: 30px;
}

.tab-btn {
  padding: 12px 24px;
  border: 2px solid #e2e8f0;
  border-radius: 25px;
  background: white;
  color: #64748b;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.tab-btn:hover {
  border-color: #667eea;
  color: #667eea;
}

.tab-btn.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-color: transparent;
  color: white;
}

/* Order List */
.order-list {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.order-card {
  background: white;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 20px;
  background: #f8f9fa;
  border-bottom: 1px solid #e5e7eb;
}

.order-id {
  font-weight: 700;
  color: #2c3e50;
  font-size: 1.1rem;
}

.order-status {
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.status-pending { background: #fef3c7; color: #d97706; }
.status-confirmed { background: #dbeafe; color: #2563eb; }
.status-arrived, .status-preparing { background: #e0e7ff; color: #4f46e5; }
.status-ready { background: #d1fae5; color: #059669; }
.status-completed { background: #d1fae5; color: #059669; }
.status-cancelled { background: #fee2e2; color: #dc2626; }

.order-body {
  padding: 20px;
}

.order-info p {
  margin: 8px 0;
  color: #4b5563;
  font-size: 0.95rem;
}

.order-items {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 1px dashed #e5e7eb;
}

.order-items ul {
  margin: 10px 0;
  padding-left: 20px;
}

.order-items li {
  color: #6b7280;
  margin: 5px 0;
}

.total {
  margin-top: 10px;
  font-size: 1.1rem;
  color: #e53935;
}

.order-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 12px 20px;
  background: #f8f9fa;
  border-top: 1px solid #e5e7eb;
}

.order-date {
  color: #9ca3af;
  font-size: 0.85rem;
}

.cancel-btn {
  padding: 8px 16px;
  background: #fee2e2;
  color: #dc2626;
  border: none;
  border-radius: 8px;
  font-size: 0.85rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.cancel-btn:hover {
  background: #fecaca;
}

/* States */
.loading, .empty {
  text-align: center;
  padding: 60px 20px;
  color: #7f8c8d;
}

@media (max-width: 640px) {
  .tabs {
    flex-direction: column;
  }
  
  .tab-btn {
    width: 100%;
  }
}
</style>
