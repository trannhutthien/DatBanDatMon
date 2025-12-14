<template>
  <div class="list-option">
    <h3 v-if="title" class="list-option-title">{{ title }}</h3>
    <div class="list-option-items">
      <button
        v-for="option in options"
        :key="getOptionValue(option)"
        @click="selectOption(option)"
        :class="['option-btn', { active: isActive(option) }]"
      >
        {{ getOptionLabel(option) }}
      </button>
    </div>
  </div>
</template>

<script setup lang="ts">
/**
 * ============================================================================
 * LIST OPTION COMPONENT
 * ============================================================================
 * 
 * Component danh sách tùy chọn tái sử dụng (dạng button list).
 * Hỗ trợ cả mảng string đơn giản và mảng object.
 * 
 * PROPS:
 * - modelValue: Giá trị được chọn (v-model)
 * - options: Mảng các tùy chọn (string[] hoặc object[])
 * - title: Tiêu đề của danh sách (optional)
 * - labelKey: Key để lấy label khi options là object[] (mặc định: 'label')
 * - valueKey: Key để lấy value khi options là object[] (mặc định: 'value')
 * 
 * EVENTS:
 * - update:modelValue: Emit khi giá trị thay đổi (cho v-model)
 * ============================================================================
 */

type OptionItem = string | Record<string, any>

interface Props {
  modelValue?: OptionItem
  options: OptionItem[]
  title?: string
  labelKey?: string
  valueKey?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: '',
  labelKey: 'label',
  valueKey: 'value'
})

const emit = defineEmits<{
  'update:modelValue': [value: OptionItem]
}>()

/**
 * Lấy giá trị hiển thị của option
 */
const getOptionLabel = (option: OptionItem): string => {
  if (typeof option === 'string') {
    return option
  }
  return option[props.labelKey] || ''
}

/**
 * Lấy giá trị để so sánh của option
 */
const getOptionValue = (option: OptionItem): string | number => {
  if (typeof option === 'string') {
    return option
  }
  return option[props.valueKey] ?? option[props.labelKey] ?? ''
}

/**
 * Kiểm tra option có đang được chọn không
 */
const isActive = (option: OptionItem): boolean => {
  if (typeof option === 'string' && typeof props.modelValue === 'string') {
    return option === props.modelValue
  }
  if (typeof option === 'object' && typeof props.modelValue === 'object') {
    return getOptionValue(option) === getOptionValue(props.modelValue)
  }
  return false
}

/**
 * Xử lý khi chọn option
 */
const selectOption = (option: OptionItem) => {
  emit('update:modelValue', option)
}
</script>

<style scoped>
.list-option {
  width: 100%;
}

.list-option-title {
  font-size: 1.1rem;
  margin-bottom: 15px;
  color: #2c3e50;
}

.list-option-items {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.option-btn {
  padding: 10px 16px;
  background: #f8f9fa;
  border: 2px solid transparent;
  border-radius: 10px;
  text-align: left;
  cursor: pointer;
  transition: all 0.3s ease;
  font-weight: 500;
  color: #495057;
  font-family: inherit;
  font-size: 0.95rem;
}

.option-btn:hover {
  background: #e9ecef;
}

.option-btn.active {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-color: transparent;
}
</style>

