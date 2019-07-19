<template>
  <pop-confirm
    type="danger"
    :confirm="onConfirm"
    size="small"
    v-bind="$attrs"
  >
    <slot>删除</slot>
  </pop-confirm>
</template>

<script>
import PopConfirm from '@c/PopConfirm'
import axios from '@/plugins/axios'
import { getMessage } from '@/libs/utils'

export default {
  name: 'RowDestroy',
  components: {
    PopConfirm,
  },
  inject: {
    elTable: {
      default: '',
    },
  },
  props: {
    resource: {
      type: String,
      default() {
        return this.elTable && this.elTable.resource
      },
    },
    data: {
      type: Array,
      default() {
        return this.elTable && this.elTable.data
      },
    },
    index: {
      type: [Number, String],
      required: true,
    },
    rowKey: {
      type: String,
      default() {
        return this.elTable && this.elTable.rowKey
      },
    },
  },
  computed: {
    row() {
      return this.data[this.index]
    },
    /**
     * 资源标识，比如 id， slug
     */
    identify() {
      return this.row[this.rowKey]
    },
  },
  methods: {
    async onConfirm() {
      if (!this.resource) {
        throw new Error('必须在表格组件 或者该组件，传入 [ resource ] 属性')
      }

      await axios.delete(`${this.resource}/${this.identify}`)
      this.data.splice(this.index, 1)
      this.$message.success(getMessage('destroyed'))
    },
  },
}
</script>
