import Vue from 'vue'

const Modal = Vue.component('AModal')
const oldWatchVisible = Modal.options.watch.visible
Modal.options.watch.visible = function (newVal) {
  // 保留原来的监视器的作用
  oldWatchVisible && oldWatchVisible.bind(this)(...arguments)

  if (newVal) {
    // 打开弹框时，自动聚焦第一个有 focus 属性的元素
    this.$nextTick(() => {
      const defaultSlots = this.$scopedSlots.default
        ? this.$scopedSlots.default()
        : this.$slots.default

      defaultSlots.some((i) => {
        const focusEl = i.elm.querySelector('[focus]')
        focusEl && focusEl.focus()
        return !!focusEl
      })
    })
  }
}
