import { Switch } from 'element-ui'

export default {
  extends: Switch,
  mounted() {
    this.$el.addEventListener('click', this.focus)
  },
  beforeDestroy() {
    this.$el.removeEventListener('click', this.focus)
  },
}
