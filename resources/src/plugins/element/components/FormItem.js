import { FormItem } from 'element-ui'
import { getPropByPath } from 'element-ui/lib/utils/util'

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
  props: {
    /**
     * 提示性文字
     */
    helper: String,
  },
  methods: {
    setInitialValue() {
      // 简单的深拷贝一个值，避免值是对象时，里面的值会跟着变，导致不能重置
      this.initialValue = (this.fieldValue === undefined)
        ? undefined
        : JSON.parse(JSON.stringify(this.fieldValue))
    },
    // form-item 设置 required 之后, 只会自动在前面加上 红色 * 号, 不会自动验证是否填了值
    getRules() {
      let formRules = this.form.rules
      const selfRules = this.rules

      const prop = getPropByPath(formRules, this.prop || '')
      formRules = formRules ? (prop.o[this.prop || ''] || prop.v) : []

      return [].concat(selfRules || formRules || [])
    },
    resetField() {
      FormItem.methods.resetField.call(this)
      this.$nextTick(() => {
        this.inputComponent && this.inputComponent.$emit('field-reset')
      })
    },
  },
}
