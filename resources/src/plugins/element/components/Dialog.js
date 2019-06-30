import { Dialog } from 'element-ui'

export default {
  extends: Dialog,
  props: {
    autoFocus: {
      type: Boolean,
      default: true,
    },
  },
  watch: {
    async visible(newVal) {
      if (this.autoFocus && newVal) {
        await this.$nextTick()
        const firstInput = this.$el.querySelector('input,textarea')
        firstInput && firstInput.focus()
      }
    },
  },
}
