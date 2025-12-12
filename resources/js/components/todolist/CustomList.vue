<script setup lang="ts">
  import { Todo } from '@/types/todo'
  import ListItem from './ListItem.vue'

  const props = defineProps<{
    todolist: Todo[]
  }>()

  const emit = defineEmits<{
    (e: 'edit-task', todo: Todo): void
    (e: 'delete-task', todo: Todo): void
  }>()

  function onEdit(todo: Todo) {
    emit('edit-task', todo)
  }

  function onDelete(todo: Todo) {
    emit('delete-task', todo)
  }
</script>

<template>
  <div class="mx-auto mt-6 max-w-xl">
    <ul>
      <ListItem v-for="(item, index) in todolist" :key="index" :todo="item" @edit="onEdit" @delete="onDelete" />
    </ul>
  </div>
</template>
