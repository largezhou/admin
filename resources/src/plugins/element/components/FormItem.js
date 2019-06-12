import { FormItem } from 'element-ui'
import { getPropByPath } from 'element-ui/src/utils/util'

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
    // form-item 设置 required 之后, 只会自动在前面加上 红色 * 号, 不会自动验证是否填了值
    getRules() {
      let formRules = this.form.rules
      const selfRules = this.rules

      const prop = getPropByPath(formRules, this.prop || '')
      formRules = formRules ? (prop.o[this.prop || ''] || prop.v) : []

      return [].concat(selfRules || formRules || [])
    },
  },
}
