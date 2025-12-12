<template>
  <div class="relative w-64" ref="root">
    <!-- Trigger Button -->
    <button
      @click="toggle"
      class="flex w-full items-center justify-between rounded-xl border bg-white px-4 py-2 shadow-sm transition-all duration-150 hover:shadow focus:ring-2 focus:ring-indigo-500"
    >
      <span class="truncate text-gray-700">
        {{ selectedLabel || placeholder }}
      </span>

      <svg class="h-5 w-5 text-gray-500 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Panel -->
    <teleport to="body">
      <transition name="fade-scale">
        <ul v-if="open" class="absolute z-50 mt-2 max-h-60 w-full overflow-auto rounded-xl border bg-white py-1 shadow-lg" :style="panelPosition">
          <li
            v-for="option in options"
            :key="option.value"
            @click="selectOption(option.value)"
            class="flex cursor-pointer items-center justify-between px-4 py-2 hover:bg-indigo-50"
          >
            <span class="text-gray-700">{{ option.label }}</span>

            <svg v-if="option.value === modelValue" class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </li>

          <li v-if="options.length === 0" class="px-4 py-2 text-sm text-gray-500">No options</li>
        </ul>
      </transition>
    </teleport>
  </div>
</template>

<script setup lang="ts">
  import type { Option } from '@/types/todo'
  import { computed, onBeforeUnmount, onMounted, ref } from 'vue'

  const props = defineProps<{
    modelValue: string | number | null | undefined
    options: Option[]
    placeholder?: string
  }>()

  const emit = defineEmits(['update:modelValue'])

  const open = ref(false)
  const root = ref<HTMLElement | null>(null)

  const placeholder = computed(() => props.placeholder ?? 'Select')

  const panelPosition = computed(() => {
    if (!root.value) return {}

    const rect = root.value.getBoundingClientRect()

    return {
      position: 'absolute',
      top: rect.bottom + 'px',
      left: rect.left + 'px',
      width: rect.width + 'px',
    }
  })

  const selectedLabel = computed(() => {
    return props.options.find((o) => o.value === props.modelValue)?.label || null
  })

  function toggle() {
    open.value = !open.value
  }

  function selectOption(value: string | number) {
    emit('update:modelValue', value)
    open.value = false
  }

  // Close dropdown when clicking outside
  function handleClickOutside(e: MouseEvent) {
    if (!root.value) return
    if (!root.value.contains(e.target as Node)) open.value = false
  }

  onMounted(() => document.addEventListener('click', handleClickOutside))
  onBeforeUnmount(() => document.removeEventListener('click', handleClickOutside))
</script>

<style scoped>
  .fade-scale-enter-active {
    transition: all 150ms ease;
  }
  .fade-scale-leave-active {
    transition: all 120ms ease;
  }
  .fade-scale-enter-from {
    opacity: 0;
    transform: translateY(-6px) scale(0.98);
  }
  .fade-scale-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  .fade-scale-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
  }
  .fade-scale-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.98);
  }
</style>
