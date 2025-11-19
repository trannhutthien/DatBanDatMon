<template>
  <button
    :type="type"
    :disabled="disabled || loading"
    :class="[
      'btn',
      `btn-${variant}`,
      `btn-${size}`,
      {
        'btn-block': block,
        'btn-loading': loading,
        'btn-disabled': disabled
      }
    ]"
    @click="handleClick"
  >
    <span v-if="loading" class="spinner"></span>
    <span v-if="prefixIcon && !loading" class="btn-icon prefix">{{ prefixIcon }}</span>
    <span class="btn-text">
      <slot />
    </span>
    <span v-if="suffixIcon && !loading" class="btn-icon suffix">{{ suffixIcon }}</span>
  </button>
</template>

<script setup lang="ts">
defineOptions({
  name: 'FormButton'
})

interface Props {
  type?: 'button' | 'submit' | 'reset'
  variant?: 'primary' | 'secondary' | 'success' | 'danger' | 'warning' | 'outline'
  size?: 'small' | 'medium' | 'large'
  block?: boolean
  disabled?: boolean
  loading?: boolean
  prefixIcon?: string
  suffixIcon?: string
}

const props = withDefaults(defineProps<Props>(), {
  type: 'button',
  variant: 'primary',
  size: 'medium',
  block: false,
  disabled: false,
  loading: false
})

const emit = defineEmits<{
  click: [event: MouseEvent]
}>()

const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    emit('click', event)
  }
}
</script>

<style scoped>
.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  font-weight: 600;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 2px solid transparent;
  font-family: inherit;
  white-space: nowrap;
}

.btn:hover:not(.btn-disabled):not(.btn-loading) {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn:active:not(.btn-disabled):not(.btn-loading) {
  transform: translateY(0);
}

/* Sizes */
.btn-small {
  padding: 8px 16px;
  font-size: 0.85rem;
}

.btn-medium {
  padding: 12px 24px;
  font-size: 0.95rem;
}

.btn-large {
  padding: 14px 32px;
  font-size: 1.05rem;
}

/* Variants */
.btn-primary {
  background: #3498db;
  color: #fff;
  border-color: #3498db;
}

.btn-primary:hover:not(.btn-disabled):not(.btn-loading) {
  background: #2980b9;
  border-color: #2980b9;
}

.btn-secondary {
  background: #95a5a6;
  color: #fff;
  border-color: #95a5a6;
}

.btn-secondary:hover:not(.btn-disabled):not(.btn-loading) {
  background: #7f8c8d;
  border-color: #7f8c8d;
}

.btn-success {
  background: #2ecc71;
  color: #fff;
  border-color: #2ecc71;
}

.btn-success:hover:not(.btn-disabled):not(.btn-loading) {
  background: #27ae60;
  border-color: #27ae60;
}

.btn-danger {
  background: #e74c3c;
  color: #fff;
  border-color: #e74c3c;
}

.btn-danger:hover:not(.btn-disabled):not(.btn-loading) {
  background: #c0392b;
  border-color: #c0392b;
}

.btn-warning {
  background: #f39c12;
  color: #fff;
  border-color: #f39c12;
}

.btn-warning:hover:not(.btn-disabled):not(.btn-loading) {
  background: #e67e22;
  border-color: #e67e22;
}

.btn-outline {
  background: transparent;
  color: #3498db;
  border-color: #3498db;
}

.btn-outline:hover:not(.btn-disabled):not(.btn-loading) {
  background: #3498db;
  color: #fff;
}

/* States */
.btn-block {
  width: 100%;
}

.btn-disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-loading {
  cursor: wait;
  opacity: 0.8;
}

/* Icon */
.btn-icon {
  font-size: 1.2em;
  display: flex;
  align-items: center;
}

/* Spinner */
.spinner {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.6s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
