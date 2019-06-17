<template>
  <el-form
    ref="form"
    style="width: 800px;"
    v-bind="$attrs"
    v-on="$listeners"
    :model="form"
    :errors="errors"
    :label-position="labelPosition"
  >
    <slot/>
    <slot name="footer">
      <el-form-item>
        <loading-action type="primary" :action="onSubmit">{{ editMode ? '更新' : '添加' }}</loading-action>
        <el-button @click="onReset">重置</el-button>
      </el-form-item>
    </slot>
  </el-form>
</template>

<script>
import _forIn from 'lodash/forIn'
import Form from '@/plugins/element/components/Form'
import { assignExsits, getMessage, handleValidateErrors } from '@/libs/utils'

export default {
  name: 'LzForm',
  props: {
    redirect: String,
    updateMethod: Function,
    storeMethod: Function,
    editMethod: Function,
    errors: Object,
    form: Object,
  },
  computed: {
    editMode() {
      return !!this.resourceId
    },
    resourceId() {
      return this.$route.params.id
    },
    labelPosition() {
      return this.$store.state.miniWidth ? 'top' : 'right'
    },
  },
  created() {
    this.copyMethods()
    if (this.editMode) {
      this.editResource()
    }
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
    async onSubmit() {
      this.$emit('update:errors', {})
      try {
        this.editMode
          ? await this.updateResource()
          : await this.storeResource()
      } catch (e) {
        this.$emit('update:errors', handleValidateErrors(e.response))
      }
    },
    async updateResource() {
      await this.updateMethod(this.resourceId, this.form)
      this.$router.back()
      this.$message.success(getMessage('updated'))
    },
    async storeResource() {
      await this.storeMethod(this.form)
      this.$router.push(this.redirect)
      this.$message.success(getMessage('created'))
    },
    async editResource() {
      const { data } = await this.editMethod(this.resourceId)
      this.$emit('update:form', assignExsits(this.form, data))
      await this.$nextTick()
      this.$refs.form.setInitialValues()
    },
    onReset() {
      this.$refs.form.resetFields()
    },
  },
}
</script>
