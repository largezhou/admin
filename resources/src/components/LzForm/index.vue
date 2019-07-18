<template>
  <el-form
    v-loading="loading"
    ref="form"
    :style="{ width: inDialog ? 'auto' : '800px' }"
    :class="{ 'in-dialog': inDialog }"
    v-bind="$attrs"
    v-on="$listeners"
    :model="form"
    :errors="errors"
    :label-position="realLabelPosition"
  >
    <slot/>
    <slot name="footer">
      <el-form-item class="footer">
        <loading-action type="primary" :action="onSubmit">{{ submitText }}</loading-action>
        <el-button @click="onReset">重置</el-button>
        <slot name="footer-append"/>
      </el-form-item>
    </slot>
  </el-form>
</template>

<script>
import _forIn from 'lodash/forIn'
import _get from 'lodash/get'
import Form from '@/plugins/element/components/Form'
import { handleValidateErrors } from '@/libs/utils'

export default {
  name: 'LzForm',
  inject: {
    // 使用 FormHelper 混入，会自动提供该注入
    view: {
      from: 'view',
      default: null,
    },
  },
  data() {
    return {
      loading: false,
    }
  },
  props: {
    getData: Function,
    submit: Function,
    errors: Object,
    form: Object,
    submitText: {
      type: String,
      default: '保存',
    },
    labelPosition: String,
    inDialog: Boolean,
  },
  computed: {
    realLabelPosition() {
      return this.labelPosition || (this.$store.state.miniWidth ? 'top' : 'right')
    },
  },
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
    async _getData() {
      this.loading = true
      this.view && this.$emit('update:form', this.view.formBak)

      try {
        this.getData && await this.getData()
        await this.$nextTick()
        this.setInitialValues()
      } catch (e) {
        Promise.reject(e)
      }

      this.loading = false
    },
    async onSubmit() {
      this.$emit('update:errors', {})
      try {
        this.submit && await this.submit()
      } catch (e) {
        this.$emit('update:errors', handleValidateErrors(e.response))
        if (_get(e, 'response.status') !== 422) {
          Promise.reject(e)
        }
      }
    },
    onReset() {
      this.$refs.form.resetFields()
    },
  },
  watch: {
    $route: {
      handler() {
        this._getData()
      },
      immediate: true,
    },
  },
}
</script>

<style scoped lang="scss">
.in-dialog {
  .footer {
    text-align: right;
  }
}
</style>
