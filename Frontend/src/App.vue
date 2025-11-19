<script setup lang="ts">
import { RouterView } from 'vue-router'
import Header from './components/layouts/Header.vue'
</script>

<template>
  <div class="app">
    <Header />
    <RouterView />
  </div>
</template>

<style scoped>
.app {
  min-height: 100vh;
}
</style>
