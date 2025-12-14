<template>
  <div class="account-user-page">
    <div class="page-header">
      <div class="header-content">
        <h1>👥 Quản lý người dùng</h1>
        <p>Danh sách tất cả người dùng trong hệ thống</p>
      </div>
      <Button
        variant="gradient"
        prefix-icon="➕"
        @click="openAddUserModal"
      >
        Thêm người dùng
      </Button>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="stat-card total">
        <span class="stat-icon">👥</span>
        <div class="stat-info">
          <span class="stat-value">{{ stats.total }}</span>
          <span class="stat-label">Tổng người dùng</span>
        </div>
      </div>
      <div class="stat-card admin">
        <span class="stat-icon">👑</span>
        <div class="stat-info">
          <span class="stat-value">{{ stats.admin }}</span>
          <span class="stat-label">Admin</span>
        </div>
      </div>
      <div class="stat-card staff">
        <span class="stat-icon">👔</span>
        <div class="stat-info">
          <span class="stat-value">{{ stats.nhan_vien }}</span>
          <span class="stat-label">Nhân viên</span>
        </div>
      </div>
      <div class="stat-card customer">
        <span class="stat-icon">🧑</span>
        <div class="stat-info">
          <span class="stat-value">{{ stats.khach_hang }}</span>
          <span class="stat-label">Khách hàng</span>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="filters-bar">
      <div class="search-box">
        <span class="search-icon">🔍</span>
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Tìm kiếm theo tên, email, SĐT..."
          @input="debouncedSearch"
        />
      </div>
      <div class="filter-buttons">
        <button 
          v-for="filter in filters" 
          :key="filter.value"
          :class="['filter-btn', { active: selectedFilter === filter.value }]"
          @click="setFilter(filter.value)"
        >
          {{ filter.label }}
        </button>
      </div>
    </div>

    <!-- User Table -->
    <div class="table-container">
      <table class="user-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Người dùng</th>
            <th>Email</th>
            <th>SĐT</th>
            <th>Loại</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td colspan="7" class="loading-cell">
              <div class="loading-spinner">⏳</div>
              <span>Đang tải...</span>
            </td>
          </tr>
          <tr v-else-if="users.length === 0">
            <td colspan="7" class="empty-cell">
              <div class="empty-icon">📭</div>
              <span>Không tìm thấy người dùng nào</span>
            </td>
          </tr>
          <tr v-for="user in users" :key="user.NguoiDungID" v-else>
            <td class="id-cell">{{ user.NguoiDungID }}</td>
            <td class="user-cell">
              <div class="user-info">
                <div class="user-avatar" :style="{ background: getAvatarColor(user.HoTen) }">
                  {{ getInitial(user.HoTen) }}
                </div>
                <span class="user-name">{{ user.HoTen }}</span>
              </div>
            </td>
            <td>{{ user.Email }}</td>
            <td>{{ user.SDT || '—' }}</td>
            <td>
              <span class="role-badge" :style="{ background: getRoleColor(user.LoaiNguoiDung) }">
                {{ getRoleLabel(user.LoaiNguoiDung) }}
              </span>
            </td>
            <td>{{ formatDate(user.TaoLuc) }}</td>
            <td class="actions-cell">
              <button class="action-btn edit-btn" @click="editUser(user)" title="Chỉnh sửa">
                ✏️
              </button>
              <button class="action-btn delete-btn" @click="deleteUser(user)" title="Xóa">
                🗑️
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pagination" v-if="totalPages > 1">
      <button 
        class="page-btn" 
        :disabled="currentPage === 1"
        @click="goToPage(currentPage - 1)"
      >
        ← Trước
      </button>
      <span class="page-info">Trang {{ currentPage }} / {{ totalPages }}</span>
      <button 
        class="page-btn" 
        :disabled="currentPage === totalPages"
        @click="goToPage(currentPage + 1)"
      >
        Sau →
      </button>
    </div>

    <!-- Edit/Add Modal Component -->
    <UpdateUser
      v-model="showEditModal"
      :user="editingUser"
      :is-add-mode="isAddMode"
      @saved="handleUserUpdated"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import nguoiDungService, { type NguoiDung } from '@/services/nguoidung.service'
import UpdateUser from '@/components/form/UpdateUser.vue'
import Button from '@/components/ui/Button.vue'

// State
const users = ref<NguoiDung[]>([])
const loading = ref(false)
const searchQuery = ref('')
const selectedFilter = ref<number | null>(null)
const currentPage = ref(1)
const totalPages = ref(1)
const perPage = 10

const stats = ref({
  total: 0,
  admin: 0,
  nhan_vien: 0,
  khach_hang: 0
})

// Edit/Add Modal State
const showEditModal = ref(false)
const editingUser = ref<NguoiDung | null>(null)
const isAddMode = ref(false)

const filters = [
  { label: 'Tất cả', value: null },
  { label: 'Admin', value: 1 },
  { label: 'Nhân viên', value: 2 },
  { label: 'Khách hàng', value: 3 }
]

// Methods
const fetchUsers = async () => {
  loading.value = true
  try {
    const response = await nguoiDungService.getAll({
      search: searchQuery.value || undefined,
      loai: selectedFilter.value || undefined,
      per_page: perPage,
      page: currentPage.value
    })
    users.value = response.data.data
    totalPages.value = response.data.last_page
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    loading.value = false
  }
}

const fetchStats = async () => {
  try {
    const response = await nguoiDungService.getStats()
    stats.value = response.data
  } catch (error) {
    console.error('Error fetching stats:', error)
  }
}

let debounceTimer: ReturnType<typeof setTimeout>
const debouncedSearch = () => {
  clearTimeout(debounceTimer)
  debounceTimer = setTimeout(() => {
    currentPage.value = 1
    fetchUsers()
  }, 300)
}

const setFilter = (value: number | null) => {
  selectedFilter.value = value
  currentPage.value = 1
  fetchUsers()
}

const goToPage = (page: number) => {
  currentPage.value = page
  fetchUsers()
}

const getInitial = (name: string): string => {
  return (name || 'U').charAt(0).toUpperCase()
}

const getAvatarColor = (name: string): string => {
  const colors = ['#e53935', '#8e24aa', '#3949ab', '#1e88e5', '#00acc1', '#43a047', '#fb8c00']
  const index = (name || '').charCodeAt(0) % colors.length
  return colors[index]
}

const getRoleLabel = (loai: number): string => {
  return nguoiDungService.getLoaiNguoiDungLabel(loai)
}

const getRoleColor = (loai: number): string => {
  return nguoiDungService.getLoaiNguoiDungColor(loai)
}

const formatDate = (date: string | undefined): string => {
  if (!date) return '—'
  return new Date(date).toLocaleDateString('vi-VN')
}

const openAddUserModal = () => {
  editingUser.value = null
  isAddMode.value = true
  showEditModal.value = true
}

const editUser = (user: NguoiDung) => {
  editingUser.value = user
  isAddMode.value = false
  showEditModal.value = true
}

const handleUserUpdated = () => {
  fetchUsers()
  fetchStats()
}

const deleteUser = async (user: NguoiDung) => {
  if (!confirm(`Bạn có chắc muốn xóa người dùng "${user.HoTen}"?`)) return
  try {
    await nguoiDungService.delete(user.NguoiDungID)
    alert('Xóa thành công!')
    fetchUsers()
    fetchStats()
  } catch (error) {
    alert('Xóa thất bại!')
  }
}

onMounted(() => {
  fetchUsers()
  fetchStats()
})
</script>

<style scoped>
.account-user-page {
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
  border-radius: 16px;
  background: white;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.stat-card.total { border-left: 4px solid #6b7280; }
.stat-card.admin { border-left: 4px solid #7c3aed; }
.stat-card.staff { border-left: 4px solid #2563eb; }
.stat-card.customer { border-left: 4px solid #16a34a; }

.stat-icon {
  font-size: 2.5rem;
}

.stat-info {
  display: flex;
  flex-direction: column;
}

.stat-value {
  font-size: 1.8rem;
  font-weight: 700;
  color: #1f2937;
}

.stat-label {
  font-size: 0.9rem;
  color: #6b7280;
}

/* Filters */
.filters-bar {
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  flex-wrap: wrap;
}

.search-box {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  flex: 1;
  min-width: 250px;
}

.search-box input {
  border: none;
  outline: none;
  font-size: 1rem;
  width: 100%;
}

.filter-buttons {
  display: flex;
  gap: 8px;
}

.filter-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 10px;
  background: white;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.filter-btn:hover {
  background: #f3f4f6;
}

.filter-btn.active {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
}

/* Table */
.table-container {
  background: white;
  border-radius: 16px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  overflow: hidden;
}

.user-table {
  width: 100%;
  border-collapse: collapse;
}

.user-table th {
  background: #f8fafc;
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: #374151;
  border-bottom: 2px solid #e5e7eb;
}

.user-table td {
  padding: 16px;
  border-bottom: 1px solid #f3f4f6;
  color: #4b5563;
}

.user-table tr:hover {
  background: #fafafa;
}

.id-cell {
  font-weight: 600;
  color: #6b7280;
}

.user-cell .user-info {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: 700;
  font-size: 1rem;
}

.user-name {
  font-weight: 500;
  color: #1f2937;
}

.role-badge {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  color: white;
}

.actions-cell {
  display: flex;
  gap: 8px;
}

.action-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  transition: all 0.2s ease;
}

.view-btn { background: #e0f2fe; }
.view-btn:hover { background: #bae6fd; transform: scale(1.1); }

.edit-btn { background: #fef3c7; }
.edit-btn:hover { background: #fde68a; transform: scale(1.1); }

.delete-btn { background: #fee2e2; }
.delete-btn:hover { background: #fecaca; transform: scale(1.1); }

.loading-cell, .empty-cell {
  text-align: center;
  padding: 60px 20px !important;
  color: #6b7280;
}

.loading-spinner, .empty-icon {
  font-size: 3rem;
  margin-bottom: 10px;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 20px;
  margin-top: 30px;
}

.page-btn {
  padding: 10px 20px;
  border: none;
  border-radius: 10px;
  background: white;
  color: #374151;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.page-btn:hover:not(:disabled) {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-weight: 500;
  color: #374151;
}

/* Responsive */
@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
  }

  .filters-bar {
    flex-direction: column;
  }

  .filter-buttons {
    flex-wrap: wrap;
  }

  .user-table th:nth-child(4),
  .user-table td:nth-child(4),
  .user-table th:nth-child(6),
  .user-table td:nth-child(6) {
    display: none;
  }
}
</style>

