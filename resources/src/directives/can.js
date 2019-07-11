import { replaceWithComment } from './utils'
import { can } from '@/libs/utils'

const handler = (el, { value }, vnode) => {
  if (!can(value)) {
    replaceWithComment(el, vnode)
  }
}

export default {
  inserted: handler,
  update: handler,
}
