/**
 * 把 el 或者 vnode 替换成 注释元素
 *
 * @link https://stackoverflow.com/questions/43003976/a-custom-directive-similar-to-v-if-in-vuejs#answer-43543814
 *
 * @param el
 * @param vnode
 */
export const replaceWithComment = (el, vnode) => {
  const comment = document.createComment('')
  Object.defineProperty(comment, 'setAttribute', {
    value: () => undefined,
  })
  vnode.elm = comment
  vnode.text = ''
  vnode.isComment = true
  vnode.context = undefined
  vnode.tag = undefined
  vnode.data.directives = undefined

  if (vnode.componentInstance) {
    vnode.componentInstance.$el = comment
  }

  if (el.parentNode) {
    el.parentNode.replaceChild(comment, el)
  }
}
