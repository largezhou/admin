import { Table } from 'element-ui'

export default {
  extends: Table,
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
  },
}
