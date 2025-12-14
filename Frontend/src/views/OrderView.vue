<template>
  <div class="order-page">
    <section class="hero-banner">
      <div class="container">
        <h1>🍽️ Đặt Món Ngon - Giao Tận Nơi</h1>
        <p>Khám phá hàng trăm món ăn từ các nhà hàng uy tín</p>
      </div>
    </section>

    <div class="container">
      <div class="order-layout">
        <!-- Sidebar Filters -->
        <aside class="sidebar">
          <div class="filter-card">
            <h3>🔍 Tìm kiếm</h3>
            <Input
              v-model="searchQuery"
              type="text"
              placeholder="Tìm món ăn..."
              variant="search"
            />
          </div>

          <div class="filter-card">
            <ListOption
              v-model="selectedCategory"
              :options="categories"
              title="📂 Danh mục"
            />
          </div>

          <div class="filter-card">
            <ListOption
              v-model="selectedPriceRange"
              :options="priceRanges"
              title="💰 Khoảng giá"
              label-key="label"
            />
          </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
     

          <!-- Loading State -->
          <div v-if="loading" class="loading-grid">
            <div v-for="n in 12" :key="n" class="skeleton-card">
              <div class="skeleton-image"></div>
              <div class="skeleton-text"></div>
              <div class="skeleton-text short"></div>
            </div>
          </div>

          <!-- Error State -->
          <div v-else-if="error" class="error-state">
            <p>{{ error }}</p>
            <button @click="fetchDishes" class="retry-btn">Thử lại</button>
          </div>

          <!-- Dishes Grid - Sử dụng Card component -->
          <div v-else class="dishes-grid">
            <Card
              v-for="dish in sortedDishes"
              :key="dish.id"
              :dish="dish"
              @add-to-cart="addToCart"
            />
          </div>

          <div
            v-if="!loading && !error && filteredDishes.length === 0"
            class="empty-state"
          >
            <div class="empty-icon">🔍</div>
            <h3>Không tìm thấy món ăn</h3>
            <p>Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
          </div>
        </main>

        <!-- Cart Sidebar -->
        <ListFoot
          :cart="cart"
          :shipping-fee="shippingFee"
          @increase="increaseQuantity"
          @decrease="decreaseQuantity"
          @remove="removeFromCart"
          @checkout="checkout"
          @require-login="openLoginModal"
        />
      </div>
    </div>

    <!-- Checkout Modal -->
    <CheckoutModal
      :visible="showCheckoutModal"
      :cart="cart"
      :shipping-fee="shippingFee"
      @close="closeCheckoutModal"
      @confirm="handleConfirmOrder"
    />

    <!-- Login Modal -->
    <LoginView
      v-model="showLoginModal"
      @success="handleLoginSuccess"
    />
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import Card from "@/components/Home/Card.vue";
import Input from "@/components/ui/Input.vue";
import ListOption from "@/components/ui/ListOption.vue";
import ListFoot from "@/components/ui/ListFoot.vue";
import CheckoutModal from "@/components/ui/CheckoutModal.vue";
import LoginView from "@/components/form/LoginView.vue";
import { monAnService, type MonAn } from "@/services/monan.service";

interface Dish {
  id: number;
  name: string;
  restaurant: string;
  restaurantAddress?: string;
  price: number;
  emoji: string;
  category: string;
  description: string;
  image?: string;
  rating?: number;
}

interface CartItem {
  dish: Dish;
  quantity: number;
}

// State
const loading = ref(true);
const error = ref<string | null>(null);
const searchQuery = ref("");
const selectedCategory = ref("Tất cả");
const selectedPriceRange = ref({ label: "Tất cả", min: 0, max: Infinity });

const sortOptions = [
  { value: "default", label: "Mặc định" },
  { value: "price-asc", label: "Giá tăng dần" },
  { value: "price-desc", label: "Giá giảm dần" },
  { value: "name", label: "Tên A-Z" },
];
const sortBy = ref(sortOptions[0]);

const categories = [
  "Tất cả",
  "Phở",
  "Bún",
  "Cơm",
  "Bánh",
  "Món Nướng",
  "Lẩu",
  "Đồ Uống",
];

const priceRanges = [
  { label: "Tất cả", min: 0, max: Infinity },
  { label: "Dưới 50k", min: 0, max: 50000 },
  { label: "50k - 100k", min: 50000, max: 100000 },
  { label: "Trên 100k", min: 100000, max: Infinity },
];

// Danh sách món ăn từ API
const dishes = ref<Dish[]>([]);
const cart = ref<CartItem[]>([]);

// Checkout Modal
const showCheckoutModal = ref(false);

// Login Modal
const showLoginModal = ref(false);
const pendingCheckout = ref(false); // Flag để mở checkout sau khi login

/**
 * Lấy emoji phù hợp với tên món
 */
const getEmojiForDish = (name: string): string => {
  const lower = name.toLowerCase();
  if (lower.includes("phở")) return "🍜";
  if (lower.includes("bún")) return "🍜";
  if (lower.includes("cơm")) return "🍚";
  if (lower.includes("gà")) return "�";
  if (lower.includes("bò") || lower.includes("thịt")) return "🥩";
  if (lower.includes("bánh mì")) return "🥖";
  if (lower.includes("bánh")) return "🍰";
  if (lower.includes("chè") || lower.includes("kem")) return "🍨";
  if (lower.includes("gỏi") || lower.includes("cuốn")) return "🥗";
  if (lower.includes("lẩu")) return "🍲";
  if (lower.includes("nướng")) return "🔥";
  if (
    lower.includes("trà") ||
    lower.includes("cà phê") ||
    lower.includes("nước")
  )
    return "🥤";
  return "🍽️";
};

/**
 * Lấy category từ tên món
 */
const getCategoryFromName = (name: string): string => {
  const lower = name.toLowerCase();
  if (lower.includes("phở")) return "Phở";
  if (lower.includes("bún")) return "Bún";
  if (lower.includes("cơm")) return "Cơm";
  if (lower.includes("bánh")) return "Bánh";
  if (lower.includes("nướng")) return "Món Nướng";
  if (lower.includes("lẩu")) return "Lẩu";
  if (
    lower.includes("trà") ||
    lower.includes("cà phê") ||
    lower.includes("nước")
  )
    return "Đồ Uống";
  return "Khác";
};

/**
 * Transform dữ liệu từ API sang format của Card component
 */
const transformDish = (dish: MonAn): Dish => {
  return {
    id: dish.MonAnID,
    name: dish.TenMon,
    restaurant: dish.nha_hang?.TenNhaHang || "Chưa có nhà hàng",
    restaurantAddress: dish.nha_hang?.DiaChi || "",
    price: Number(dish.DonGia),
    image: dish.HinhAnh || undefined,
    emoji: getEmojiForDish(dish.TenMon),
    category: getCategoryFromName(dish.TenMon),
    description: dish.MoTa || "",
    rating: 4.5 + Math.random() * 0.5,
  };
};

/**
 * Gọi API lấy tất cả món ăn
 */
const fetchDishes = async () => {
  loading.value = true;
  error.value = null;

  try {
    // Gọi API lấy tất cả món ăn (không phân trang)
    const response = await monAnService.getAll({ per_page: 100 });

    // Transform dữ liệu từ API
    dishes.value = response.data.items.map(transformDish);
  } catch (err: any) {
    console.error("Error fetching dishes:", err);
    error.value = "Không thể tải danh sách món ăn. Vui lòng thử lại.";
    dishes.value = [];
  } finally {
    loading.value = false;
  }
};

// Gọi API khi component mount
onMounted(() => {
  fetchDishes();
});

// Computed
const filteredDishes = computed(() => {
  return dishes.value.filter((dish) => {
    const matchSearch =
      dish.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
      dish.description.toLowerCase().includes(searchQuery.value.toLowerCase());
    const matchCategory =
      selectedCategory.value === "Tất cả" ||
      dish.category === selectedCategory.value;
    const matchPrice =
      dish.price >= selectedPriceRange.value.min &&
      dish.price <= selectedPriceRange.value.max;

    return matchSearch && matchCategory && matchPrice;
  });
});

const sortedDishes = computed(() => {
  const sorted = [...filteredDishes.value];
  const sortValue = sortBy.value?.value || "default";

  switch (sortValue) {
    case "price-asc":
      return sorted.sort((a, b) => a.price - b.price);
    case "price-desc":
      return sorted.sort((a, b) => b.price - a.price);
    case "name":
      return sorted.sort((a, b) => a.name.localeCompare(b.name));
    default:
      return sorted;
  }
});

const shippingFee = computed(() => {
  const subtotal = cart.value.reduce((sum, item) => sum + item.dish.price * item.quantity, 0);
  return subtotal > 0 ? 20000 : 0;
});

// Methods
const formatPrice = (price: number) => {
  return price.toLocaleString("vi-VN") + "đ";
};

const addToCart = (dish: Dish) => {
  const existingItem = cart.value.find((item) => item.dish.id === dish.id);

  if (existingItem) {
    existingItem.quantity++;
  } else {
    cart.value.push({ dish, quantity: 1 });
  }
};

const increaseQuantity = (dishId: number) => {
  const item = cart.value.find((item) => item.dish.id === dishId);
  if (item) item.quantity++;
};

const decreaseQuantity = (dishId: number) => {
  const item = cart.value.find((item) => item.dish.id === dishId);
  if (item) {
    if (item.quantity > 1) {
      item.quantity--;
    } else {
      removeFromCart(dishId);
    }
  }
};

const removeFromCart = (dishId: number) => {
  const index = cart.value.findIndex((item) => item.dish.id === dishId);
  if (index > -1) {
    cart.value.splice(index, 1);
  }
};

const checkout = () => {
  if (cart.value.length === 0) return;
  showCheckoutModal.value = true;
};

const closeCheckoutModal = () => {
  showCheckoutModal.value = false;
};

// Login modal functions
const openLoginModal = () => {
  pendingCheckout.value = true; // Đánh dấu cần mở checkout sau khi login
  showLoginModal.value = true;
};

const handleLoginSuccess = () => {
  showLoginModal.value = false;
  // Nếu đang pending checkout thì mở checkout modal ngay
  if (pendingCheckout.value) {
    pendingCheckout.value = false;
    // Delay nhỏ để đảm bảo modal login đã đóng
    setTimeout(() => {
      showCheckoutModal.value = true;
    }, 100);
  }
};

const handleConfirmOrder = (orderData: any) => {
  console.log("Order confirmed:", orderData);

  // Hiển thị thông báo thành công
  alert(
    `🎉 Đặt hàng thành công!\n\n` +
    `👤 Khách hàng: ${orderData.customer.name}\n` +
    `📱 SĐT: ${orderData.customer.phone}\n` +
    `📍 Địa chỉ: ${orderData.customer.address}\n` +
    `💳 Thanh toán: ${orderData.customer.paymentMethod === 'cash' ? 'Tiền mặt' : orderData.customer.paymentMethod === 'banking' ? 'Chuyển khoản' : 'MoMo'}\n\n` +
    `💰 Tổng tiền: ${formatPrice(orderData.total)}\n\n` +
    `Cảm ơn bạn đã đặt hàng!`
  );

  // Reset giỏ hàng và đóng modal
  cart.value = [];
  showCheckoutModal.value = false;
};
</script>

<style scoped>
.order-page {
  min-height: 100vh;
  background: #f8f9fa;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
  padding: 0 20px;
}

.hero-banner {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 60px 20px;
  text-align: center;
  color: white;
  margin-bottom: 40px;
}

.hero-banner h1 {
  font-size: 2.5rem;
  margin-bottom: 0.5rem;
  font-weight: 800;
}

.hero-banner p {
  font-size: 1.1rem;
  opacity: 0.95;
}

.order-layout {
  display: grid;
  grid-template-columns: 280px 1fr 350px;
  gap: 30px;
  padding-bottom: 40px;
}

.sidebar {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.filter-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.filter-card h3 {
  font-size: 1.1rem;
  margin-bottom: 15px;
  color: #2c3e50;
}

/* search input handled by Input component */
.category-list,
.price-filters {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.category-btn,
.price-btn {
  padding: 10px 16px;
  background: #f8f9fa;
  border: 2px solid transparent;
  border-radius: 10px;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  color: #495057;
}

.category-btn:hover,
.price-btn:hover {
  background: #e9ecef;
}

.category-btn.active,
.price-btn.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: transparent;
}

.main-content {
  min-height: 600px;
}

.content-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 25px;
}

.content-header h2 {
  font-size: 1.5rem;
  color: #2c3e50;
}

.sort-select {
  padding: 10px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 0.95rem;
  cursor: pointer;
  background: white;
}

.dishes-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.empty-state {
  text-align: center;
  padding: 80px 20px;
}

.empty-icon {
  font-size: 5rem;
  margin-bottom: 20px;
  opacity: 0.5;
}

.empty-state h3 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin-bottom: 10px;
}

.empty-state p {
  color: #7f8c8d;
}

/* Loading Skeleton */
.loading-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.skeleton-card {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.skeleton-image {
  width: 100%;
  height: 180px;
  border-radius: 12px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
  margin-bottom: 15px;
}

.skeleton-text {
  height: 20px;
  border-radius: 4px;
  margin-bottom: 10px;
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s infinite;
}

.skeleton-text.short {
  width: 60%;
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

/* Error State */
.error-state {
  text-align: center;
  padding: 60px 20px;
  color: #7f8c8d;
}

.retry-btn {
  margin-top: 20px;
  padding: 12px 30px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 10px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
}

.retry-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

@media (max-width: 1200px) {
  .order-layout {
    grid-template-columns: 1fr;
  }

  .sidebar,
  .cart-sidebar {
    position: static;
  }

  .cart-sidebar {
    order: -1;
  }
}

@media (max-width: 768px) {
  .hero-banner h1 {
    font-size: 1.8rem;
  }

  .dishes-grid {
    grid-template-columns: 1fr;
  }
}
</style>
