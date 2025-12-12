<script setup lang="ts">
  import { destroy } from '@/actions/App/Http/Controllers/TodolistController'
  import CustomDropdown from '@/components/todolist/CustomDropdown.vue'
  import CustomList from '@/components/todolist/CustomList.vue'
  import CustomSearch from '@/components/todolist/CustomSearch.vue'
  import Layout from '@/layouts/Default.vue'
  import { ModalAttrs, Option, Todo } from '@/types/todo'
  import TodoModal from './Modal/TodoModal.vue'
  defineOptions({ layout: Layout })

  const page = usePage()
  const toast = useToast()

  const props = defineProps<{
    todoDates: Option[]
    dateToday: string
  }>()
  const loading = defineModel<boolean>('open')
  const selected = ref<number | string | null | undefined>(null)
  selected.value = props?.dateToday

  const searchQuery = ref('')

  function handleSearch(q: string) {
    fetchTodolist()
  }

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

  const todolist = ref<Todo[]>([])

  const fetchTodolist = async () => {
    loading.value = true
    todolist.value = []
    const { data, error: fetchError } = await useFetch<Todo[]>(`${page?.url}/get_todos?search=${searchQuery.value}&date=${selected.value}`)
    if (fetchError.value) {
      toast.add({
        title: 'Error',
        description: fetchError.value ?? 'Oops! Something went wrong. Please try again.',
        color: 'error',
      })
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

      todolist.value = todos
    }
    loading.value = false
  }

  watch([searchQuery, selected], () => {
    fetchTodolist()
  })

  fetchTodolist()

  function handleEdit(todo: Todo) {
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
  <UDashboardPanel id="todolist">
    <template #header>
      <UDashboardNavbar title="Todo List" :ui="{ right: 'gap-3' }">
        <template #leading>
          <UDashboardSidebarCollapse as="button" :disabled="false" />
        </template>
      </UDashboardNavbar>

      <UDashboardToolbar>
        <template #left>
          <div class="grid grid-cols-3 gap-1">
            <div class="col-span-1">
              <CustomDropdown class="min-w-full" v-model="selected" :options="todoDates" placeholder="Select Date" />
            </div>
            <div class="col-span-2"><CustomSearch class="min-w-full" v-model:query="searchQuery" @search="handleSearch" /></div>
          </div>
        </template>
        <template #right>
          <UButton label="Add Task" icon="i-lucide-plus" color="primary" @click="openModal(null, 'ADD')" />
          <TodoModal v-model:open="isModalOpen" :modalAttrs="modalAttrs" :dateToday="dateToday" @refresh-todos="fetchTodolist" />
        </template>
      </UDashboardToolbar>
    </template>

    <template #body>
      <CustomList class="min-w-full" :todolist="todolist" @edit-task="handleEdit" @delete-task="handleDelete" />
      <p v-show="loading" class="text-center text-gray-500">Loading...</p>
      <p v-show="!loading" v-if="todolist.length === 0" class="text-center text-gray-500">No tasks available</p>
    </template>
  </UDashboardPanel>
</template>
