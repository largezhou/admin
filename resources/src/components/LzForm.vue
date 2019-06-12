<template>
  <el-form
    ref="form"
    style="width: 800px;"
    v-bind="$props"
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
  extends: Form,
  created() {
    this.bindMethodThisToElForm()
  },
  methods: {
    /**
     * 把组件中继承来的方法中的 this, 指向 ElForm 实例
     */
    bindMethodThisToElForm() {
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
