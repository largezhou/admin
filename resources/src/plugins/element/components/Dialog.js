import { Dialog } from 'element-ui'

export default {
  extends: Dialog,
  watch: {
    async visible(newVal) {
      if (newVal) {
        await this.$nextTick()
        const firstFocus = this.$el.querySelector('input[autofocus],textarea[autofocus]')
        firstFocus && firstFocus.focus()
      }
    },
  },
}
