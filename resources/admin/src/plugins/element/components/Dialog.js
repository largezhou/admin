import { Dialog } from 'element-ui'

export default {
  extends: Dialog,
  props: {
    /**
     * 小屏幕下的宽度，在小屏幕下，会覆盖 width 的宽度
     */
    miniWidth: {
      type: String,
      default: '90%',
    },
  },
  computed: {
    style() {
      let style = {}
      if (!this.fullscreen) {
        style.marginTop = this.top
        if (this.width) {
          style.width = this.width
        }
        if (this.miniWidth && this.$store.state.miniWidth) {
          style.width = this.miniWidth
        }
      }
      return style
    },
  },
  methods: {
    afterEnter() {
      this.$emit('opened')

      // 聚焦第一个有 autofocus 的组件
      const firstFocus = this.$el.querySelector('[autofocus]')
      firstFocus && firstFocus.focus()
    },
  },
}
