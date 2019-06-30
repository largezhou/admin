import { Dialog } from 'element-ui'

export default {
  extends: Dialog,
  watch: {
    async visible(newVal) {
      if (newVal) {
        await this.$nextTick()
        const firstInput = this.$el.querySelector('input,textarea')
        firstInput && firstInput.focus()
      }
    },
  },
}
