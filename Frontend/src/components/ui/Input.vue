<template>
  <input
    :type="type"
    :value="modelValue"
    @input="handleInput"
    :placeholder="placeholder"
    :disabled="disabled"
    :class="['input', `input-${variant}`, { 'input-disabled': disabled }]"
  />
</template>

<script setup lang="ts">
/**
 * ============================================================================
 * INPUT COMPONENT
 * ============================================================================
 * 
 * Component input tái sử dụng với các variant khác nhau.
 * 
 * PROPS:
 * - modelValue: Giá trị input (v-model)
 * - type: Loại input (text, email, password, number...)
 * - placeholder: Placeholder text
 * - variant: Style variant (default, search)
 * - disabled: Vô hiệu hóa input
 * 
 * EVENTS:
 * - update:modelValue: Emit khi giá trị thay đổi (cho v-model)
 * ============================================================================
 */

interface Props {
  modelValue?: string | number
  type?: string
  placeholder?: string
  variant?: 'default' | 'search'
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  placeholder: '',
  variant: 'default',
  disabled: false
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
}>()

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement
  emit('update:modelValue', target.value)
}
</script>

<style scoped>
.input {
  width: 100%;
  padding: 12px 16px;
  border: 2px solid #e0e0e0;
  border-radius: 10px;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  font-family: inherit;
}

.input:focus {
  outline: none;
  border-color: #667eea;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input::placeholder {
  color: #95a5a6;
}

/* Variant: Search */
.input-search {
  padding-left: 40px;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='%23667eea' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Ccircle cx='11' cy='11' r='8'%3E%3C/circle%3E%3Cpath d='m21 21-4.35-4.35'%3E%3C/path%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: 12px center;
  background-size: 18px;
}

/* Disabled State */
.input-disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
  opacity: 0.6;
}

.input-disabled:focus {
  border-color: #e0e0e0;
  box-shadow: none;
}
</style>
