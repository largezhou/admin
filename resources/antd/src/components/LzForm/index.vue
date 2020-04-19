<script>
import _get from 'lodash/get'
import { assignExists, getMessage, handleValidateErrors } from '@/libs/utils'
import LoadingAction from '@c/LoadingAction'
import LzFormItem from './LzFormItem'

export default {
  name: 'LzForm',
  components: {
    LoadingAction,
    LzFormItem,
  },
  data() {
    return {
      loading: false,
      stay: false,
      /**
       * 备份表单数据，用来重置表单
       */
      formBak: '',
    }
  },
  computed: {
    tinyWidth() {
      return this.$store.state.tinyWidth
    },
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
  methods: {
    async _getData() {
      this.loading = true

      try {
        if (this.getData) {
          const data = await this.getData()
          this.$emit('update:form', assignExists(this.form, data))
          this.formBak = JSON.stringify(data)
        }
      } finally {
        this.loading = false
      }
    },
    async onSubmit() {
      this.$emit('update:errors', {})
      try {
        this.submit && await this.submit()

        this.$message.success(getMessage(this.editMode ? 'updated' : 'created'))

        if (this.stay || this.disableRedirect) {
          return
        }

        const redirect = this.editMode ? this.updatedRedirect : this.createdRedirect
        if (typeof redirect === 'string') {
          this.$router.push(redirect)
        } else if (typeof redirect === 'function') {
          redirect()
        }
      } catch (e) {
        const status = _get(e, 'response.status')
        if (status === 422 || status === 429) {
          this.$emit('update:errors', handleValidateErrors(e.response))
        }

        throw e
      }
    },
    onReset() {
      this.$emit('update:form', JSON.parse(this.formBak))
      this.$emit('update:errors', {})
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
        const attrs = formItem.data.attrs

        const error = this.errors[attrs.prop]
        if (error) {
          options.propsData.help = error
          options.propsData.validateStatus = 'error'
        }

        return formItem
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

    const colSpan = {
      labelCol: Object.assign({ span: 5 }, this.$attrs['label-col']),
      wrapperCol: Object.assign({ span: 15 }, this.$attrs['wrapper-col']),
    }

    const footerSlot = this.$slots.footer || (
      <a-form-item wrapperCol={{ offset: this.tinyWidth ? 0 : colSpan.labelCol.span }}>
        <loading-action type="primary" action={this.onSubmit}>{this.submitText}</loading-action>
        <a-button class="ml-1" vOn:click={this.onReset}>重置</a-button>
        {this.$slots.footerAppend}
        <div class="flex-spacer"/>
        {stayCheckbox}
      </a-form-item>
    )

    return (
      <a-spin
        spinning={this.loading}
        size="large"
        style={{ width: this.inDialog ? 'auto' : '800px' }}
      >
        <a-form
          ref="form"
          {...{
            attrs: Object.assign({}, this.$attrs, colSpan),
            listeners: this.$listeners,
          }}
          class={{ 'in-dialog': this.inDialog }}
        >
          {[defaultSlot, footerSlot]}
        </a-form>
      </a-spin>
    )
  },
}
</script>
