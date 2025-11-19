<template>
  <div class="select-wrapper">
    <label v-if="label" :for="selectId" class="select-label">
      {{ label }}
      <span v-if="required" class="required">*</span>
    </label>
    <div class="select-container">
      <select
        :id="selectId"
        :value="modelValue"
        :disabled="disabled"
        :required="required"
        :class="[
          'select-field',
          {
            'is-error': error,
            'is-disabled': disabled
          }
        ]"
        @change="handleChange"
        @blur="handleBlur"
        @focus="handleFocus"
      >
        <option v-if="placeholder" value="" disabled>{{ placeholder }}</option>
        <option
          v-for="option in options"
          :key="option.value"
          :value="option.value"
        >
          {{ option.label }}
        </option>
      </select>
      <span class="select-arrow">▼</span>
    </div>
    <span v-if="error" class="error-message">{{ error }}</span>
    <span v-else-if="helperText" class="helper-text">{{ helperText }}</span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

defineOptions({
  name: 'FormSelect'
})

interface Option {
  label: string
  value: string | number
}

interface Props {
  modelValue?: string | number
  label?: string
  placeholder?: string
  disabled?: boolean
  required?: boolean
  error?: string
  helperText?: string
  options: Option[]
  id?: string
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
  required: false
})

const emit = defineEmits<{
  'update:modelValue': [value: string | number]
  blur: [event: FocusEvent]
  focus: [event: FocusEvent]
}>()

const selectId = computed(() => props.id || `select-${Math.random().toString(36).substr(2, 9)}`)

const handleChange = (event: Event) => {
  const target = event.target as HTMLSelectElement
  emit('update:modelValue', target.value)
}

const handleBlur = (event: FocusEvent) => {
  emit('blur', event)
}

const handleFocus = (event: FocusEvent) => {
  emit('focus', event)
}
</script>

<style scoped>
.select-wrapper {
  display: flex;
  flex-direction: column;
  gap: 6px;
  width: 100%;
}

.select-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #2c3e50;
}

.required {
  color: #e74c3c;
  margin-left: 2px;
}

.select-container {
  position: relative;
}

.select-field {
  width: 100%;
  padding: 12px 40px 12px 16px;
  font-size: 0.95rem;
  border: 2px solid #dfe6e9;
  border-radius: 8px;
  outline: none;
  transition: all 0.3s ease;
  background: #fff;
  cursor: pointer;
  appearance: none;
  -webkit-appearance: none;
  -moz-appearance: none;
}

.select-field:focus {
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.select-field:hover:not(:disabled) {
  border-color: #b2bec3;
}

.select-field.is-error {
  border-color: #e74c3c;
}

.select-field.is-error:focus {
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.select-field.is-disabled {
  background: #f5f6fa;
  cursor: not-allowed;
  opacity: 0.6;
}

.select-arrow {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 0.7rem;
  color: #636e72;
  pointer-events: none;
}

.error-message {
  font-size: 0.85rem;
  color: #e74c3c;
}

.helper-text {
  font-size: 0.85rem;
  color: #636e72;
}
</style>
