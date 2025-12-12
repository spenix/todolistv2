export function useDebounce(fn: Function, delay = 500) {
  let timeout: any

  return (...args: any[]) => {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn(...args), delay)
  }
}
