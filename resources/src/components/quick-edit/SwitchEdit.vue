<template>
  <el-switch
    ref="switch"
    v-bind="$attrs"
    v-on="$listeners"
    @change="onChange"
    :disabled="loading"
  />
</template>

<script>
export default {
  name: 'SwitchEdit',
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
    this.oldVal = this.$refs.switch.value
  },
  methods: {
    async onChange(val) {
      if (this.loading) {
        return
      }

      try {
        this.loading = true
        await this.update(this.id, { [this.field]: val })
        this.$message.success('修改成功')
      } catch (e) {
        this.$refs.switch.$emit('input', this.oldVal)
      } finally {
        this.loading = false
      }
    },
  },
}
</script>
