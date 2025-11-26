<template>
  <div class="max-w-7xl mx-auto p-4 lg:p-8">
    <section class="mb-8">
      <div class="rounded-xl bg-gradient-to-r from-primary/10 to-white p-6 flex flex-col md:flex-row items-center gap-6">
        <div class="flex-1">
          <h1 class="text-3xl md:text-4xl font-extrabold text-primary">Chọn món yêu thích — Giao tận nơi</h1>
          <p class="mt-2 text-gray-600">Khám phá các món ngon từ nhà hàng uy tín gần bạn. Thêm vào giỏ và đặt ngay.</p>
        </div>
        <div class="w-full md:w-1/3">
          <div class="flex items-center bg-white rounded-lg shadow px-3 py-2">
            <input v-model="query" placeholder="Tìm món, nhà hàng..." class="flex-1 outline-none px-2" />
            <button @click="query = ''" class="text-sm text-gray-500">Xoá</button>
          </div>
        </div>
      </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
      <main class="lg:col-span-3">
        <div class="flex flex-wrap gap-3 mb-4">
          <button v-for="c in categories" :key="c" @click="filter=c" :class="['px-3 py-1 rounded-full border', filter===c? 'bg-primary text-white border-primary':'bg-white text-gray-700']">{{c}}</button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div v-for="item in filteredItems" :key="item.id" class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
            <img :src="item.image" alt="" class="w-full h-40 object-cover" />
            <div class="p-4">
              <h3 class="font-semibold text-lg">{{item.name}}</h3>
              <p class="text-sm text-gray-500 mt-1">{{item.description}}</p>
              <div class="mt-4 flex items-center justify-between">
                <div class="text-primary font-bold">{{formatPrice(item.price)}}</div>
                <button @click="addToCart(item)" class="px-3 py-1 bg-primary text-white rounded-md hover:bg-red-700 transition">Thêm</button>
              </div>
            </div>
          </div>
        </div>
      </main>

      <aside class="lg:col-span-1">
        <div class="bg-white rounded-lg shadow p-4">
          <h4 class="font-semibold text-lg">Giỏ hàng ({{cartCount}})</h4>
          <div class="mt-4 space-y-3">
            <div v-for="entry in cart" :key="entry.item.id" class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <img :src="entry.item.image" class="w-12 h-12 object-cover rounded" />
                <div>
                  <div class="font-medium">{{entry.item.name}}</div>
                  <div class="text-sm text-gray-500">x{{entry.qty}}</div>
                </div>
              </div>
              <div class="text-right">
                <div class="font-medium">{{formatPrice(entry.item.price * entry.qty)}}</div>
              </div>
            </div>
            <div v-if="cart.length===0" class="text-sm text-gray-500">Giỏ hàng trống</div>
          </div>

          <div class="mt-4 border-t pt-4">
            <div class="flex justify-between font-semibold">Tổng tiền: <span>{{formatPrice(total)}}</span></div>
            <button :disabled="cart.length===0" class="mt-4 w-full px-4 py-2 bg-primary text-white rounded-md disabled:opacity-50">Thanh toán</button>
          </div>
        </div>
      </aside>
    </div>
  </div>
</template>

<script setup lang="ts">
import { reactive, computed, ref } from 'vue'

interface Item { id: number; name: string; description: string; price: number; image: string; category: string }

const query = ref('')
const filter = ref('Tất cả')

const items = ref<Item[]>([
  { id: 1, name: 'Phở Bò', description: 'Phở truyền thống thơm ngon', price: 65000, image: 'https://images.unsplash.com/photo-1543352634-8d1c1d2a6b7e?w=800&q=60', category: 'Phở' },
  { id: 2, name: 'Bún Chả', description: 'Chả nướng thơm lừng', price: 70000, image: 'https://images.unsplash.com/photo-1544025162-d76694265947?w=800&q=60', category: 'Bún' },
  { id: 3, name: 'Cơm Gà', description: 'Cơm gà xé phay', price: 60000, image: 'https://images.unsplash.com/photo-1604908554109-3b76a6f4d2b7?w=800&q=60', category: 'Cơm' },
  { id: 4, name: 'Bánh Mì', description: 'Bánh mì pate đặc trưng', price: 30000, image: 'https://images.unsplash.com/photo-1544025162-3f2f2b7f2e8b?w=800&q=60', category: 'Bánh Mì' },
])

const categories = computed(() => ['Tất cả', ...Array.from(new Set(items.value.map(i=>i.category)) )])

const filteredItems = computed(() => {
  return items.value.filter(i => (filter.value==='Tất cả' || i.category===filter.value) && (i.name.toLowerCase().includes(query.value.toLowerCase()) || i.description.toLowerCase().includes(query.value.toLowerCase())))
})

const cart = reactive<{ item: Item; qty: number }[]>([])

function addToCart(item: Item) {
  const found = cart.find(c => c.item.id === item.id)
  if (found) found.qty++
  else cart.push({ item, qty: 1 })
}

const cartCount = computed(() => cart.reduce((s, e) => s + e.qty, 0))
const total = computed(() => cart.reduce((s, e) => s + e.qty * e.item.price, 0))

function formatPrice(v:number){ return v.toLocaleString('vi-VN', { style: 'currency', currency: 'VND' }) }
</script>

<style scoped>
/* small adjustments to keep card images consistent when CDN images block */
img { background: #f3f4f6 }
</style>
