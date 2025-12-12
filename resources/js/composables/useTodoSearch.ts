export function useTodoSearch() {
  const results = ref([])
  const loading = ref(false)
  const error = ref(null)

  async function searchTodos(query: string) {
    loading.value = true
    error.value = null

    const { data, error: fetchError } = await useFetch('/api/todos', {
      query: { search: query },
      method: 'GET',
    })

    if (fetchError.value) {
      error.value = fetchError.value
    } else {
      results.value = data.value || []
    }

    loading.value = false
  }

  return {
    results,
    loading,
    error,
    searchTodos,
  }
}
