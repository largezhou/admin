import { can } from '@/libs/utils'

export default {
  inserted(el, { value, modifiers }) {
    const hasPerms = can(value)
    if (
      (modifiers.not && hasPerms) ||
      (!modifiers.not && !hasPerms)
    ) {
      el.parentNode && el.parentNode.removeChild(el)
    }
  },
}
