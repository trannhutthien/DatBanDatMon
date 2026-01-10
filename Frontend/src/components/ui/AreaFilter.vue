<template>
  <div class="area-filter">
    <h3 class="filter-title">📍 Khu vực</h3>
    <div class="filter-items">
      <button
        @click="selectArea(null)"
        :class="['filter-btn', { active: selectedArea === null }]"
      >
        Tất cả
      </button>
      <button
        v-for="area in areas"
        :key="area.KhuVucID"
        @click="selectArea(area)"
        :class="['filter-btn', { active: selectedArea?.KhuVucID === area.KhuVucID }]"
      >
        {{ area.TenKhuVuc }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { KhuVuc } from '@/services/ban.service'

defineProps<{
  areas: KhuVuc[]
  selectedArea: KhuVuc | null
}>()

const emit = defineEmits<{
  'update:selectedArea': [area: KhuVuc | null]
}>()

const selectArea = (area: KhuVuc | null) => {
  emit('update:selectedArea', area)
}
</script>

<style scoped>
.area-filter {
  background: white;
  border-radius: 16px;
  padding: 20px;
  box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
  margin-bottom: 30px;
}

.filter-title {
  font-size: 1.1rem;
  margin-bottom: 15px;
  color: #2c3e50;
}

.filter-items {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
}

.filter-btn {
  padding: 10px 20px;
  background: #f8f9fa;
  border: 2px solid transparent;
  border-radius: 25px;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  color: #495057;
  font-family: inherit;
  font-size: 0.95rem;
}

.filter-btn:hover {
  background: #e9ecef;
}

.filter-btn.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: transparent;
}
</style>
