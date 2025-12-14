<template>
  <div class="dish-card">
    <!-- Hình ảnh món ăn -->
    <div class="dish-image">
      <img v-if="dish.image" :src="dish.image" :alt="dish.name" />
      <div v-else class="dish-emoji">{{ dish.emoji }}</div>
    </div>

    <!-- Thông tin món ăn -->
    <div class="dish-info">
      <h4 class="dish-name">{{ dish.name }}</h4>

      <!-- Tên nhà hàng -->
      <p class="restaurant-name">{{ dish.restaurant }}</p>

      <!-- Địa chỉ nhà hàng (nếu có) -->
      <p v-if="dish.restaurantAddress" class="restaurant-address">
        📍 {{ dish.restaurantAddress }}
      </p>

      <!-- Giá tiền -->
      <div class="price-section">
        <span class="price">{{ formatPrice(dish.price) }}</span>
        <span v-if="dish.originalPrice" class="original-price">
          {{ formatPrice(dish.originalPrice) }}
        </span>
      </div>

      <!-- Nút thêm giỏ hàng -->
      <div class="card-actions">
        <FormButton
          variant="primary"
          size="medium"
          :block="true"
          @click="addToCart"
          :loading="isAdding"
        >
          <span class="cart-icon">🛒</span>
          Thêm vào giỏ
        </FormButton>
      </div>
    </div>

    <!-- Badge giảm giá -->
    <div v-if="dish.discount" class="discount-badge">-{{ dish.discount }}%</div>

    <!-- Rating -->
    <div v-if="dish.rating" class="rating">
      <span class="stars">⭐</span>
      <span class="rating-text">{{ dish.rating.toFixed(1) }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
/**
 * ============================================================================
 * CARD COMPONENT - HIỂN THỊ MÓN ĂN
 * ============================================================================
 *
 * Component hiển thị thông tin một món ăn dạng card.
 *
 * PROPS:
 * - dish: Object chứa thông tin món ăn
 *   - id: ID món ăn
 *   - name: Tên món
 *   - restaurant: Tên nhà hàng
 *   - restaurantAddress: Địa chỉ nhà hàng
 *   - price: Giá tiền
 *   - originalPrice: Giá gốc (nếu có giảm giá)
 *   - image: URL hình ảnh
 *   - emoji: Emoji fallback nếu không có hình
 *   - discount: Phần trăm giảm giá
 *   - rating: Điểm đánh giá
 *
 * EVENTS:
 * - addToCart: Emit khi click nút thêm giỏ hàng
 * ============================================================================
 */

import { ref } from "vue";
import FormButton from "@/components/ui/Button.vue";

// Interface định nghĩa cấu trúc món ăn
interface Dish {
  id: number;
  name: string;
  restaurant: string;
  restaurantAddress?: string; // Địa chỉ nhà hàng
  price: number;
  originalPrice?: number;
  image?: string;
  emoji?: string;
  discount?: number;
  rating?: number;
}

// Props
const props = defineProps<{
  dish: Dish;
}>();

// Events
const emit = defineEmits<{
  addToCart: [dish: Dish];
}>();

// State
const isAdding = ref(false);

/**
 * Format giá tiền theo định dạng Việt Nam
 * @param price - Giá tiền (number)
 * @returns String đã format (VD: "65.000đ")
 */
const formatPrice = (price: number): string => {
  return price.toLocaleString("vi-VN") + "đ";
};

/**
 * Xử lý thêm vào giỏ hàng
 * Có loading state để UX tốt hơn
 */
const addToCart = async () => {
  isAdding.value = true;

  // Simulate API call (sau này thay bằng API thật)
  await new Promise((resolve) => setTimeout(resolve, 500));

  // Emit event lên parent
  emit("addToCart", props.dish);

  isAdding.value = false;
};
</script>

<style scoped>
.dish-card {
  position: relative;
  background: white;
  border-radius: 20px;
  padding: 20px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
  cursor: pointer;
  overflow: hidden;
}

.dish-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

/* Hình ảnh */
.dish-image {
  position: relative;
  width: 100%;
  height: 180px;
  border-radius: 16px;
  overflow: hidden;
  margin-bottom: 15px;
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  display: flex;
  align-items: center;
  justify-content: center;
}

.dish-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.dish-card:hover .dish-image img {
  transform: scale(1.05);
}

.dish-emoji {
  font-size: 5rem;
  filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1));
}

/* Thông tin */
.dish-info {
  text-align: center;
}

.dish-name {
  font-size: 1.25rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 8px;
  line-height: 1.3;
}

.restaurant-name {
  font-size: 0.95rem;
  color: #e53935;
  margin-bottom: 4px;
  font-weight: 600;
}

.restaurant-address {
  font-size: 0.8rem;
  color: #7f8c8d;
  margin-bottom: 12px;
  font-weight: 400;
}

/* Giá tiền */
.price-section {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-bottom: 15px;
}

.price {
  font-size: 1.4rem;
  font-weight: 800;
  color: #e53935;
}

.original-price {
  font-size: 1rem;
  color: #95a5a6;
  text-decoration: line-through;
}

/* Actions */
.card-actions {
  margin-top: 15px;
}

.cart-icon {
  margin-right: 6px;
}

/* Badges */
.discount-badge {
  position: absolute;
  top: 15px;
  left: 15px;
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 100%);
  color: white;
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 700;
  box-shadow: 0 2px 8px rgba(229, 57, 53, 0.4);
  z-index: 5;
}

.rating {
  position: absolute;
  top: 15px;
  right: 15px;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(10px);
  color: white;
  padding: 6px 10px;
  border-radius: 15px;
  font-size: 0.85rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
  z-index: 5;
}

.stars {
  font-size: 0.9rem;
}

.rating-text {
  font-size: 0.8rem;
}

/* Hover Effect */
.dish-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.4),
    transparent
  );
  transition: left 0.5s ease;
  z-index: 1;
}

.dish-card:hover::before {
  left: 100%;
}

/* Responsive */
@media (max-width: 640px) {
  .dish-card {
    padding: 15px;
  }

  .dish-image {
    height: 150px;
  }

  .dish-emoji {
    font-size: 4rem;
  }

  .dish-name {
    font-size: 1.1rem;
  }

  .price {
    font-size: 1.2rem;
  }
}
</style>
