export interface Todo {
  id: number
  description: string
  task_date: string
  priority: 'Low' | 'Medium' | 'High' | 'All'
  status: 'Pending' | 'Ongoing' | 'Done' | 'All'
}

export interface Option {
  label: string
  value: string | number
}

export interface ModalAttrs {
  myTodo: Todo | null
  action: 'ADD' | 'EDIT' | 'DELETE' | 'VIEW' | ''
}
