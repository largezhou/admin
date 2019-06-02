import { Form } from 'element-ui'

export default {
  name: Form.name,
  extends: Form,
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
