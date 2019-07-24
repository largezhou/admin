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
        <flex-spacer/>
        <el-checkbox
          v-if="!disableStay"
          v-model="stay"
          title="表单提交后，留在此页"
        >
          留在此页
        </el-checkbox>
      </el-form-item>
    </slot>
  </el-form>
</template>

<script>
import _forIn from 'lodash/forIn'
import _get from 'lodash/get'
import Form from '@/plugins/element/components/Form'
import { getMessage, handleValidateErrors } from '@/libs/utils'
import FlexSpacer from '@c/FlexSpacer'

export default {
  name: 'LzForm',
  components: {
    FlexSpacer,
  },
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
      stay: false,
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
    createdRedirect: {
      type: [String, Function],
      default() {
        const p = this.$route.path.split('/')
        return '/' + (p[p.length - 2] || '')
      },
    },
    updatedRedirect: {
      type: [String, Function],
      default() {
        return this.$router.back.bind(this.$router)
      },
    },
    disableRedirect: Boolean,
    disableStay: Boolean,
    editMode: Boolean,
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

        this.$message.success(getMessage(this.editMode ? 'updated' : 'created'))

        if (this.stay || this.disableRedirect) {
          return
        }

        let redirect = this.editMode ? this.updatedRedirect : this.createdRedirect
        if (typeof redirect === 'string') {
          this.$router.push(redirect)
        } else if (typeof redirect === 'function') {
          redirect()
        }
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
.footer {
  /deep/ {
    .el-form-item__content {
      display: flex;
    }
  }
}

.in-dialog {
  .footer {
    text-align: right;
    margin-bottom: 0;
  }
}
</style>
