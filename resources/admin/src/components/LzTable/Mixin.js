export default {
  inject: {
    elTable: {
      default: '',
    },
  },
  data: () => ({
    identify: undefined,
  }),
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
  methods: {
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
    setIdentify() {
      // 唯一标识，获取之后，就不会再变了，跟 index 无关
      if (this.identify !== undefined) {
        return
      }

      const index = this.getIndex()
      this.identify = this.data[index][this.rowKey]
    },
    checkResourceName() {
      if (!this.resource) {
        throw new Error(`必须在 [ 表格 ] 或者 [ ${this.$options.name} ] 中，传入 [ resource ] 属性`)
      }
    },
  },
}
