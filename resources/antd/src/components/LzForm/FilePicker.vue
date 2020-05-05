<template>
  <div class="file-picker">
    <div class="file-wrapper">
      <file-preview
        class="preview file-item"
        v-for="(item, i) of arrayValue"
        :key="i"
        :file="item"
      >
        <a-icon
          class="replace"
          type="sync"
          title="替换"
          @click.stop="onReplace(i)"
        />
        <lz-popconfirm title="确认移除？" :confirm="() => remove(i)">
          <a-icon
            class="remove"
            type="delete"
            title="移除"
          />
        </lz-popconfirm>
      </file-preview>

      <div
        v-show="!isMax"
        class="picker file-item flex-box"
        @click="onPick"
      >
        <svg-icon :icon-class="pickerIcon"/>
      </div>
    </div>

    <a-modal
      :title="title"
      v-model="dialog"
      width="90%"
      :footer="null"
      wrap-class-name="system-media-dialog"
    >
      <system-media
        ref="media"
        :default-multiple="mediaMultiple"
        :default-ext="ext"
      >
        <template #actions="media">
          <a-button
            type="primary"
            :disabled="!media.anySelected"
            @click="onPickConfirm(media.selected)"
          >
            选定
          </a-button>
        </template>
      </system-media>
    </a-modal>
  </div>
</template>

<script>
import SystemMedia from '@c/SystemMedia/index'
import FilePreview from '@c/FilePreview'
import _pick from 'lodash/pick'
import LzPopconfirm from '@c/LzPopconfirm'

export default {
  name: 'FilePicker',
  components: {
    LzPopconfirm,
    FilePreview,
    SystemMedia,
  },
  inject: {
    lzFormItem: {
      default: null,
    },
  },
  data() {
    return {
      dialog: false,
      formattedValue: null,
      pickIndex: -1,
    }
  },
  props: {
    /**
     * 多选
     */
    multiple: Boolean,
    /**
     * 最多选多少个
     */
    max: {
      type: [String, Number],
      default: 8,
    },
    /**
     * 选择器弹框的标题，默认为 FormItem 的 label 属性
     */
    title: {
      type: String,
      default() {
        return this.lzFormItem?.label || '选择文件'
      },
    },
    value: null,
    /**
     * 类型筛选，强制限制上传和选择
     */
    ext: String,
    /**
     * 返回的数据对象中，只包含指定字段，path 字段会自动包含
     * 不传则包含所有字段
     */
    valueFields: {
      type: String,
      default: 'path',
    },
    /**
     * 在 valueFields 只返回一个字段时，是否直接返回该字段值，而非对象
     */
    flatValue: {
      type: Boolean,
      default: true,
    },
  },
  computed: {
    pickerIcon() {
      return this.multiple ? 'multi-file' : 'single-file'
    },
    canPick() {
      // 没有达到最大可选数，或者是替换文件的情况下，可以打开文件选择器
      return !this.isMax || this.isReplace
    },
    isMax() {
      return (this.multiple && this.value.length >= this.max) ||
        (!this.multiple && this.value)
    },
    /**
     * 是否是处于替换文件的情况
     */
    isReplace() {
      return this.pickIndex !== -1
    },
    miniWidth() {
      return this.$store.state.miniWidth
    },
    arrayValue() {
      if (!this.value) {
        return []
      }

      return Array.isArray(this.value) ? this.value : [this.value]
    },
    mediaMultiple() {
      // 替换的情况下，只能单选
      return this.isReplace ? false : this.multiple
    },
  },
  methods: {
    onPick() {
      if (!this.canPick) {
        return
      }

      this.dialog = true
    },
    onPickConfirm(selected) {
      selected = selected.map(this.formatReturn)
      let value
      // 复制并格式化 selected 中的对象的值
      if (this.multiple) {
        if (this.isReplace) {
          value = this.value
          value[this.pickIndex] = selected[0]
        } else {
          value = this
            .value
            .concat(selected.slice(0, this.max - this.value.length))
        }
      } else {
        value = selected[0]
      }

      this.$emit('input', value)
      this.$refs.media.clearSelected()
      this.dialog = false
    },
    /**
     * 根据 valueFields 和 flatValue 格式化返回值
     */
    formatReturn(value) {
      let fields = this.valueFields ? this.valueFields.split(',') : []
      if (fields.length === 0) { // 返回字段为空，返回所有字段
        value = { ...value }
      } else { // 否则返回指定字段
        // 默认自动包含 path 字段
        fields.push('path')
        value = _pick(value, fields)
      }

      // 去重
      fields = [...new Set(fields)]
      // 在只有一个字段时，如果需要平铺，则只返回该字段的值
      if (this.flatValue && fields.length === 1) {
        return value[fields[0]]
      } else {
        return value
      }
    },
    remove(index) {
      if (this.multiple) {
        this.value.splice(index, 1)
      } else {
        this.$emit('input', null)
      }
    },
    onReplace(index) {
      this.pickIndex = index
      this.onPick()
    },
  },
  watch: {
    dialog(newVal) {
      if (!newVal) {
        this.pickIndex = -1
      }
    },
  },
}
</script>

<style scoped lang="less">
@import "~@/styles/vars";

.file-wrapper {
  display: flex;
  flex-wrap: wrap;
}

.picker {
  cursor: pointer;
}

.file-item {
  width: 100px;
  height: 100px;
  min-width: 100px;
  min-height: 100px;
  border: @border-base;
  border-radius: @border-radius-base;
  color: @border-color-base;
  margin: 0 5px 5px 0;
  transition: all .3s;

  &:hover {
    border-color: @input-hover-border-color;
    color: @info-color;
  }

  svg {
    width: 50px;
    height: 50px;
  }
}

.remove {
  color: @error-color;
}

.replace {
  color: @primary-color;
}
</style>

<style lang="less">
.system-media-dialog {
  .ant-modal {
    max-width: 848px;
  }
}
</style>
