import { Form } from 'element-ui'

export default {
  extends: Form,
  props: {
    labelWidth: {
      type: String,
      default: '150px',
    },
    errors: Object,
  },
  methods: {
    setInitialValues() {
      if (!this.model) {
        console.warn('[Element Warn][Form]model is required for resetFields to work.')
        return
      }
      this.fields.forEach(field => {
        field.setInitialValue()
      })
    },
  },
  watch: {
    /**
     * 自动给 FormItem 设置错误信息, 不用每个 FormItem 手动指定这个
     */
    errors: {
      handler(newVal) {
        this.fields.forEach(i => {
          if (i.prop) {
            i.validateMessage = newVal[i.prop]
            i.validateState = newVal[i.prop] ? 'error' : ''
          }
        })
      },
      immediate: true,
    },
  },
}
