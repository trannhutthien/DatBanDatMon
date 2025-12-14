<template>
  <Teleport to="body">
    <Transition name="modal">
      <div v-if="visible" class="modal-overlay" @click.self="closeModal">
        <div class="modal-container">
          <!-- Header -->
          <div class="modal-header">
            <h2>🧾 Xác nhận đơn hàng</h2>
            <button class="btn-close" @click="closeModal">×</button>
          </div>

          <!-- Body -->
          <div class="modal-body">
            <!-- Order Summary -->
            <div class="order-summary">
              <h3>📦 Đơn hàng của bạn</h3>
              <div class="order-items">
                <div v-for="item in cart" :key="item.dish.id" class="order-item">
                  <span class="item-emoji">{{ item.dish.emoji }}</span>
                  <span class="item-name">{{ item.dish.name }}</span>
                  <span class="item-qty">x{{ item.quantity }}</span>
                  <span class="item-price">{{ formatPrice(item.dish.price * item.quantity) }}</span>
                </div>
              </div>
              <div class="order-totals">
                <div class="total-row">
                  <span>Tạm tính:</span>
                  <span>{{ formatPrice(subtotal) }}</span>
                </div>
                <div class="total-row">
                  <span>Phí giao hàng:</span>
                  <span>{{ formatPrice(shippingFee) }}</span>
                </div>
                <div class="total-row final">
                  <span>Tổng cộng:</span>
                  <span>{{ formatPrice(total) }}</span>
                </div>
              </div>
            </div>

            <!-- Customer Info -->
            <div class="customer-form">
              <h3>👤 Thông tin giao hàng</h3>
              <div class="form-group">
                <label>Họ và tên *</label>
                <input v-model="form.name" type="text" placeholder="Nhập họ tên..." />
              </div>
              <div class="form-group">
                <label>Số điện thoại *</label>
                <input v-model="form.phone" type="tel" placeholder="Nhập số điện thoại..." />
              </div>
              <div class="form-group">
                <label>Địa chỉ giao hàng *</label>
                <textarea v-model="form.address" placeholder="Nhập địa chỉ chi tiết..." rows="3"></textarea>
              </div>
              <div class="form-group">
                <label>Ghi chú</label>
                <textarea v-model="form.note" placeholder="Ghi chú cho nhà hàng (không bắt buộc)..." rows="2"></textarea>
              </div>
            </div>

            <!-- Payment Method -->
            <div class="payment-method">
              <h3>💳 Phương thức thanh toán</h3>
              <div class="payment-options">
                <label class="payment-option" :class="{ active: form.paymentMethod === 'cash' }">
                  <input type="radio" v-model="form.paymentMethod" value="cash" />
                  <span class="option-icon">💵</span>
                  <span class="option-text">Tiền mặt khi nhận hàng</span>
                </label>
                <label class="payment-option" :class="{ active: form.paymentMethod === 'banking' }">
                  <input type="radio" v-model="form.paymentMethod" value="banking" />
                  <span class="option-icon">🏦</span>
                  <span class="option-text">Chuyển khoản ngân hàng</span>
                </label>
                <label class="payment-option" :class="{ active: form.paymentMethod === 'momo' }">
                  <input type="radio" v-model="form.paymentMethod" value="momo" />
                  <span class="option-icon">📱</span>
                  <span class="option-text">Ví MoMo</span>
                </label>
              </div>
            </div>
          </div>

          <!-- Footer -->
          <div class="modal-footer">
            <button class="btn-cancel" @click="closeModal">Hủy</button>
            <button class="btn-confirm" @click="confirmOrder" :disabled="!isFormValid">
              ✅ Xác nhận đặt hàng
            </button>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";

export interface CartItem {
  dish: {
    id: number;
    name: string;
    price: number;
    emoji: string;
  };
  quantity: number;
}

interface Props {
  visible: boolean;
  cart: CartItem[];
  shippingFee?: number;
}

const props = withDefaults(defineProps<Props>(), {
  shippingFee: 20000
});

const emit = defineEmits<{
  close: [];
  confirm: [orderData: any];
}>();

const form = ref({
  name: "",
  phone: "",
  address: "",
  note: "",
  paymentMethod: "cash"
});

// Reset form when modal opens
watch(() => props.visible, (newVal) => {
  if (newVal) {
    form.value = { name: "", phone: "", address: "", note: "", paymentMethod: "cash" };
  }
});

const subtotal = computed(() => props.cart.reduce((sum, item) => sum + item.dish.price * item.quantity, 0));
const total = computed(() => subtotal.value + props.shippingFee);
const isFormValid = computed(() => form.value.name && form.value.phone && form.value.address);

const formatPrice = (price: number) => price.toLocaleString("vi-VN") + "đ";
const closeModal = () => emit("close");

const confirmOrder = () => {
  if (!isFormValid.value) return;
  emit("confirm", {
    customer: { ...form.value },
    items: props.cart,
    subtotal: subtotal.value,
    shippingFee: props.shippingFee,
    total: total.value
  });
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 20px;
}

.modal-container {
  background: white;
  border-radius: 20px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 25px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
}

.modal-header h2 {
  font-size: 1.4rem;
  margin: 0;
}

.btn-close {
  width: 36px;
  height: 36px;
  border: none;
  background: rgba(255, 255, 255, 0.2);
  color: white;
  border-radius: 50%;
  font-size: 1.5rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-close:hover {
  background: rgba(255, 255, 255, 0.3);
  transform: rotate(90deg);
}

.modal-body {
  padding: 25px;
  overflow-y: auto;
  flex: 1;
}

.order-summary, .customer-form, .payment-method {
  margin-bottom: 25px;
}

.order-summary h3, .customer-form h3, .payment-method h3 {
  font-size: 1.1rem;
  color: #2c3e50;
  margin-bottom: 15px;
  padding-bottom: 10px;
  border-bottom: 2px solid #f0f0f0;
}

.order-items {
  max-height: 150px;
  overflow-y: auto;
}

.order-item {
  display: grid;
  grid-template-columns: 40px 1fr 50px 80px;
  gap: 10px;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 10px;
  margin-bottom: 8px;
  align-items: center;
}

.item-emoji { font-size: 1.5rem; }
.item-name { font-weight: 500; color: #2c3e50; }
.item-qty { color: #7f8c8d; text-align: center; }
.item-price { font-weight: 600; color: #e53935; text-align: right; }

.order-totals {
  margin-top: 15px;
  padding-top: 15px;
  border-top: 2px dashed #e0e0e0;
}

.total-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 8px;
  color: #495057;
}

.total-row.final {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c3e50;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 2px solid #e0e0e0;
}

.form-group {
  margin-bottom: 15px;
}

.form-group label {
  display: block;
  font-weight: 600;
  color: #2c3e50;
  margin-bottom: 8px;
  font-size: 0.95rem;
}

.form-group input, .form-group textarea {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  font-family: inherit;
}

.form-group input:focus, .form-group textarea:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.payment-options {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.payment-option {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 15px;
  background: #f8f9fa;
  border: 2px solid transparent;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.payment-option:hover {
  background: #e9ecef;
}

.payment-option.active {
  background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
  border-color: #667eea;
}

.payment-option input {
  display: none;
}

.option-icon {
  font-size: 1.5rem;
}

.option-text {
  font-weight: 500;
  color: #2c3e50;
}

.modal-footer {
  display: flex;
  gap: 15px;
  padding: 20px 25px;
  background: #f8f9fa;
  border-top: 1px solid #e0e0e0;
}

.btn-cancel, .btn-confirm {
  flex: 1;
  padding: 14px 20px;
  border: none;
  border-radius: 12px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-cancel {
  background: #e0e0e0;
  color: #495057;
}

.btn-cancel:hover {
  background: #d0d0d0;
}

.btn-confirm {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
}

.btn-confirm:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(229, 57, 53, 0.4);
}

.btn-confirm:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

/* Animations */
.modal-enter-active, .modal-leave-active {
  transition: all 0.3s ease;
}

.modal-enter-from, .modal-leave-to {
  opacity: 0;
}

.modal-enter-from .modal-container, .modal-leave-to .modal-container {
  transform: scale(0.9) translateY(20px);
}

@media (max-width: 600px) {
  .modal-container {
    max-height: 100vh;
    border-radius: 0;
  }

  .order-item {
    grid-template-columns: 30px 1fr 40px 70px;
    font-size: 0.9rem;
  }
}
</style>

