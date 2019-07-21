<template>
  <el-form label-position="top">
    <template v-if="type === this.CONFIG_TYPES.FILE">
      <el-form-item label="最大上传数">
        <el-input-number v-model="value.max" :min="1" :max="99"/>
      </el-form-item>
      <el-form-item label="文件类型">
        <el-input v-model="value.ext" placeholder="多个之间用英文逗号隔开"/>
      </el-form-item>
    </template>
    <template v-else-if="type === this.CONFIG_TYPES.SINGLE_SELECT || type === this.CONFIG_TYPES.MULTIPLE_SELECT">
      <el-form-item label="选项设置">
        <el-input
          type="textarea"
          rows="4"
          v-model="value.options"
          :placeholder="'配置示例：\n值1=>文字1\n值2=>文字2'"
        />
      </el-form-item>
      <el-form-item label="选择形式">
        <el-radio-group v-model="value.type">
          <el-radio label="input">单选/复选</el-radio>
          <el-radio label="select">下拉选择</el-radio>
        </el-radio-group>
      </el-form-item>
    </template>
    <template v-else>
      <span>无</span>
    </template>
  </el-form>
</template>

<script>
import { mapConstants } from '@/libs/constants'
import _pick from 'lodash/pick'

export default {
  name: 'TypeOptions',
  inject: {
    elForm: {
      default: '',
    },

    elFormItem: {
      default: '',
    },
  },
  props: {
    type: String,
    value: Object,
  },
  computed: {
    ...mapConstants(['CONFIG_TYPES']),
  },
  created() {
    /**
     * 存储类型对应的 options
     * 用来在切换类型后，恢复原来设置的值
     */
    this.optionsBak = {}

    this.registryToFormItem()
    this.$on('field-reset', this.onReset)
  },
  beforeDestroy() {
    this.$off('field-reset', this.onReset)
  },
  methods: {
    registryToFormItem() {
      this.elFormItem && (this.elFormItem.inputComponent = this)
    },
    onReset() {
      this.optionsBak = {}
      this.initOptions()
    },
    initOptions() {
      const type = this.type
      if (!type) {
        return
      }

      let defaultValue
      switch (type) {
        case this.CONFIG_TYPES.FILE:
          defaultValue = {
            max: 1,
            ext: '',
          }
          break
        case this.CONFIG_TYPES.SINGLE_SELECT:
        case this.CONFIG_TYPES.MULTIPLE_SELECT:
          defaultValue = {
            options: '',
            type: 'input',
          }
          break
        default:
          defaultValue = null
      }

      const v = defaultValue
        ? _pick(Object.assign({}, this.optionsBak[type] || defaultValue, this.value), Object.keys(defaultValue))
        : null
      // 存储类型对应的 options，用来还原
      this.optionsBak[type] = v
      this.$emit('input', v)
    },
  },
  watch: {
    type: {
      handler() {
        this.initOptions()
      },
      immediate: true,
    },
    $route() {
      // 路由变化会复用组件，清空一下
      this.optionsBak = {}
    },
  },
}
</script>
