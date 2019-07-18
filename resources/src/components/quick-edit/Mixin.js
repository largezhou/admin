import { getFirstError, getMessage } from '@/libs/utils'

export default {
  data() {
    return {
      loading: false,
      /**
       * 更新出错时，是否把编辑中的值，重置为初始值
       */
      resetValueWhenError: false,
    }
  },
  props: {
    update: Function,
    field: String,
    id: [String, Number],
  },
  async mounted() {
    await this.$nextTick()
    this.setOldVal()
  },
  methods: {
    /**
     * 默认提交方法，可适当重写
     *
     * @return {Promise<void>}
     */
    async onSubmit() {
      await this.goUpdate()
    },
    async goUpdate(val = this.$refs.input.value) {
      if (this.loading) {
        return
      }

      try {
        this.loading = true
        const res = await this.update(this.id, { [this.field]: val })
        this.setOldVal()
        this.$message.success(getMessage('updated'))

        this.onSuccess(res)
      } catch (e) {
        const res = e.response
        if (this.resetValueWhenError) {
          this.changeVal(this.oldVal)
        }

        const err = getFirstError(res)
        err && this.$message.error(err)

        this.$nextTick(() => {
          this.focus()
        })

        this.onError(e)
      } finally {
        this.loading = false
      }
    },
    setOldVal() {
      this.oldVal = this.$refs.input.value
    },
    changeVal(val) {
      this.$refs.input.$emit('input', val)
    },
    focus() {
      this.$refs.input.focus()
    },
    /**
     * 请求出错的回调
     *
     * @param e 错误实例
     */
    onError(e) {},
    /**
     * 请求成功的回调
     * @param response 完整的响应
     */
    onSuccess(response) {},
  },
}
