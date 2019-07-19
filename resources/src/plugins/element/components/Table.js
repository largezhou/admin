import { Table } from 'element-ui'

export default {
  extends: Table,
  provide() {
    return {
      elTable: this,
    }
  },
  props: {
    stripe: {
      type: Boolean,
      default: true,
    },
    border: {
      type: Boolean,
      default: true,
    },
    rowKey: {
      type: [String, Function],
      default: 'id',
    },
    /**
     * API 资源名
     */
    resource: String,
  },
}
