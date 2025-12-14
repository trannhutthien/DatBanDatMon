# LUỒNG DỮ LIỆU: TỪ DATABASE ĐẾN CARD.VUE

## 📊 SƠ ĐỒ TỔNG QUAN

```
┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐     ┌─────────────────┐
│   DATABASE      │ --> │   BACKEND API   │ --> │   FRONTEND      │ --> │   CARD.VUE      │
│   (MySQL)       │     │   (Laravel)     │     │   (Vue.js)      │     │   (Component)   │
└─────────────────┘     └─────────────────┘     └─────────────────┘     └─────────────────┘
     Bảng monan          MonAnController         HomeFoot.vue            Hiển thị UI
     Bảng nhahang        routes/api.php          monan.service.ts
```

---

## 1️⃣ DATABASE (MySQL)

### Bảng `monan` - Lưu thông tin món ăn
```sql
CREATE TABLE monan (
    MonAnID     BIGINT PRIMARY KEY AUTO_INCREMENT,
    TenMon      VARCHAR(200),      -- Tên món: "Phở Bò Tái"
    NhaHangID   BIGINT,            -- FK liên kết nhà hàng
    DonGia      DECIMAL(10,2),     -- Giá: 65000.00
    HinhAnh     VARCHAR(500),      -- URL hình ảnh
    MoTa        TEXT,              -- Mô tả món ăn
    TrangThai   TINYINT,           -- 1=Còn món, 0=Hết
    TaoLuc      TIMESTAMP,         -- Ngày thêm
    CapNhatLuc  TIMESTAMP          -- Ngày cập nhật
);
```

### Bảng `nhahang` - Lưu thông tin nhà hàng
```sql
CREATE TABLE nhahang (
    NhaHangID   BIGINT PRIMARY KEY AUTO_INCREMENT,
    TenNhaHang  VARCHAR(200),      -- Tên: "Phở Hà Nội"
    DiaChi      VARCHAR(500),      -- Địa chỉ: "123 Nguyễn Huệ, Q1"
    SDT         VARCHAR(20),
    TrangThai   TINYINT
);
```

### Dữ liệu mẫu trong DB:
```
monan:
| MonAnID | TenMon        | NhaHangID | DonGia | TrangThai | TaoLuc              |
|---------|---------------|-----------|--------|-----------|---------------------|
| 1       | Phở Bò Tái    | 1         | 65000  | 1         | 2024-01-15 10:00:00 |
| 2       | Bún Chả       | 2         | 70000  | 1         | 2024-01-14 09:00:00 |

nhahang:
| NhaHangID | TenNhaHang      | DiaChi                    |
|-----------|-----------------|---------------------------|
| 1         | Phở Hà Nội      | 123 Nguyễn Huệ, Q1, HCM   |
| 2         | Bún Chả Dác Kim | 45 Lê Lợi, Q1, HCM        |
```

---

## 2️⃣ BACKEND API (Laravel)

### File: `app/Models/MonAn.php` - Model Eloquent
```php
class MonAn extends Model
{
    protected $table = 'monan';
    protected $primaryKey = 'MonAnID';
    
    // Quan hệ với nhà hàng
    public function nhaHang()
    {
        return $this->belongsTo(NhaHang::class, 'NhaHangID', 'NhaHangID');
    }
    
    // Scope lọc món còn
    public function scopeConMon($query)
    {
        return $query->where('TrangThai', 1);
    }
}
```

### File: `app/Http/Controllers/Api/MonAnController.php` - Controller
```php
class MonAnController extends Controller
{
    /**
     * API: GET /api/mon-an/latest
     * Lấy 10 món ăn mới nhất
     */
    public function latest(Request $request)
    {
        $limit = $request->get('limit', 10);
        
        // Query database
        $monAn = MonAn::with('nhaHang')    // JOIN với bảng nhahang
            ->conMon()                      // WHERE TrangThai = 1
            ->orderBy('TaoLuc', 'desc')     // ORDER BY TaoLuc DESC
            ->limit($limit)                 // LIMIT 10
            ->get();
        
        // Trả về JSON
        return response()->json([
            'success' => true,
            'data' => $monAn
        ]);
    }
}
```

### File: `routes/api.php` - Định nghĩa route
```php
Route::get('/mon-an/latest', [MonAnController::class, 'latest']);
```

### API Response (JSON):
```json
// GET http://localhost:8000/api/mon-an/latest?limit=10

{
  "success": true,
  "data": [
    {
      "MonAnID": 1,
      "TenMon": "Phở Bò Tái",
      "NhaHangID": 1,
      "DonGia": "65000.00",
      "HinhAnh": null,
      "MoTa": "Phở bò tái với nước dùng ninh xương 12 tiếng",
      "TrangThai": 1,
      "TaoLuc": "2024-01-15T10:00:00.000000Z",
      "CapNhatLuc": "2024-01-15T10:00:00.000000Z",
      "nha_hang": {                          // <-- Dữ liệu từ bảng nhahang (JOIN)
        "NhaHangID": 1,
        "TenNhaHang": "Phở Hà Nội",
        "DiaChi": "123 Nguyễn Huệ, Q1, HCM",
        "SDT": "0901234567"
      }
    },
    {
      "MonAnID": 2,
      "TenMon": "Bún Chả Hà Nội",
      "NhaHangID": 2,
      "DonGia": "70000.00",
      ...
      "nha_hang": {
        "NhaHangID": 2,
        "TenNhaHang": "Bún Chả Dác Kim",
        "DiaChi": "45 Lê Lợi, Q1, HCM"
      }
    }
  ]
}
```

---

## 3️⃣ FRONTEND SERVICE (Vue.js)

### File: `services/monan.service.ts` - Gọi API
```typescript
// Interface định nghĩa cấu trúc dữ liệu từ API
export interface NhaHang {
  NhaHangID: number
  TenNhaHang: string
  DiaChi: string | null
}

export interface MonAn {
  MonAnID: number
  TenMon: string
  NhaHangID: number | null
  DonGia: number
  HinhAnh: string | null
  MoTa: string | null
  TrangThai: number
  TaoLuc: string
  nha_hang?: NhaHang  // Dữ liệu nhà hàng (từ JOIN)
}

export const monAnService = {
  /**
   * Gọi API lấy món ăn mới nhất
   */
  async getLatest(limit: number = 10) {
    const response = await axios.get('http://localhost:8000/api/mon-an/latest', {
      params: { limit }
    })
    return response.data  // { success: true, data: [...] }
  }
}
```

---

## 4️⃣ HOMEFOOT.VUE - Component cha

### Gọi API và truyền dữ liệu xuống Card
```vue
<template>
  <!-- Lặp qua danh sách món ăn -->
  <Card 
    v-for="dish in dishes" 
    :key="dish.MonAnID"
    :dish="transformDish(dish)"    <!-- Truyền dữ liệu đã transform -->
    @add-to-cart="handleAddToCart"
  />
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import Card from './Card.vue'
import { monAnService, type MonAn } from '@/services/monan.service'

// State lưu danh sách món ăn từ API
const dishes = ref<MonAn[]>([])

/**
 * TRANSFORM: Chuyển đổi dữ liệu từ API sang format của Card
 * 
 * API trả về (tiếng Việt):     Card cần (tiếng Anh):
 * - MonAnID                 -> id
 * - TenMon                  -> name
 * - nha_hang.TenNhaHang     -> restaurant
 * - nha_hang.DiaChi         -> restaurantAddress
 * - DonGia                  -> price
 * - HinhAnh                 -> image
 */
const transformDish = (dish: MonAn) => {
  return {
    id: dish.MonAnID,                                    // ID món ăn
    name: dish.TenMon,                                   // Tên món
    restaurant: dish.nha_hang?.TenNhaHang || 'Chưa có',  // Tên nhà hàng
    restaurantAddress: dish.nha_hang?.DiaChi || '',      // Địa chỉ nhà hàng
    price: Number(dish.DonGia),                          // Giá tiền
    image: dish.HinhAnh || undefined,                    // Hình ảnh
    emoji: getEmojiForDish(dish.TenMon),                 // Emoji fallback
    rating: 4.5 + Math.random() * 0.5                    // Rating tạm
  }
}

/**
 * Gọi API khi component được mount
 */
const fetchDishes = async () => {
  const response = await monAnService.getLatest(10)
  dishes.value = response.data  // Lưu vào state
}

onMounted(() => {
  fetchDishes()
})
</script>
```

---

## 5️⃣ CARD.VUE - Component con (hiển thị)

### Nhận props và hiển thị
```vue
<template>
  <div class="dish-card">
    <!-- Hình ảnh hoặc emoji -->
    <div class="dish-image">
      <img v-if="dish.image" :src="dish.image" :alt="dish.name" />
      <div v-else class="dish-emoji">{{ dish.emoji }}</div>
    </div>
    
    <!-- Thông tin món ăn -->
    <h4>{{ dish.name }}</h4>              <!-- "Phở Bò Tái" -->
    <p>{{ dish.restaurant }}</p>          <!-- "Phở Hà Nội" -->
    <p>📍 {{ dish.restaurantAddress }}</p> <!-- "123 Nguyễn Huệ, Q1" -->
    <span>{{ formatPrice(dish.price) }}</span>  <!-- "65.000đ" -->
  </div>
</template>

<script setup lang="ts">
// Interface định nghĩa props mà Card nhận
interface Dish {
  id: number
  name: string              // Tên món
  restaurant: string        // Tên nhà hàng
  restaurantAddress?: string // Địa chỉ nhà hàng
  price: number             // Giá tiền
  image?: string            // URL hình ảnh
  emoji?: string            // Emoji fallback
  rating?: number           // Điểm đánh giá
}

// Nhận props từ component cha
const props = defineProps<{
  dish: Dish
}>()

// Format giá tiền
const formatPrice = (price: number) => {
  return price.toLocaleString('vi-VN') + 'đ'
}
</script>
```

---

## 📝 TÓM TẮT LUỒNG DỮ LIỆU

```
1. DATABASE
   └── Bảng monan: { MonAnID: 1, TenMon: "Phở Bò Tái", DonGia: 65000, NhaHangID: 1 }
   └── Bảng nhahang: { NhaHangID: 1, TenNhaHang: "Phở Hà Nội", DiaChi: "123 Nguyễn Huệ" }

2. BACKEND (Laravel)
   └── MonAn::with('nhaHang')->get()  // JOIN 2 bảng
   └── Trả về JSON: { MonAnID, TenMon, DonGia, nha_hang: { TenNhaHang, DiaChi } }

3. FRONTEND SERVICE
   └── axios.get('/api/mon-an/latest')
   └── Nhận JSON và lưu vào state

4. HOMEFOOT.VUE
   └── transformDish(): Chuyển { TenMon, nha_hang.TenNhaHang } -> { name, restaurant }
   └── Truyền xuống Card: <Card :dish="transformDish(dish)" />

5. CARD.VUE
   └── Nhận props: { id, name, restaurant, restaurantAddress, price, image, emoji }
   └── Hiển thị: {{ dish.name }}, {{ dish.restaurant }}, {{ dish.restaurantAddress }}
```

---

## 🔧 CÁCH THÊM TRƯỜNG MỚI

Ví dụ: Thêm trường `SoLuongDaBan` (số lượng đã bán)

### 1. Database - Thêm cột
```sql
ALTER TABLE monan ADD COLUMN SoLuongDaBan INT DEFAULT 0;
```

### 2. Backend - Model tự động có
```php
// MonAn model tự động có $monan->SoLuongDaBan
```

### 3. Frontend Service - Thêm vào interface
```typescript
export interface MonAn {
  ...
  SoLuongDaBan?: number  // Thêm dòng này
}
```

### 4. HomeFoot.vue - Transform
```typescript
const transformDish = (dish: MonAn) => {
  return {
    ...
    soldCount: dish.SoLuongDaBan || 0  // Thêm dòng này
  }
}
```

### 5. Card.vue - Hiển thị
```vue
<p>Đã bán: {{ dish.soldCount }}</p>
```
