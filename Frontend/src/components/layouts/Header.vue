<template>
  <header class="bg-white shadow sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex items-center justify-between h-20">
        <div class="flex-shrink-0">
          <a href="/" class="text-2xl font-bold text-primary hover:text-red-700">Ăn Ngon</a>
        </div>

        <nav class="hidden md:flex space-x-3">
          <button class="px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white transition">Ăn Uống</button>
          <button class="px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white transition">Nhà Hàng Uy Tín</button>
          <button class="px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white transition">Ưu Đãi Hot</button>
          <button class="px-4 py-2 border border-primary text-primary rounded-md hover:bg-primary hover:text-white transition">Mới Nhất</button>
        </nav>

        <div class="flex items-center space-x-3">
          <button @click="goToRegister" class="px-4 py-2 border border-blue-500 text-blue-500 rounded-md hover:bg-blue-500 hover:text-white transition">Đăng Ký</button>
          <button @click="goToLogin" class="px-4 py-2 bg-primary text-white rounded-md hover:bg-red-700 transition">Đăng Nhập</button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'

const router = useRouter()

const goToRegister = () => router.push('/register')
const goToLogin = () => router.push('/login')
</script>

<!-- No CSS here: styling is provided by Tailwind classes -->
