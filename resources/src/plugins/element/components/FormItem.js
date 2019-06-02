import { FormItem } from 'element-ui'

FormItem.mounted = function () {
  if (this.prop) {
    this.dispatch('ElForm', 'el.form.addField', [this])

    let initialValue = this.fieldValue
    if (Array.isArray(initialValue)) {
      initialValue = [].concat(initialValue)
    }
    Object.defineProperty(this, 'initialValue', {
      value: initialValue,
      // 可以重写
      writable: true,
    })

    this.addValidateEvents()
  }
}

export default {
  extends: FormItem,
  methods: {
    setInitialValue() {
      this.initialValue = this.fieldValue
    },
  },
}
