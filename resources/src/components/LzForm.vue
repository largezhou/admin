<template>
  <el-form
    ref="form"
    style="width: 800px;"
    v-bind="$attrs"
    v-on="$listeners"
  >
    <slot/>
  </el-form>
</template>

<script>
import _forIn from 'lodash/forIn'
import Form from '@/plugins/element/components/Form'

export default {
  name: 'LzForm',
  created() {
    this.copyMethods()
  },
  methods: {
    /**
     * 复制实际 Form 组件的方法, 给外部调用, 并把 this 指向 ElForm 实例
     */
    copyMethods() {
      // 原始 ElForm 和 重写的 Form
      [Form.extends.methods, Form.methods].forEach(methods => {
        _forIn(methods, (m, k) => {
          this[k] = function () {
            m.apply(this.$refs.form, arguments)
          }
        })
      })
    },
  },
}
</script>
