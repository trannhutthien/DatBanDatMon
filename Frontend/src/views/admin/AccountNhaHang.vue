<template>
  <div class="account-nhahang-page">
    <!-- Header -->
    <div class="page-header">
      <div class="header-content">
        <h1>🏪 Quản lý nhà hàng</h1>
        <p>Danh sách tất cả nhà hàng trong hệ thống</p>
      </div>
      <Button variant="gradient" prefix-icon="➕" @click="openAddModal">
        Thêm nhà hàng
      </Button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-icon total">📊</div>
        <div class="stat-info">
          <h3>{{ stats.total }}</h3>
          <p>Tổng nhà hàng</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon active">✅</div>
        <div class="stat-info">
          <h3>{{ stats.hoat_dong }}</h3>
          <p>Đang hoạt động</p>
        </div>
      </div>
      <div class="stat-card">
        <div class="stat-icon inactive">⛔</div>
        <div class="stat-info">
          <h3>{{ stats.ngung_hoat_dong }}</h3>
          <p>Ngừng hoạt động</p>
        </div>
      </div>
    </div>

    <!-- Search & Filter -->
    <div class="search-section">
      <div class="search-box">
        <input
          v-model="searchQuery"
          type="text"
          placeholder="🔍 Tìm kiếm theo tên, địa chỉ, SĐT..."
          @input="debouncedSearch"
        />
      </div>
    </div>

    <!-- Table -->
    <div class="table-container">
      <table class="user-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Tên nhà hàng</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="nhaHang in nhaHangs" :key="nhaHang.NhaHangID">
            <td>#{{ nhaHang.NhaHangID }}</td>
            <td>
              <div class="nhahang-name">{{ nhaHang.TenNhaHang }}</div>
            </td>
            <td>{{ nhaHang.DiaChi || '---' }}</td>
            <td>{{ nhaHang.SDT || '---' }}</td>
            <td>
              <div class="action-buttons">
                <button class="action-btn view-btn" @click="viewNhaHang(nhaHang)" title="Xem chi tiết">
                  👁️
                </button>
                <button class="action-btn edit-btn" @click="editNhaHang(nhaHang)" title="Chỉnh sửa">
                  ✏️
                </button>
                <button class="action-btn delete-btn" @click="deleteNhaHang(nhaHang)" title="Xóa">
                  🗑️
                </button>
              </div>
            </td>
          </tr>
          <tr v-if="nhaHangs.length === 0">
            <td colspan="5" class="empty-row">Không có dữ liệu</td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div v-if="totalPages > 1" class="pagination">
      <button :disabled="currentPage <= 1" @click="goToPage(currentPage - 1)">‹ Trước</button>
      <span>Trang {{ currentPage }} / {{ totalPages }}</span>
      <button :disabled="currentPage >= totalPages" @click="goToPage(currentPage + 1)">Sau ›</button>
    </div>

    <!-- Edit/Add Modal -->
    <UpdateNhaHang
      v-model="showModal"
      :nha-hang="editingNhaHang"
      :is-add-mode="isAddMode"
      @saved="handleSaved"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import nhaHangService, { type NhaHang, type NhaHangStats } from '@/services/nhahang.service'
import Button from '@/components/ui/Button.vue'
import UpdateNhaHang from '@/components/form/UpdateNhaHang.vue'

const nhaHangs = ref<NhaHang[]>([])
const stats = ref<NhaHangStats>({ total: 0, hoat_dong: 0, ngung_hoat_dong: 0 })
const loading = ref(false)
const searchQuery = ref('')
const currentPage = ref(1)
const totalPages = ref(1)

const showModal = ref(false)
const editingNhaHang = ref<NhaHang | null>(null)
const isAddMode = ref(false)

let searchTimeout: ReturnType<typeof setTimeout>
const debouncedSearch = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    currentPage.value = 1
    fetchNhaHangs()
  }, 300)
}

const fetchNhaHangs = async () => {
  loading.value = true
  try {
    const response = await nhaHangService.getAll({
      search: searchQuery.value || undefined,
      page: currentPage.value,
      per_page: 10
    })
    nhaHangs.value = response.data.data
    totalPages.value = response.data.last_page
  } catch (error) {
    console.error('Error fetching nha hangs:', error)
  } finally {
    loading.value = false
  }
}

const fetchStats = async () => {
  try {
    const response = await nhaHangService.getStats()
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

const goToPage = (page: number) => {
  currentPage.value = page
  fetchNhaHangs()
}

const openAddModal = () => {
  editingNhaHang.value = null
  isAddMode.value = true
  showModal.value = true
}

const viewNhaHang = (nhaHang: NhaHang) => {
  alert(`📋 Chi tiết nhà hàng:\n\n🏪 Tên: ${nhaHang.TenNhaHang}\n📍 Địa chỉ: ${nhaHang.DiaChi || 'Chưa có'}\n📞 SĐT: ${nhaHang.SDT || 'Chưa có'}\n📧 Email: ${nhaHang.Email || 'Chưa có'}\n📝 Mô tả: ${nhaHang.MoTa || 'Chưa có'}`)
}

const editNhaHang = (nhaHang: NhaHang) => {
  editingNhaHang.value = nhaHang
  isAddMode.value = false
  showModal.value = true
}

const deleteNhaHang = async (nhaHang: NhaHang) => {
  if (!confirm(`Bạn có chắc muốn xóa nhà hàng "${nhaHang.TenNhaHang}"?`)) return
  try {
    await nhaHangService.delete(nhaHang.NhaHangID)
    alert('Xóa thành công!')
    fetchNhaHangs()
    fetchStats()
  } catch (error) {
    console.error('Error deleting nha hang:', error)
    alert('Xóa thất bại!')
  }
}

const handleSaved = () => {
  fetchNhaHangs()
  fetchStats()
}

onMounted(() => {
  fetchNhaHangs()
  fetchStats()
})
</script>

<style scoped>
.account-nhahang-page {
  max-width: 1400px;
  margin: 0 auto;
  padding: 30px 20px;
}

.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
  flex-wrap: wrap;
  gap: 16px;
}

.header-content h1 {
  font-size: 2rem;
  color: #1f2937;
  margin-bottom: 8px;
}

.header-content p {
  color: #6b7280;
}

/* Stats Grid */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 20px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.stat-icon.total { background: #e0e7ff; }
.stat-icon.active { background: #d1fae5; }
.stat-icon.inactive { background: #fee2e2; }

.stat-info h3 {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
}

.stat-info p {
  color: #6b7280;
  font-size: 0.875rem;
}

/* Search */
.search-section {
  margin-bottom: 24px;
}

.search-box input {
  width: 100%;
  max-width: 400px;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
}

.search-box input:focus {
  outline: none;
  border-color: #f97316;
}

/* Table */
.table-container {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th,
.user-table td {
  padding: 16px;
  text-align: left;
  border-bottom: 1px solid #e5e7eb;
}

.user-table th {
  background: #f9fafb;
  font-weight: 600;
  color: #374151;
}

.nhahang-name {
  font-weight: 600;
  color: #1f2937;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.view-btn { background: #dbeafe; }
.view-btn:hover { background: #bfdbfe; }
.edit-btn { background: #fef3c7; }
.edit-btn:hover { background: #fde68a; }
.delete-btn { background: #fee2e2; }
.delete-btn:hover { background: #fecaca; }

.empty-row {
  text-align: center;
  color: #6b7280;
  padding: 40px !important;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 16px;
  margin-top: 24px;
}

.pagination button {
  padding: 8px 16px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  cursor: pointer;
}

.pagination button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.pagination button:not(:disabled):hover {
  background: #f3f4f6;
}
</style>

