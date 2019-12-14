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
    miniWidth() {
      return this.$store.state.miniWidth
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
        // 在某些情况下，会出现方法未定义，所以放到 nextTick 中
        this.$nextTick(() => {
          this.setInitialValues()
        })
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
        this.$active && this._getData()
      },
      immediate: true,
    },
  },
  render(h) {
    let defaultSlot = this.$slots.default
    if (Array.isArray(defaultSlot)) {
      defaultSlot = defaultSlot.map((formItem) => {
        const options = formItem.componentOptions

        // 如果有 helper props，则生成一个新的 FormItem 替换掉原来的
        let { helper, label } = options.propsData
        if (helper) {
          helper = helper.replace(/\n/g, '<br>')
          const labelSlot = h('template', {
            slot: 'label',
          }, [
            (<span>{label}</span>),
            (
              <el-tooltip
                effect="dark"
                placement="top-start"
                popper-class={`form-helper-popper ${this.miniWidth ? 'mini-width' : ''}`}
              >
                <div slot="content" domPropsInnerHTML={helper}/>
                <i class="ml-1 el-icon-question helper"/>
              </el-tooltip>
            ),
          ])
          return h('el-form-item', {
            props: options.propsData,
          }, [labelSlot, ...options.children])
        } else {
          return formItem
        }
      })
    }

    const stayCheckbox = !this.disableStay && (
      <el-checkbox
        vModel={this.stay}
        title="表单提交后，留在此页"
      >
        留在此页
      </el-checkbox>
    )

    const footerSlot = this.$slots.footer || (
      <el-form-item class="footer">
        <loading-action type="primary" action={this.onSubmit}>{this.submitText}</loading-action>
        <el-button vOn:click={this.onReset}>重置</el-button>
        {this.$slots.footerAppend}
        <flex-spacer/>
        {stayCheckbox}
      </el-form-item>
    )

    // 用 jsx，ElForm 组件的 model props 传不进去，，，
    return h('el-form', {
      props: {
        model: this.form,
        errors: this.errors,
        labelPosition: this.realLabelPosition,
        ...this.$attrs,
      },
      on: {
        ...this.$listeners,
      },
      class: {
        'in-dialog': this.inDialog,
      },
      style: {
        width: this.inDialog ? 'auto' : '800px',
      },
      directives: [
        {
          name: 'loading',
          value: this.loading,
        },
      ],
      ref: 'form',
    }, [defaultSlot, footerSlot])
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.footer {
  ::v-deep {
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

.helper {
  color: $--color-primary;
  border: 2px solid transparent;
  border-radius: 50%;
  transition: border-color .3s;

  &:hover {
    border-color: $--color-primary-light-1;
  }
}
</style>

<style lang="scss">
.form-helper-popper {
  max-width: 400px;

  &.mini-width {
    max-width: 90%;
  }
}
</style>
