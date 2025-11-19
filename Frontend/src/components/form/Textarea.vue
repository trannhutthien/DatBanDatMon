<template>
  <div class="textarea-wrapper">
    <label v-if="label" :for="textareaId" class="textarea-label">
      {{ label }}
      <span v-if="required" class="required">*</span>
    </label>
    <div class="textarea-container">
      <textarea
        :id="textareaId"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :readonly="readonly"
        :required="required"
        :rows="rows"
        :maxlength="maxLength"
        :class="[
          'textarea-field',
          {
            'is-error': error,
            'is-disabled': disabled
          }
        ]"
        @input="handleInput"
        @blur="handleBlur"
        @focus="handleFocus"
      ></textarea>
    </div>
    <div class="textarea-footer">
      <span v-if="error" class="error-message">{{ error }}</span>
      <span v-else-if="helperText" class="helper-text">{{ helperText }}</span>
      <span v-if="maxLength && showCount" class="char-count">
        {{ (modelValue || '').length }} / {{ maxLength }}
      </span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'

defineOptions({
  name: 'FormTextarea'
})

interface Props {
  modelValue?: string
  label?: string
  placeholder?: string
  disabled?: boolean
  readonly?: boolean
  required?: boolean
  error?: string
  helperText?: string
  rows?: number
  maxLength?: number
  showCount?: boolean
  id?: string
}

const props = withDefaults(defineProps<Props>(), {
  disabled: false,
  readonly: false,
  required: false,
  rows: 4,
  showCount: true
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  blur: [event: FocusEvent]
  focus: [event: FocusEvent]
}>()

const textareaId = computed(() => props.id || `textarea-${Math.random().toString(36).substr(2, 9)}`)

const handleInput = (event: Event) => {
  const target = event.target as HTMLTextAreaElement
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
.textarea-wrapper {
  display: flex;
  flex-direction: column;
  gap: 6px;
  width: 100%;
}

.textarea-label {
  font-size: 0.9rem;
  font-weight: 600;
  color: #2c3e50;
}

.required {
  color: #e74c3c;
  margin-left: 2px;
}

.textarea-container {
  position: relative;
}

.textarea-field {
  width: 100%;
  padding: 12px 16px;
  font-size: 0.95rem;
  border: 2px solid #dfe6e9;
  border-radius: 8px;
  outline: none;
  transition: all 0.3s ease;
  background: #fff;
  font-family: inherit;
  resize: vertical;
  min-height: 80px;
}

.textarea-field:focus {
  border-color: #3498db;
  box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.textarea-field:hover:not(:disabled) {
  border-color: #b2bec3;
}

.textarea-field.is-error {
  border-color: #e74c3c;
}

.textarea-field.is-error:focus {
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.textarea-field.is-disabled {
  background: #f5f6fa;
  cursor: not-allowed;
  opacity: 0.6;
}

.textarea-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 10px;
}

.error-message {
  font-size: 0.85rem;
  color: #e74c3c;
  flex: 1;
}

.helper-text {
  font-size: 0.85rem;
  color: #636e72;
  flex: 1;
}

.char-count {
  font-size: 0.85rem;
  color: #95a5a6;
  white-space: nowrap;
}
</style>
