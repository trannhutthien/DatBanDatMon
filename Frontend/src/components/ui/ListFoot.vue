<template>
  <aside class="cart-sidebar">
    <div class="cart-card">
      <div class="cart-header">
        <h3>🛒 Giỏ hàng</h3>
        <span class="cart-count">{{ cartItemCount }} món</span>
      </div>

      <div class="cart-items">
        <div v-if="cart.length === 0" class="cart-empty">
          <div class="empty-cart-icon">🛒</div>
          <p>Giỏ hàng trống</p>
        </div>

        <div v-for="item in cart" :key="item.dish.id" class="cart-item">
          <div class="item-emoji">{{ item.dish.emoji }}</div>
          <div class="item-info">
            <h5>{{ item.dish.name }}</h5>
            <span class="item-price">{{ formatPrice(item.dish.price) }}</span>
          </div>
          <div class="item-controls">
            <button @click="$emit('decrease', item.dish.id)" class="qty-btn">
              -
            </button>
            <span class="qty">{{ item.quantity }}</span>
            <button @click="$emit('increase', item.dish.id)" class="qty-btn">
              +
            </button>
          </div>
          <button @click="$emit('remove', item.dish.id)" class="btn-remove">
            ×
          </button>
        </div>
      </div>

      <div v-if="cart.length > 0" class="cart-summary">
        <div class="summary-row">
          <span>Tạm tính:</span>
          <span>{{ formatPrice(subtotal) }}</span>
        </div>
        <div class="summary-row">
          <span>Phí giao hàng:</span>
          <span>{{ formatPrice(shippingFee) }}</span>
        </div>
        <div class="summary-row total">
          <span>Tổng cộng:</span>
          <span>{{ formatPrice(total) }}</span>
        </div>

        <button @click="handleCheckout" class="btn-checkout">Thanh toán</button>
      </div>
    </div>
  </aside>
</template>

<script setup lang="ts">
import { computed } from "vue";

/**
 * ============================================================================
 * LIST FOOT (CART) COMPONENT
 * ============================================================================
 *
 * Component giỏ hàng tái sử dụng.
 *
 * PROPS:
 * - cart: Danh sách các item trong giỏ hàng
 * - shippingFee: Phí giao hàng (mặc định: 20000)
 *
 * EVENTS:
 * - increase: Tăng số lượng món (dishId)
 * - decrease: Giảm số lượng món (dishId)
 * - remove: Xóa món khỏi giỏ (dishId)
 * - checkout: Thanh toán
 * ============================================================================
 */

export interface Dish {
  id: number;
  name: string;
  price: number;
  emoji: string;
  [key: string]: any;
}

export interface CartItem {
  dish: Dish;
  quantity: number;
}

interface Props {
  cart: CartItem[];
  shippingFee?: number;
}

const props = withDefaults(defineProps<Props>(), {
  shippingFee: 20000,
});

const emit = defineEmits<{
  increase: [dishId: number];
  decrease: [dishId: number];
  remove: [dishId: number];
  checkout: [];
  "require-login": [];
}>();

// Check login status from localStorage
const isLoggedIn = () => {
  const token = localStorage.getItem("auth_token");
  const user = localStorage.getItem("user");
  return !!(token && user);
};

// Handle checkout - check login first
const handleCheckout = () => {
  if (!isLoggedIn()) {
    // Emit event to require login first
    emit("require-login");
  } else {
    // Already logged in, proceed to checkout
    emit("checkout");
  }
};

// Computed
const cartItemCount = computed(() => {
  return props.cart.reduce((sum, item) => sum + item.quantity, 0);
});

const subtotal = computed(() => {
  return props.cart.reduce(
    (sum, item) => sum + item.dish.price * item.quantity,
    0
  );
});

const total = computed(() => {
  return subtotal.value + props.shippingFee;
});

// Methods
const formatPrice = (price: number) => {
  return price.toLocaleString("vi-VN") + "đ";
};
</script>

<style scoped>
.cart-sidebar {
  position: sticky;
  top: 90px;
  height: fit-content;
}

.cart-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.cart-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f0f0f0;
}

.cart-header h3 {
  font-size: 1.2rem;
  color: #2c3e50;
}

.cart-count {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 4px 12px;
  border-radius: 20px;
  font-size: 0.85rem;
  font-weight: 600;
}

.cart-items {
  max-height: 400px;
  overflow-y: auto;
  margin-bottom: 20px;
}

.cart-empty {
  text-align: center;
  padding: 40px 20px;
}

.empty-cart-icon {
  font-size: 4rem;
  opacity: 0.3;
  margin-bottom: 10px;
}

.cart-empty p {
  color: #7f8c8d;
}

.cart-item {
  display: grid;
  grid-template-columns: 50px 1fr auto 30px;
  gap: 12px;
  align-items: center;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 12px;
  margin-bottom: 10px;
}

.item-emoji {
  font-size: 2.5rem;
}

.item-info h5 {
  font-size: 0.95rem;
  color: #2c3e50;
  margin-bottom: 4px;
}

.item-price {
  font-size: 0.9rem;
  color: #e53935;
  font-weight: 600;
}

.item-controls {
  display: flex;
  align-items: center;
  gap: 8px;
  background: white;
  padding: 4px;
  border-radius: 8px;
}

.qty-btn {
  width: 28px;
  height: 28px;
  border: none;
  background: #667eea;
  color: white;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
  transition: all 0.2s ease;
}

.qty-btn:hover {
  background: #764ba2;
}

.qty {
  min-width: 30px;
  text-align: center;
  font-weight: 600;
  color: #2c3e50;
}

.btn-remove {
  width: 30px;
  height: 30px;
  border: none;
  background: #ff4757;
  color: white;
  border-radius: 50%;
  cursor: pointer;
  font-size: 1.2rem;
  line-height: 1;
  transition: all 0.2s ease;
}

.btn-remove:hover {
  background: #ee5a6f;
  transform: rotate(90deg);
}

.cart-summary {
  border-top: 2px solid #f0f0f0;
  padding-top: 20px;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  margin-bottom: 12px;
  color: #495057;
}

.summary-row.total {
  font-size: 1.2rem;
  font-weight: 700;
  color: #2c3e50;
  margin-top: 15px;
  padding-top: 15px;
  border-top: 2px solid #f0f0f0;
}

.btn-checkout {
  width: 100%;
  padding: 16px;
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
  border: none;
  border-radius: 12px;
  font-size: 1.1rem;
  font-weight: 700;
  cursor: pointer;
  margin-top: 20px;
  transition: all 0.3s ease;
}

.btn-checkout:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(229, 57, 53, 0.4);
}
</style>
