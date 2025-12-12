<script setup lang="ts">
  import { destroy } from '@/actions/App/Http/Controllers/TodolistController'
  import CustomList from '@/components/todolist/CustomList.vue'
  import Layout from '@/layouts/Default.vue'
  import { ModalAttrs, Todo } from '@/types/todo'
  import TodoModal from './Todolist/Modal/TodoModal.vue'

  defineOptions({ layout: Layout })
  const toast = useToast()

  const props = defineProps<{
    todolist: Todo[]
    todayFormatDate: string
    dateToday: string
  }>()

  const loading = defineModel<boolean>('open')
  const mytodos = ref<Todo[]>(props.todolist)
  const status = ref<'Pending' | 'Ongoing' | 'Done' | 'All'>('All')

  const statusOptions = [
    { label: 'All', value: 'All' },
    { label: 'Pending', value: 'Pending' },
    { label: 'Ongoing', value: 'Ongoing' },
    { label: 'Done', value: 'Done' },
  ]

  const fetchTodolist = async () => {
    loading.value = true
    mytodos.value = []
    const { data, error: fetchError } = await useFetch<Todo[]>(`/todolist/get_todos?status=${status.value}&date=${props.dateToday}`)
    if (fetchError.value) {
      toast.add({
        title: 'Error',
        description: fetchError.value ?? 'Oops! Something went wrong. Please try again.',
        color: 'error',
      })
      loading.value = false
    } else {
      const raw = data.value
      let todos: Todo[] = []
      if (Array.isArray(raw)) {
        todos = raw
      } else if (raw && typeof raw === 'object') {
        todos = Object.values(raw) as Todo[]
      } else if (raw && typeof raw === 'string') {
        todos = JSON.parse(raw) as Todo[]
      }

      mytodos.value = todos
      loading.value = false
    }
  }

  watch(status, () => {
    fetchTodolist()
  })

  const modalAttrs = ref<ModalAttrs>({
    myTodo: null,
    action: '',
  })

  const isModalOpen = ref(false)
  function openModal(modalId: Todo | null, action: 'ADD' | 'EDIT' | 'DELETE' | 'VIEW') {
    isModalOpen.value = true
    modalAttrs.value.myTodo = modalId
    modalAttrs.value.action = action
  }

  function handleEdit(todo: Todo) {
    console.log('todo', todo)
    openModal(todo, 'EDIT')
  }

  function handleDelete(todo: Todo) {
    if (!confirm(`Are you sure you want to delete "${todo.description}"?`)) return

    deleteTodo(todo.id)
  }

  async function deleteTodo(todoId: number) {
    try {
      const { url } = destroy(todoId)
      router.delete(url, {
        onSuccess: () => {
          toast.add({
            title: 'Success',
            description: 'Task was deleted.',
            color: 'success',
          })
          fetchTodolist()
        },
        onError: (errors) => {
          toast.add({
            title: 'Error',
            description: errors.errorMessage ?? 'Oops! Something went wrong. Please try again.',
            color: 'error',
          })
        },
      })
    } catch (err) {
      toast.add({
        title: 'Error',
        description: 'Something went wrong.',
        color: 'error',
      })
    }
  }
</script>

<template>
  <UDashboardPanel id="home">
    <template #header>
      <UDashboardNavbar title="Home" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse as="button" :disabled="false" />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <span>Today’s tasks: {{ todayFormatDate }}</span>
        </template>
        <template #right>
          <USelect class="min-w-48" v-model="status" :items="statusOptions" placeholder="Select status" teleport-to="body" searchable />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <CustomList class="min-w-full" :todolist="mytodos" @edit-task="handleEdit" @delete-task="handleDelete" />
      <TodoModal v-model:open="isModalOpen" :modalAttrs="modalAttrs" :dateToday="dateToday" @refresh-todos="fetchTodolist" />
      <p v-show="loading" class="text-center text-gray-500">Loading...</p>
      <p v-show="!loading" v-if="mytodos.length === 0" class="text-center text-gray-500">No tasks available</p>
    </template>
  </UDashboardPanel>
</template>
