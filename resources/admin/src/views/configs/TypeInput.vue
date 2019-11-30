<script>
import { mapConstants } from '@/libs/constants'
import FilePicker from '@c/FilePicker'
import _debounce from 'lodash/debounce'

export default {
  name: 'TypeInput',
  components: {
    FilePicker,
  },
  inject: {
    elForm: {
      default: '',
    },

    elFormItem: {
      default: '',
    },
  },
  data() {
    return {
      filePickerKey: 1,
    }
  },
  props: {
    type: String,
    value: null,
    options: Object,
  },
  computed: {
    ...mapConstants(['CONFIG_TYPES']),
    selectOptions() {
      if (
        (this.type !== this.CONFIG_TYPES.SINGLE_SELECT) &&
        (this.type !== this.CONFIG_TYPES.MULTIPLE_SELECT)
      ) {
        return []
      } else {
        const pairs = this.options.options.split('\n')
        const options = []
        pairs.forEach((pair) => {
          const [value, label] = pair.split('=>').map((i) => i.trim())
          if (label) {
            options.push({ value, label })
          }
        })
        return options
      }
    },
  },
  methods: {
    onInput(val) {
      this.$emit('input', val)
    },
    /**
     * 当类型或者文件选择最大限制改变时，要改变值的类型
     */
    initValue() {
      if (this.type === this.CONFIG_TYPES.FILE) {
        if (this.options.max > 1 && !Array.isArray(this.value)) {
          // 如果是多选，且原来的值不是数组，则用数组包起来，空值则为空数组
          this.onInput(this.value ? [this.value] : [])
        } else if (this.options.max <= 1 && Array.isArray(this.value)) {
          // 如果是单选，且原来的值是数组，则选第一个，否则为 null
          this.onInput(this.value[0] || null)
        }
      } else if (this.type === this.CONFIG_TYPES.MULTIPLE_SELECT) {
        !Array.isArray(this.value) && this.onInput([])
      } else {
        if (['string', 'boolean', 'number'].indexOf(typeof this.value) === -1) {
          this.onInput(null)
        }
      }
    },
    /**
     * 重新渲染 FilePicker 组件
     */
    updateFilePicker: _debounce(function () {
      this.filePickerKey++
    }, 200),
  },
  watch: {
    type: {
      handler() {
        this.initValue()
      },
      immediate: true,
    },
    'options.max'(newVal, oldVal) {
      // 如果变了之后，都还是多选，则不用更新
      // 这里条件取反
      if (!((newVal > 1) && (oldVal > 1))) {
        this.updateFilePicker()
      }
      this.initValue()
      // 多选时，如果数量变少了，则要去掉 value 中多出来的文件
      if ((newVal > 1) && (newVal < oldVal)) {
        this.onInput(this.value.slice(0, newVal))
      }
    },
    'options.ext'() {
      this.updateFilePicker()
    },
  },
  render(h) {
    let component = null
    const renderData = {
      props: {
        value: this.value,
      },
      on: {
        input: this.onInput,
      },
    }
    let slots = null

    const TYPES = this.CONFIG_TYPES
    switch (this.type) {
      case TYPES.INPUT:
      case TYPES.OTHER:
        component = 'el-input'
        break
      case TYPES.TEXTAREA:
        component = 'el-input'
        Object.assign(renderData.props, {
          rows: 4,
          type: 'textarea',
        })
        break
      case TYPES.FILE:
        component = 'file-picker'
        Object.assign(renderData.props, {
          max: this.options.max,
          ext: this.options.ext,
          multiple: this.options.max > 1,
        })
        renderData.key = this.filePickerKey
        break
      case TYPES.SINGLE_SELECT:
      case TYPES.MULTIPLE_SELECT:
        const isMultiple = this.type === TYPES.MULTIPLE_SELECT
        if (this.options.type === 'input') {
          component = isMultiple
            ? 'el-checkbox-group'
            : 'el-radio-group'
          slots = this.selectOptions.map((i) => (
            h(isMultiple ? 'el-checkbox' : 'el-radio', {
              props: {
                key: i.value,
                label: i.value,
              },
            }, i.label)
          ))
        } else if (this.options.type === 'select') {
          component = 'el-select'
          Object.assign(renderData.props, {
            multiple: isMultiple,
            clearable: isMultiple,
          })
          slots = this.selectOptions.map((i) => (
            <el-option key={i.value} value={i.value} label={i.label}/>
          ))
        }
        break
      default:
      // do nothing
    }

    return h(component, renderData, slots)
  },
}
</script>
