import { Form } from 'element-ui'

export default {
  extends: Form,
  props: {
    labelWidth: {
      type: String,
      default: '150px',
    },
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
}
