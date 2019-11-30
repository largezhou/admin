function directive(e, el, binding) {
  const inEl = e.path.some((i) => i === el)
  !inEl && (typeof binding.value === 'function') && binding.value(e)
}

export default {
  inserted(el, binding) {
    const onClick = (e) => directive(e, el, binding)
    const app = document.querySelector('#admin-app')
    app.addEventListener('click', onClick, true)
    el._clickOutside = onClick
  },
  unbind(el) {
    if (!el._clickOutside) {
      return
    }

    const app = document.querySelector('#admin-app')
    app && app.removeEventListener('click', el._clickOutside, true)
    delete el._clickOutside
  },
}
