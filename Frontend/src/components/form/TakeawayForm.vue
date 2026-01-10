<template>
  <div v-if="modelValue" class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>🥡 Đặt món mang đi</h2>
        <button class="close-btn" @click="closeModal">×</button>
      </div>

      <!-- Thông tin nhà hàng -->
      <div v-if="restaurant" class="restaurant-info-section">
        <div class="restaurant-preview">
          <span class="restaurant-icon">🏪</span>
          <div class="restaurant-details">
            <h3>{{ restaurant.TenNhaHang }}</h3>
            <p>{{ restaurant.DiaChi }}</p>
          </div>
        </div>
      </div>

      <form @submit.prevent="handleSubmit" class="takeaway-form">
        <div class="form-layout">
          <!-- Cột trái: Thông tin khách hàng -->
          <div class="form-column">
            <div class="form-section">
              <h4 class="section-title">👤 Thông tin khách hàng</h4>
              
              <div class="form-group">
                <label>Họ tên <span class="required">*</span></label>
                <input
                  v-model="form.HoTen"
                  type="text"
                  placeholder="Nhập họ tên"
                  required
                />
              </div>

              <div class="form-group">
                <label>Số điện thoại <span class="required">*</span></label>
                <input
                  v-model="form.SDT"
                  type="tel"
                  placeholder="Nhập số điện thoại"
                  required
                />
              </div>

              <div class="form-group">
                <label>Địa chỉ giao hàng <span class="required">*</span></label>
                <input
                  v-model="form.DiaChi"
                  type="text"
                  placeholder="Nhập địa chỉ giao hàng"
                  required
                />
              </div>

              <div class="form-group">
                <label>Thời gian lấy <span class="required">*</span></label>
                <div class="form-row">
                  <input
                    v-model="form.NgayLay"
                    type="date"
                    :min="minDate"
                    required
                  />
                  <input
                    v-model="form.GioLay"
                    type="time"
                    required
                  />
                </div>
              </div>

              <div class="form-group">
                <label>Ghi chú</label>
                <textarea
                  v-model="form.GhiChu"
                  placeholder="Yêu cầu đặc biệt (nếu có)"
                  rows="3"
                ></textarea>
              </div>
            </div>

            <!-- Tổng tiền & Món đã chọn -->
            <div class="selected-section">
              <h4 class="section-title">🛒 Món đã chọn ({{ selectedDishes.length }})</h4>
              
              <div v-if="selectedDishes.length === 0" class="no-selected">
                Chưa chọn món nào
              </div>
              
              <div v-else class="selected-dishes">
                <div 
                  v-for="item in selectedDishes" 
                  :key="item.MonAnID" 
                  class="selected-item"
                >
                  <span class="item-name">{{ item.TenMon }} x{{ item.quantity }}</span>
                  <span class="item-price">{{ formatPrice(item.DonGia * item.quantity) }}</span>
                </div>
              </div>
              
              <div class="total-section">
                <div class="total-row">
                  <span>Tổng tiền:</span>
                  <span class="total-amount">{{ formatPrice(totalAmount) }}</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Cột phải: Chọn món ăn -->
          <div class="form-column">
            <div class="form-section dishes-section">
              <h4 class="section-title">🍽️ Chọn món</h4>
              
              <!-- Filter danh mục -->
              <div class="category-filter">
                <button
                  type="button"
                  @click="selectedCategoryId = null"
                  :class="['filter-btn', { active: selectedCategoryId === null }]"
                >
                  Tất cả
                </button>
                <button
                  v-for="cat in categories"
                  :key="cat.DanhMucID"
                  type="button"
                  @click="selectedCategoryId = cat.DanhMucID"
                  :class="['filter-btn', { active: selectedCategoryId === cat.DanhMucID }]"
                >
                  {{ cat.TenDanhMuc }}
                </button>
              </div>

              <div v-if="loadingDishes" class="loading-dishes">
                Đang tải danh sách món...
              </div>

              <div v-else class="dishes-list">
                <div 
                  v-for="dish in filteredDishes" 
                  :key="dish.MonAnID" 
                  class="dish-item"
                >
                  <div class="dish-info">
                    <span class="dish-name">{{ dish.TenMon }}</span>
                    <span class="dish-price">{{ formatPrice(dish.DonGia) }}</span>
                  </div>
                  <div class="dish-quantity">
                    <button 
                      type="button" 
                      class="qty-btn minus"
                      @click="decreaseQty(dish.MonAnID)"
                      :disabled="!getQuantity(dish.MonAnID)"
                    >
                      −
                    </button>
                    <span class="qty-value">{{ getQuantity(dish.MonAnID) }}</span>
                    <button 
                      type="button" 
                      class="qty-btn plus"
                      @click="increaseQty(dish.MonAnID)"
                    >
                      +
                    </button>
                  </div>
                </div>

                <div v-if="filteredDishes.length === 0" class="no-dishes">
                  Không có món ăn trong danh mục này
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="closeModal">
            Hủy
          </button>
          <button 
            type="submit" 
            class="btn-submit" 
            :disabled="submitting || selectedDishes.length === 0"
          >
            {{ submitting ? 'Đang xử lý...' : 'Xác nhận đặt món' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch, computed } from 'vue'
import { monAnService, type MonAn } from '@/services/monan.service'
import danhMucMonService, { type DanhMucMon } from '@/services/danhmucmon.service'
import authService from '@/services/auth.service'
import type { NhaHang } from '@/services/nhahang.service'

const props = defineProps<{
  modelValue: boolean
  restaurant: NhaHang | null
  dishes: MonAn[]
}>()

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  'submit': [data: TakeawayData]
}>()

export interface OrderItem {
  MonAnID: number
  TenMon: string
  DonGia: number
  SoLuong: number
}

export interface TakeawayData {
  NhaHangID: number
  HoTen: string
  SDT: string
  DiaChi: string
  ThoiGianLay: string
  GhiChu: string
  items: OrderItem[]
  TongTien: number
}

const submitting = ref(false)
const loadingDishes = ref(false)
const categories = ref<DanhMucMon[]>([])
const dishQuantities = ref<Record<number, number>>({})
const selectedCategoryId = ref<number | null>(null)

const form = ref({
  HoTen: '',
  SDT: '',
  DiaChi: '',
  NgayLay: '',
  GioLay: '',
  GhiChu: ''
})

// Lọc món theo danh mục và nhà hàng
const filteredDishes = computed(() => {
  let result = props.dishes
  
  if (selectedCategoryId.value) {
    result = result.filter(dish => dish.DanhMucID === selectedCategoryId.value)
  }
  
  return result
})

// Ngày tối thiểu là hôm nay
const minDate = computed(() => {
  const today = new Date()
  return today.toISOString().split('T')[0]
})

// Danh sách món đã chọn
const selectedDishes = computed(() => {
  return props.dishes
    .filter(dish => dishQuantities.value[dish.MonAnID] > 0)
    .map(dish => ({
      ...dish,
      quantity: dishQuantities.value[dish.MonAnID]
    }))
})

// Tổng tiền
const totalAmount = computed(() => {
  return selectedDishes.value.reduce((sum, item) => {
    return sum + (Number(item.DonGia) * item.quantity)
  }, 0)
})

// Format giá tiền
const formatPrice = (price: number) => {
  return Number(price).toLocaleString('vi-VN') + 'đ'
}

// Lấy số lượng món
const getQuantity = (dishId: number) => {
  return dishQuantities.value[dishId] || 0
}

// Tăng số lượng
const increaseQty = (dishId: number) => {
  if (!dishQuantities.value[dishId]) {
    dishQuantities.value[dishId] = 0
  }
  dishQuantities.value[dishId]++
}

// Giảm số lượng
const decreaseQty = (dishId: number) => {
  if (dishQuantities.value[dishId] > 0) {
    dishQuantities.value[dishId]--
  }
}

// Lấy danh sách danh mục món
const fetchCategories = async () => {
  try {
    const response = await danhMucMonService.getAll()
    categories.value = response.data
  } catch (err) {
    console.error('Error fetching categories:', err)
  }
}

// Điền thông tin user đã đăng nhập
watch(() => props.modelValue, (isOpen) => {
  if (isOpen) {
    const user = authService.getUser()
    if (user) {
      form.value.HoTen = user.HoTen || ''
      form.value.SDT = user.SDT || ''
      form.value.DiaChi = user.DiaChi || ''
    } else {
      form.value.HoTen = ''
      form.value.SDT = ''
      form.value.DiaChi = ''
    }
    form.value.NgayLay = minDate.value
    form.value.GioLay = '12:00'
    form.value.GhiChu = ''
    dishQuantities.value = {}
    selectedCategoryId.value = null
    fetchCategories()
  }
})

const closeModal = () => {
  emit('update:modelValue', false)
}

const handleSubmit = async () => {
  if (selectedDishes.value.length === 0) {
    alert('Vui lòng chọn ít nhất 1 món!')
    return
  }

  submitting.value = true
  try {
    const items: OrderItem[] = selectedDishes.value.map(dish => ({
      MonAnID: dish.MonAnID,
      TenMon: dish.TenMon,
      DonGia: Number(dish.DonGia),
      SoLuong: dish.quantity
    }))

    const takeawayData: TakeawayData = {
      NhaHangID: props.restaurant?.NhaHangID || 0,
      HoTen: form.value.HoTen,
      SDT: form.value.SDT,
      DiaChi: form.value.DiaChi,
      ThoiGianLay: `${form.value.NgayLay} ${form.value.GioLay}:00`,
      GhiChu: form.value.GhiChu,
      items: items,
      TongTien: totalAmount.value
    }
    
    emit('submit', takeawayData)
  } finally {
    submitting.value = false
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-content {
  background: white;
  border-radius: 24px;
  width: 100%;
  max-width: 900px;
  max-height: 95vh;
  overflow-y: auto;
  box-shadow: 0 25px 80px rgba(0, 0, 0, 0.35);
  animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
  from { opacity: 0; transform: translateY(-30px) scale(0.95); }
  to { opacity: 1; transform: translateY(0) scale(1); }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 30px;
  border-bottom: 1px solid #e5e7eb;
  position: sticky;
  top: 0;
  background: white;
  border-radius: 24px 24px 0 0;
  z-index: 10;
}

.modal-header h2 {
  font-size: 1.5rem;
  color: #1f2937;
  margin: 0;
}

.close-btn {
  width: 40px;
  height: 40px;
  border: none;
  background: #f3f4f6;
  border-radius: 50%;
  font-size: 1.6rem;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #e5e7eb;
  color: #1f2937;
}

/* Restaurant info */
.restaurant-info-section {
  padding: 20px 30px;
  background: linear-gradient(135deg, #e5393515 0%, #ff6f0015 100%);
  border-bottom: 1px solid #e5e7eb;
}

.restaurant-preview {
  display: flex;
  align-items: center;
  gap: 18px;
}

.restaurant-icon {
  font-size: 3rem;
}

.restaurant-details h3 {
  font-size: 1.4rem;
  color: #2c3e50;
  margin: 0 0 6px 0;
}

.restaurant-details p {
  color: #7f8c8d;
  margin: 0;
  font-size: 0.95rem;
}

/* Form */
.takeaway-form {
  padding: 30px;
}

.form-layout {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 30px;
}

.form-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.form-section {
  background: #fafafa;
  border-radius: 16px;
  padding: 24px;
}

.dishes-section {
  background: white;
  border: 2px solid #e5e7eb;
}

.section-title {
  font-size: 1.1rem;
  color: #374151;
  margin: 0 0 20px 0;
}

.form-group {
  margin-bottom: 18px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #374151;
  margin-bottom: 10px;
  font-size: 0.95rem;
}

.required {
  color: #e53935;
}

.form-group input,
.form-group textarea {
  width: 100%;
  padding: 14px 18px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.2s;
  box-sizing: border-box;
  font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #e53935;
  box-shadow: 0 0 0 4px rgba(229, 57, 53, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 90px;
}

/* Category filter */
.category-filter {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-bottom: 18px;
}

.filter-btn {
  padding: 10px 18px;
  background: #f3f4f6;
  border: 2px solid transparent;
  border-radius: 25px;
  cursor: pointer;
  transition: all 0.2s;
  font-weight: 500;
  color: #495057;
  font-family: inherit;
  font-size: 0.9rem;
}

.filter-btn:hover {
  background: #e9ecef;
}

.filter-btn.active {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
}

/* Dishes list */
.loading-dishes {
  text-align: center;
  padding: 30px;
  color: #7f8c8d;
}

.dishes-list {
  max-height: 350px;
  overflow-y: auto;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  background: white;
}

.dish-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 16px 18px;
  border-bottom: 1px solid #f3f4f6;
  transition: background 0.2s;
}

.dish-item:hover {
  background: #f8f9fa;
}

.dish-item:last-child {
  border-bottom: none;
}

.dish-info {
  flex: 1;
}

.dish-name {
  display: block;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 6px;
  font-size: 1rem;
}

.dish-price {
  font-size: 0.95rem;
  color: #e53935;
  font-weight: 700;
}

.dish-quantity {
  display: flex;
  align-items: center;
  gap: 10px;
}

.qty-btn {
  width: 36px;
  height: 36px;
  border: none;
  border-radius: 10px;
  font-size: 1.3rem;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  align-items: center;
  justify-content: center;
}

.qty-btn.minus {
  background: #fee2e2;
  color: #dc2626;
}

.qty-btn.minus:hover:not(:disabled) {
  background: #fecaca;
}

.qty-btn.plus {
  background: #e53935;
  color: white;
}

.qty-btn.plus:hover {
  background: #c62828;
}

.qty-btn:disabled {
  background: #e5e7eb;
  color: #9ca3af;
  cursor: not-allowed;
}

.qty-value {
  min-width: 36px;
  text-align: center;
  font-weight: 700;
  color: #2c3e50;
  font-size: 1.1rem;
}

.no-dishes {
  text-align: center;
  padding: 40px;
  color: #9ca3af;
}

/* Selected dishes */
.selected-section {
  background: #fafafa;
  border-radius: 16px;
  padding: 24px;
}

.no-selected {
  text-align: center;
  padding: 20px;
  color: #9ca3af;
}

.selected-dishes {
  max-height: 150px;
  overflow-y: auto;
  margin-bottom: 16px;
}

.selected-item {
  display: flex;
  justify-content: space-between;
  padding: 12px 0;
  border-bottom: 1px dashed #e5e7eb;
}

.selected-item:last-child {
  border-bottom: none;
}

.item-name {
  color: #495057;
  font-size: 0.95rem;
}

.item-price {
  font-weight: 600;
  color: #2c3e50;
}

/* Total section */
.total-section {
  background: linear-gradient(135deg, #e5393515 0%, #ff6f0015 100%);
  padding: 18px;
  border-radius: 12px;
}

.total-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: 700;
  font-size: 1.1rem;
}

.total-amount {
  font-size: 1.5rem;
  color: #e53935;
}

/* Actions */
.form-actions {
  display: flex;
  gap: 16px;
  margin-top: 30px;
  padding-top: 24px;
  border-top: 2px solid #e5e7eb;
}

.btn-cancel,
.btn-submit {
  flex: 1;
  padding: 18px 24px;
  border: none;
  border-radius: 14px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s;
}

.btn-cancel {
  background: #f3f4f6;
  color: #374151;
}

.btn-cancel:hover {
  background: #e5e7eb;
}

.btn-submit {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(229, 57, 53, 0.4);
}

.btn-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

@media (max-width: 768px) {
  .form-layout {
    grid-template-columns: 1fr;
  }
  
  .modal-content {
    max-width: 100%;
    border-radius: 16px;
  }
  
  .form-row {
    grid-template-columns: 1fr;
  }
}
</style>
