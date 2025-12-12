import type { Todo } from '@/types/todo'

export const useTodoStore = () => {
  // todolist shared state
  const todolist = useState<Todo[]>('todolist', () => [])

  // formatted date shared state
  const todayDate = useState<string>('today_format_date', () => '')

  return {
    todolist,
    todayDate,
  }
}
