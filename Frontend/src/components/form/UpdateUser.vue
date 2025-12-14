<template>
  <div v-if="modelValue" class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>{{ isAddMode ? '➕ Thêm người dùng' : '✏️ Chỉnh sửa người dùng' }}</h2>
        <button class="close-btn" @click="closeModal">×</button>
      </div>
      <form @submit.prevent="handleSave" class="edit-form">
        <div class="form-group">
          <label>Họ tên</label>
          <input
            v-model="form.HoTen"
            type="text"
            placeholder="Nhập họ tên"
            required
          />
        </div>
        <div class="form-group">
          <label>Email</label>
          <input
            v-model="form.Email"
            type="email"
            placeholder="Nhập email"
            required
          />
        </div>
        <div class="form-group">
          <label>Số điện thoại</label>
          <input
            v-model="form.SDT"
            type="tel"
            placeholder="Nhập số điện thoại"
          />
        </div>
        <div class="form-group">
          <label>Vai trò</label>
          <select v-model="form.LoaiNguoiDung" required>
            <option :value="1">👑 Admin</option>
            <option :value="2">👔 Nhân viên</option>
            <option :value="3">🧑 Khách hàng</option>
          </select>
        </div>
        <!-- Password field for add mode -->
        <div v-if="isAddMode" class="form-group">
          <label>Mật khẩu</label>
          <input
            v-model="form.MatKhau"
            type="password"
            placeholder="Nhập mật khẩu"
            required
          />
        </div>
        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="closeModal">Hủy</button>
          <button type="submit" class="btn-save" :disabled="saving">
            {{ saving ? 'Đang lưu...' : (isAddMode ? 'Thêm mới' : 'Lưu thay đổi') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue'
import nguoiDungService, { type NguoiDung } from '@/services/nguoidung.service'

const props = defineProps<{
  modelValue: boolean
  user: NguoiDung | null
  isAddMode?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
  (e: 'saved'): void
}>()

const saving = ref(false)
const form = ref({
  HoTen: '',
  Email: '',
  SDT: '',
  LoaiNguoiDung: 3,
  MatKhau: ''
})

// Watch for user changes to populate form
watch(() => props.user, (newUser) => {
  if (newUser) {
    form.value = {
      HoTen: newUser.HoTen,
      Email: newUser.Email,
      SDT: newUser.SDT || '',
      LoaiNguoiDung: newUser.LoaiNguoiDung,
      MatKhau: ''
    }
  } else {
    // Reset form for add mode
    form.value = {
      HoTen: '',
      Email: '',
      SDT: '',
      LoaiNguoiDung: 3,
      MatKhau: ''
    }
  }
}, { immediate: true })

const closeModal = () => {
  emit('update:modelValue', false)
}

const handleSave = async () => {
  saving.value = true
  try {
    if (props.isAddMode) {
      // Add new user
      await nguoiDungService.create({
        HoTen: form.value.HoTen,
        Email: form.value.Email,
        SDT: form.value.SDT || undefined,
        LoaiNguoiDung: form.value.LoaiNguoiDung,
        MatKhau: form.value.MatKhau
      })
      alert('Thêm người dùng thành công!')
    } else {
      // Update existing user
      if (!props.user) return
      await nguoiDungService.update(props.user.NguoiDungID, {
        HoTen: form.value.HoTen,
        Email: form.value.Email,
        SDT: form.value.SDT || undefined,
        LoaiNguoiDung: form.value.LoaiNguoiDung
      })
      alert('Cập nhật thành công!')
    }
    emit('saved')
    closeModal()
  } catch (error) {
    console.error('Error saving user:', error)
    alert(props.isAddMode ? 'Thêm người dùng thất bại!' : 'Cập nhật thất bại!')
  } finally {
    saving.value = false
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
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background: white;
  border-radius: 20px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: modalSlideIn 0.3s ease;
}

@keyframes modalSlideIn {
  from { opacity: 0; transform: translateY(-30px); }
  to { opacity: 1; transform: translateY(0); }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 1.3rem;
  color: #1f2937;
  margin: 0;
}

.close-btn {
  width: 36px;
  height: 36px;
  border: none;
  background: #f3f4f6;
  border-radius: 50%;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
  transition: all 0.2s;
}

.close-btn:hover {
  background: #e5e7eb;
  color: #1f2937;
}

.edit-form {
  padding: 24px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #374151;
  margin-bottom: 8px;
}

.form-group input,
.form-group select {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.2s;
  box-sizing: border-box;
}

.form-group input:focus,
.form-group select:focus {
  outline: none;
  border-color: #e53935;
  box-shadow: 0 0 0 3px rgba(229, 57, 53, 0.1);
}

.form-actions {
  display: flex;
  gap: 12px;
  margin-top: 24px;
}

.btn-cancel,
.btn-save {
  flex: 1;
  padding: 14px 20px;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
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

.btn-save {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
}

.btn-save:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(229, 57, 53, 0.4);
}

.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>

