<template>
  <div v-if="modelValue" class="modal-overlay" @click.self="closeModal">
    <div class="modal-content">
      <div class="modal-header">
        <h2>{{ isAddMode ? "➕ Thêm nhà hàng" : "✏️ Chỉnh sửa nhà hàng" }}</h2>
        <button class="close-btn" @click="closeModal">×</button>
      </div>
      <form @submit.prevent="handleSave" class="edit-form">
        <div class="form-group">
          <label>Tên nhà hàng</label>
          <input
            v-model="form.TenNhaHang"
            type="text"
            placeholder="Nhập tên nhà hàng"
            required
          />
        </div>
        <div class="form-group">
          <label>Địa chỉ</label>
          <input v-model="form.DiaChi" type="text" placeholder="Nhập địa chỉ" />
        </div>
        <div class="form-group">
          <label>Số điện thoại</label>
          <input
            v-model="form.SDT"
            type="tel"
            placeholder="Nhập số điện thoại"
          />
        </div>
        <div class="form-actions">
          <button type="button" class="btn-cancel" @click="closeModal">
            Hủy
          </button>
          <button type="submit" class="btn-save" :disabled="saving">
            {{
              saving ? "Đang lưu..." : isAddMode ? "Thêm mới" : "Lưu thay đổi"
            }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, watch } from "vue";
import nhaHangService, { type NhaHang } from "@/services/nhahang.service";

const props = defineProps<{
  modelValue: boolean;
  nhaHang: NhaHang | null;
  isAddMode?: boolean;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: boolean): void;
  (e: "saved"): void;
}>();

const saving = ref(false);
const form = ref({
  TenNhaHang: "",
  DiaChi: "",
  SDT: "",
});

// Watch for nhaHang changes to populate form
watch(
  () => props.nhaHang,
  (newNhaHang) => {
    if (newNhaHang) {
      form.value = {
        TenNhaHang: newNhaHang.TenNhaHang,
        DiaChi: newNhaHang.DiaChi || "",
        SDT: newNhaHang.SDT || "",
      };
    } else {
      form.value = {
        TenNhaHang: "",
        DiaChi: "",
        SDT: "",
      };
    }
  },
  { immediate: true }
);

const closeModal = () => {
  emit("update:modelValue", false);
};

const handleSave = async () => {
  saving.value = true;
  try {
    if (props.isAddMode) {
      await nhaHangService.create({
        TenNhaHang: form.value.TenNhaHang,
        DiaChi: form.value.DiaChi || undefined,
        SDT: form.value.SDT || undefined,
      });
      alert("Thêm nhà hàng thành công!");
    } else {
      if (!props.nhaHang) return;
      await nhaHangService.update(props.nhaHang.NhaHangID, {
        TenNhaHang: form.value.TenNhaHang,
        DiaChi: form.value.DiaChi || undefined,
        SDT: form.value.SDT || undefined,
      });
      alert("Cập nhật thành công!");
    }
    emit("saved");
    closeModal();
  } catch (error) {
    console.error("Error saving nha hang:", error);
    alert(props.isAddMode ? "Thêm nhà hàng thất bại!" : "Cập nhật thất bại!");
  } finally {
    saving.value = false;
  }
};
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
  border-radius: 16px;
  width: 90%;
  max-width: 500px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  animation: slideUp 0.3s ease;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 24px;
  border-bottom: 1px solid #e5e7eb;
}

.modal-header h2 {
  font-size: 1.25rem;
  color: #1f2937;
  margin: 0;
}
.close-btn {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #6b7280;
}
.close-btn:hover {
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
  margin-bottom: 8px;
  color: #374151;
}
.form-group input {
  width: 100%;
  padding: 12px 16px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.form-group input:focus {
  outline: none;
  border-color: #f97316;
  box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
}

.form-actions {
  display: flex;
  gap: 12px;
  justify-content: flex-end;
  padding-top: 16px;
  border-top: 1px solid #e5e7eb;
}
.btn-cancel {
  padding: 12px 24px;
  border: 1px solid #d1d5db;
  background: white;
  border-radius: 8px;
  cursor: pointer;
}
.btn-cancel:hover {
  background: #f3f4f6;
}
.btn-save {
  padding: 12px 24px;
  background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
  color: white;
  border: none;
  border-radius: 8px;
  cursor: pointer;
}
.btn-save:hover {
  opacity: 0.9;
}
.btn-save:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>
