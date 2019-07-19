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
    index: [Number, String],
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
      return this.row && this.row[this.rowKey]
    },
  },
  methods: {
    async onConfirm() {
      // 如果没有传 索引，是获取不到数据的，所以从 DOM 尝试获取索引，再获取到数据列
      let index = this.index
      let identify = this.identify
      if (identify === undefined) {
        index = this.getIndex()
        identify = this.data[index][this.rowKey]
      }

      if (!this.resource) {
        throw new Error('必须在表格组件 或者该组件，传入 [ resource ] 属性')
      }

      await axios.delete(`${this.resource}/${identify}`)
      this.data.splice(index, 1)
      this.$message.success(getMessage('destroyed'))
    },
    getIndex() {
      if (this.index !== undefined) {
        return this.index
      }

      if (!this.elTable) {
        return undefined
      }

      // 从 DOM 中获取索引
      const tr = this.findTr()
      if (!tr) {
        return undefined
      }

      const index = Array.from(this.elTable.$el.querySelectorAll('.el-table__body tr')).indexOf(tr)
      return index === -1 ? undefined : index
    },
    /**
     * 从该元素网上找到 tr 元素
     * @param el
     */
    findTr(el = this.$el) {
      if (!el) {
        return undefined
      }

      const tagName = el.tagName.toLowerCase()

      if (tagName === 'tr') {
        return el
      } else if (tagName === 'tbody') {
        return undefined
      } else {
        return this.findTr(el.parentNode)
      }
    },
  },
}
</script>
