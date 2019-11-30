import { roleIn } from '@/libs/utils'

export default {
  inserted(el, { value, modifiers }) {
    const hasRole = roleIn(value)
    if (
      (modifiers.not && hasRole) ||
      (!modifiers.not && !hasRole)
    ) {
      el.parentNode && el.parentNode.removeChild(el)
    }
  },
}
