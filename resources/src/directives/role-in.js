import { replaceWithComment } from './utils'
import { roleIn } from '@/libs/utils'

const handler = (el, { value }, vnode) => {
  if (!roleIn(value)) {
    replaceWithComment(el, vnode)
  }
}

export default {
  inserted: handler,
  update: handler,
}
