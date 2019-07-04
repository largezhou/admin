import { getFirstError, getMessage } from '@/libs/utils'

export default {
  data() {
    return {
      loading: false,
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
    onChange(val) {
      this.goUpdate(val)
    },
    async goUpdate(val) {
      if (this.loading) {
        return
      }

      try {
        this.loading = true
        await this.update(this.id, { [this.field]: val })
        this.setOldVal()
        this.$message.success(getMessage('updated'))
      } catch ({ response: res }) {
        this.$refs.input.$emit('input', this.oldVal)

        const err = getFirstError(res)
        err && this.$message.error(err)
      } finally {
        this.loading = false
      }
    },
    setOldVal() {
      this.oldVal = this.$refs.input.value
    },
  },
}
