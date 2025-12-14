<template>
  <button
    :type="props.type"
    :disabled="props.disabled || props.loading"
    :class="[
      'btn',
      `btn-${props.variant}`,
      `btn-${props.size}`,
      {
        'btn-block': props.block,
        'btn-loading': props.loading,
        'btn-disabled': props.disabled,
        'btn-rounded': props.rounded,
        'btn-icon-only': props.iconOnly,
      },
    ]"
    @click="handleClick"
  >
    <span v-if="props.loading" class="spinner-wrapper">
      <span class="spinner"></span>
    </span>
    <span v-if="props.prefixIcon && !props.loading" class="btn-icon prefix">{{
      props.prefixIcon
    }}</span>
    <span class="btn-text" :class="{ 'sr-only': props.iconOnly }">
      <slot />
    </span>
    <span v-if="props.suffixIcon && !props.loading" class="btn-icon suffix">{{
      props.suffixIcon
    }}</span>
    <span class="btn-shine"></span>
  </button>
</template>

<script setup lang="ts">
// @ts-nocheck
defineOptions({
  name: "FormButton",
});

interface Props {
  type?: "button" | "submit" | "reset";
  variant?:
    | "primary"
    | "secondary"
    | "success"
    | "danger"
    | "warning"
    | "outline"
    | "ghost"
    | "gradient";
  size?: "small" | "medium" | "large";
  block?: boolean;
  disabled?: boolean;
  loading?: boolean;
  rounded?: boolean;
  iconOnly?: boolean;
  prefixIcon?: string;
  suffixIcon?: string;
}

const props = withDefaults(defineProps<Props>(), {
  type: "button",
  variant: "primary",
  size: "medium",
  block: false,
  disabled: false,
  loading: false,
  rounded: false,
  iconOnly: false,
});

const emit = defineEmits<{
  click: [event: MouseEvent];
}>();

const handleClick = (event: MouseEvent) => {
  if (!props.disabled && !props.loading) {
    emit("click", event);
  }
};
</script>

<style scoped>
.btn {
  position: relative;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-weight: 600;
  border-radius: 12px;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: none;
  font-family: inherit;
  white-space: nowrap;
  overflow: hidden;
  isolation: isolate;
  letter-spacing: 0.02em;
}

/* Shine effect */
.btn-shine {
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.2),
    transparent
  );
  transition: left 0.5s ease;
  pointer-events: none;
}

.btn:hover:not(.btn-disabled):not(.btn-loading) .btn-shine {
  left: 100%;
}

/* Hover & Active states */
.btn:hover:not(.btn-disabled):not(.btn-loading) {
  transform: translateY(-3px);
}

.btn:active:not(.btn-disabled):not(.btn-loading) {
  transform: translateY(-1px);
}

/* Focus ring */
.btn:focus-visible {
  outline: none;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.4);
}

/* Sizes */
.btn-small {
  padding: 8px 18px;
  font-size: 0.85rem;
  border-radius: 10px;
}

.btn-medium {
  padding: 12px 28px;
  font-size: 0.95rem;
}

.btn-large {
  padding: 16px 36px;
  font-size: 1.05rem;
  border-radius: 14px;
}

/* Primary */
.btn-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4), 0 1px 3px rgba(0, 0, 0, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.btn-primary:hover:not(.btn-disabled):not(.btn-loading) {
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5), 0 3px 6px rgba(0, 0, 0, 0.15),
    inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

/* Secondary */
.btn-secondary {
  background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
  color: #fff;
  box-shadow: 0 4px 15px rgba(108, 117, 125, 0.35),
    inset 0 1px 0 rgba(255, 255, 255, 0.1);
}

.btn-secondary:hover:not(.btn-disabled):not(.btn-loading) {
  box-shadow: 0 8px 25px rgba(108, 117, 125, 0.45),
    inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

/* Success */
.btn-success {
  background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
  color: #fff;
  box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.btn-success:hover:not(.btn-disabled):not(.btn-loading) {
  box-shadow: 0 8px 25px rgba(17, 153, 142, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

/* Danger */
.btn-danger {
  background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
  color: #fff;
  box-shadow: 0 4px 15px rgba(235, 51, 73, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.15);
}

.btn-danger:hover:not(.btn-disabled):not(.btn-loading) {
  box-shadow: 0 8px 25px rgba(235, 51, 73, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

/* Warning */
.btn-warning {
  background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%);
  color: #1a1a2e;
  box-shadow: 0 4px 15px rgba(247, 151, 30, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
}

.btn-warning:hover:not(.btn-disabled):not(.btn-loading) {
  box-shadow: 0 8px 25px rgba(247, 151, 30, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.25);
}

/* Gradient (special) */
.btn-gradient {
  background: linear-gradient(135deg, #e53935 0%, #ff6f00 50%, #ffc107 100%);
  background-size: 200% 200%;
  color: #fff;
  box-shadow: 0 4px 20px rgba(229, 57, 53, 0.4),
    inset 0 1px 0 rgba(255, 255, 255, 0.2);
  animation: gradient-shift 3s ease infinite;
}

@keyframes gradient-shift {
  0%,
  100% {
    background-position: 0% 50%;
  }
  50% {
    background-position: 100% 50%;
  }
}

.btn-gradient:hover:not(.btn-disabled):not(.btn-loading) {
  box-shadow: 0 8px 30px rgba(229, 57, 53, 0.5),
    inset 0 1px 0 rgba(255, 255, 255, 0.25);
}

/* Outline */
.btn-outline {
  background: transparent;
  color: #667eea;
  border: 2px solid #667eea;
  box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
}

.btn-outline:hover:not(.btn-disabled):not(.btn-loading) {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: #fff;
  border-color: transparent;
  box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
}

/* Ghost */
.btn-ghost {
  background: transparent;
  color: #667eea;
  box-shadow: none;
}

.btn-ghost:hover:not(.btn-disabled):not(.btn-loading) {
  background: rgba(102, 126, 234, 0.1);
  box-shadow: none;
  transform: translateY(0);
}

.btn-ghost:active:not(.btn-disabled):not(.btn-loading) {
  background: rgba(102, 126, 234, 0.15);
}

/* Rounded */
.btn-rounded {
  border-radius: 50px;
}

/* Icon only */
.btn-icon-only.btn-small {
  padding: 8px;
  width: 36px;
  height: 36px;
}

.btn-icon-only.btn-medium {
  padding: 12px;
  width: 44px;
  height: 44px;
}

.btn-icon-only.btn-large {
  padding: 16px;
  width: 52px;
  height: 52px;
}

.sr-only {
  position: absolute;
  width: 1px;
  height: 1px;
  padding: 0;
  margin: -1px;
  overflow: hidden;
  clip: rect(0, 0, 0, 0);
  border: 0;
}

/* Block */
.btn-block {
  width: 100%;
}

/* Disabled */
.btn-disabled {
  opacity: 0.5;
  cursor: not-allowed;
  filter: grayscale(30%);
}

/* Loading */
.btn-loading {
  cursor: wait;
  pointer-events: none;
}

.btn-loading .btn-text,
.btn-loading .btn-icon {
  opacity: 0.7;
}

/* Icon */
.btn-icon {
  font-size: 1.15em;
  display: flex;
  align-items: center;
  flex-shrink: 0;
}

/* Spinner */
.spinner-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}

.spinner {
  width: 18px;
  height: 18px;
  border: 2.5px solid rgba(255, 255, 255, 0.3);
  border-top-color: #fff;
  border-radius: 50%;
  animation: spin 0.7s linear infinite;
}

.btn-outline .spinner,
.btn-ghost .spinner {
  border-color: rgba(102, 126, 234, 0.3);
  border-top-color: #667eea;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Ripple effect on click */
.btn::after {
  content: "";
  position: absolute;
  inset: 0;
  background: radial-gradient(
    circle,
    rgba(255, 255, 255, 0.3) 0%,
    transparent 70%
  );
  opacity: 0;
  transform: scale(0);
  transition: transform 0.5s ease, opacity 0.3s ease;
  pointer-events: none;
}

.btn:active:not(.btn-disabled):not(.btn-loading)::after {
  opacity: 1;
  transform: scale(2);
  transition: transform 0s, opacity 0s;
}
</style>
