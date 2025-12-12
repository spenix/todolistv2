<script setup lang="ts">
  import { defineEmits, ref, watch } from 'vue'

  const emit = defineEmits(['update:query', 'search'])

  const query = ref('')

  watch(query, (val) => {
    emit('update:query', val) // live two-way binding
  })

  const handleKeydown = (e: KeyboardEvent) => {
    if (e.key === 'Enter') emit('search', query.value)
  }
</script>

<template>
  <div class="relative w-full">
    <!-- Search Icon -->
    <svg
      class="pointer-events-none absolute top-1/2 left-3 h-5 w-5 -translate-y-1/2 text-gray-400"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35m1.1-5.4a6.5 6.5 0 11-13 0 6.5 6.5 0 0113 0z" />
    </svg>

    <!-- Search Input -->
    <input
      v-model="query"
      @keydown="handleKeydown"
      type="text"
      placeholder="Search..."
      class="w-full rounded-lg border px-10 py-2 placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none"
    />
  </div>
</template>
