import Vue from 'vue'

const { options } = Vue.component('AModal')

if (!options.mixins) {
  options.mixins = []
}

options.mixins.push({
  watch: {
    visible(newVal) {
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
    },
  },
})
