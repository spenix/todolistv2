<script setup lang="ts">
  import { Todo } from '@/types/todo'

  const props = defineProps<{
    todo: Todo
  }>()

  const statusColor: Record<string, string> = {
    Ongoing: 'bg-yellow-100 text-yellow-800',
    Done: 'bg-green-100 text-green-800',
    Pending: 'bg-red-100 text-red-800',
  }
  // Emits
  const emit = defineEmits<{
    (e: 'edit', todo: Todo): void
    (e: 'delete', todo: Todo): void
  }>()

  function handleEdit() {
    emit('edit', props.todo)
  }

  function handleDelete() {
    emit('delete', props.todo)
  }
</script>
<template>
  <li class="mb-2 flex items-center justify-between rounded-lg bg-white p-4 shadow-sm transition-shadow hover:shadow-md dark:bg-gray-800">
    <div class="flex flex-col">
      <span class="font-semibold text-gray-800 dark:text-gray-200"
        >{{ todo?.description }}
        <span :class="['ml-4 rounded-full px-3 py-1 text-sm font-medium', statusColor[todo?.status] || 'bg-gray-200 text-gray-800']">
          {{ todo?.status }}
        </span></span
      >
      <span class="text-sm text-gray-500 dark:text-gray-400">{{ todo.priority }}</span>
    </div>

    <div class="ml-4 flex gap-2">
      <UButton icon="i-lucide-pencil" size="sm" color="primary" title="Edit Task" variant="outline" @click="handleEdit" />
      <UButton icon="i-lucide-trash" size="sm" color="error" title="Delete Task" variant="outline" @click="handleDelete" />
    </div>
  </li>
</template>
