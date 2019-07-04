import _get from 'lodash/get'

export default {
  name: 'CollapseButtonGroup',
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
      return (
        <el-dropdown trigger="click">
          <el-button>
            操作<i class="el-icon-arrow-down el-icon--right"/>
          </el-button>
          <el-dropdown-menu slot="dropdown">
            {buttons.map(i => <el-dropdown-item class={{ 'button-item': true }}>{i}</el-dropdown-item>)}
          </el-dropdown-menu>
        </el-dropdown>
      )
    } else {
      return (
        <el-button-group>{buttons}</el-button-group>
      )
    }
  },
}
