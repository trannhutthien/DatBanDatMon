<template>
  <div class="input-wrapper">
    <label v-if="label" :for="inputId" class="input-label">
      {{ label }}
      <span v-if="required" class="required">*</span>
    </label>
    <div class="input-container">
      <span v-if="prefixIcon" class="prefix-icon">{{ prefixIcon }}</span>
      <input
        :id="inputId"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :class="[
          'input-field',
          {
            'has-prefix': prefixIcon,
            'has-suffix': suffixIcon,
            'is-error': error,
            'is-disabled': disabled
          }
        ]"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
      />
      <span v-if="suffixIcon" class="suffix-icon">{{ suffixIcon }}</span>
    </div>
    <span v-if="error" class="error-message">{{ error }}</span>
    <span v-else-if="helperText" class="helper-text">{{ helperText }}</span>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

defineOptions({
  name: 'FormInput'
})

interface Props {
  modelValue?: string | number
  type?: string
  label?: string
  placeholder?: string
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  error?: string
  helperText?: string
  prefixIcon?: string
  suffixIcon?: string
  id?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  disabled: false,
  readonly: false,
  required: false
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  blur: [event: FocusEvent]
  focus: [event: FocusEvent]
}>()

const inputId = computed(() => props.id || `input-${Math.random().toString(36).substr(2, 9)}`)

const handleInput = (event: Event) => {
  const target = event.target as HTMLInputElement
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
.input-wrapper {
  display: flex;
  flex-direction: column;
  gap: 6px;
  width: 100%;
}

.input-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #2c3e50;
}

.required {
  color: #e74c3c;
  margin-left: 2px;
}

.input-container {
  position: relative;
  display: flex;
  align-items: center;
}

.input-field {
  width: 100%;
  padding: 12px 16px;
  font-size: 0.95rem;
  border: 2px solid #dfe6e9;
  border-radius: 8px;
  outline: none;
  transition: all 0.3s ease;
  background: #fff;
}

.input-field:focus {
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.input-field:hover:not(:disabled) {
  border-color: #b2bec3;
}

.input-field.has-prefix {
  padding-left: 45px;
}

.input-field.has-suffix {
  padding-right: 45px;
}

.input-field.is-error {
  border-color: #e74c3c;
}

.input-field.is-error:focus {
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.input-field.is-disabled {
  background: #f5f6fa;
  cursor: not-allowed;
  opacity: 0.6;
}

.prefix-icon,
.suffix-icon {
  position: absolute;
  font-size: 1.2rem;
  color: #636e72;
}

.prefix-icon {
  left: 15px;
}

.suffix-icon {
  right: 15px;
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
