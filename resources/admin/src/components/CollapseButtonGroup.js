import _get from 'lodash/get'

export default {
  name: 'CollapseButtonGroup',
  props: {
    minWidth: String,
  },
  computed: {
    miniWidth() {
      return this.$store.state.miniWidth
    },
    tooManyButtons() {
      return _get(this.$slots, 'default.length', 0) > 1
    },
    collapse() {
      return this.miniWidth && this.tooManyButtons
    },
  },
  render(h) {
    const buttons = this.$slots.default
    if (this.collapse) {
      const size = buttons[0].componentOptions.propsData.size
      return (
        <el-dropdown trigger="click">
          <el-button size={size}>
            操作<i class="el-icon-arrow-down el-icon--right"/>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            {buttons.map(i => <el-dropdown-item class={{ 'button-item': true }}>{i}</el-dropdown-item>)}
          </el-dropdown-menu>
        </el-dropdown>
      )
    } else {
      return (
        <el-button-group style={{ minWidth: this.minWidth }}>{buttons}</el-button-group>
      )
    }
  },
}
