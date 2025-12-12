<script setup lang="ts">
  import { store, update } from '@/actions/App/Http/Controllers/TodolistController'
  import type { ModalAttrs } from '@/types/todo'
  import type { FormSubmitEvent } from '@nuxt/ui'
  import { reactive, watch } from 'vue'
  import * as z from 'zod'

  const open = defineModel<boolean>('open')

  const props = defineProps<{
    modalAttrs: ModalAttrs
    dateToday: string
  }>()

  // Zod Schema
  const schema = z.object({
    id: z.number().nullable(),
    description: z.string().min(3, 'Too short'),
    task_date: z.string().min(1, 'Required'),
    priority: z.enum(['Low', 'Medium', 'High', 'All']),
    status: z.enum(['Pending', 'Ongoing', 'Done', 'All']),
  })

  type Schema = z.output<typeof schema>

  // State
  const state = reactive<Partial<Schema>>({
    id: null,
    description: '',
    task_date: props.dateToday,
    priority: 'Low',
    status: 'Pending',
  })

  // Nuxt UI Form
  const form = useForm<Partial<Schema>>({
    id: null,
    description: '',
    task_date: props.dateToday,
    priority: 'Low',
    status: 'Pending',
  })

  // Reset
  const resetForm = () => {
    form.reset()
    form.clearErrors()

    Object.assign(state, {
      id: null,
      description: '',
      task_date: props.dateToday,
      priority: 'Low',
      status: 'Pending',
    })
  }

  const toast = useToast()

  const emit = defineEmits<{
    (e: 'refresh-todos'): void
  }>()

  async function onSubmit(event: FormSubmitEvent<Schema>) {
    form.description = event.data.description
    form.task_date = event.data.task_date
    form.priority = event.data.priority
    form.status = event.data.status
    if (props.modalAttrs.action === 'ADD') {
      form.submit(store(), {
        onSuccess: () => {
          toast.add({
            title: 'Success',
            description: 'New task added',
            color: 'success',
          })
          resetForm()
          open.value = false
          emit('refresh-todos')
        },
        onError: (errors) => {
          toast.add({
            title: 'Error',
            description: errors?.errorMessage ?? 'Oops! Something went wrong. Please try again.',
            color: 'error',
          })
        },
      })
    } else {
      const todoId: number = event.data.id ?? 0
      form.submit(update(todoId), {
        onSuccess: () => {
          toast.add({
            title: 'Success',
            description: 'Task was updated.',
            color: 'success',
          })
          resetForm()
          open.value = false
          emit('refresh-todos')
        },
        onError: (errors) => {
          toast.add({
            title: 'Error',
            description: errors?.errorMessage ?? 'Oops! Something went wrong. Please try again.',
            color: 'error',
          })
        },
      })
    }
  }

  // Select List Options
  const priorityOptions = [
    { label: 'Low', value: 'Low' },
    { label: 'Medium', value: 'Medium' },
    { label: 'High', value: 'High' },
  ]

  const statusOptions = [
    { label: 'Pending', value: 'Pending' },
    { label: 'Ongoing', value: 'Ongoing' },
    { label: 'Done', value: 'Done' },
  ]

  // OPEN modal when parent triggers
  watch(
    () => props.modalAttrs.action,
    () => {
      if (props.modalAttrs.action === 'ADD') {
        resetForm()
      }
      open.value = true
    },
  )

  function ucwords(str: string): string {
    return str
      .split(' ')
      .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
      .join(' ')
  }

  const modalTitle = computed(() => {
    if (props.modalAttrs.action !== 'ADD') {
      state.id = props.modalAttrs.myTodo?.id
      state.description = props.modalAttrs.myTodo?.description
      state.task_date = props.modalAttrs.myTodo?.task_date
      state.priority = props.modalAttrs.myTodo?.priority
      state.status = props.modalAttrs.myTodo?.status
    }
    return ucwords(props.modalAttrs.action ? `${props.modalAttrs.action} TASK` : 'TASK')
  })
</script>

<template>
  <!-- Modal -->
  <UModal v-model:open="open" :title="modalTitle">
    <template #body>
      <UForm :schema="schema" :state="state" class="space-y-4" @submit="onSubmit">
        <!-- Description -->
        <UFormField label="Description" name="description">
          <UTextarea class="min-w-full" v-model="state.description" placeholder="Enter task description..." :rows="3" />
        </UFormField>

        <!-- Task Date -->
        <UFormField label="Task Date" name="task_date">
          <UInput type="date" v-model="state.task_date" />
        </UFormField>

        <!-- Priority Select -->
        <UFormField label="Priority" name="priority">
          <USelect class="min-w-48" v-model="state.priority" :items="priorityOptions" placeholder="Select priority" teleport-to="body" searchable />
        </UFormField>

        <!-- Status Select -->
        <UFormField label="Status" name="status">
          <USelect class="min-w-48" v-model="state.status" :items="statusOptions" placeholder="Select status" teleport-to="body" searchable />
        </UFormField>

        <!-- Actions -->
        <div class="flex justify-end gap-2 pt-2">
          <UButton label="Cancel" color="neutral" variant="subtle" @click="open = false" />
          <UButton :label="ucwords(modalAttrs.action)" color="primary" type="submit" />
        </div>
      </UForm>
    </template>
  </UModal>
</template>
