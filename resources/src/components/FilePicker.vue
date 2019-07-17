<template>
  <div class="file-picker">
    <div class="file-wrapper">
      <file-preview
        class="preview file-item"
        v-for="(item, i) of arrayValue"
        :key="i"
        :file="item"
      >
        <div class="actions flex-box">
          <i class="el-icon-delete" @click="remove(i)"/>
        </div>
      </file-preview>

      <div
        v-show="canPick"
        class="picker file-item flex-box"
        @click="onPick"
      >
        <svg-icon :icon-class="pickerIcon"/>
      </div>
    </div>

    <el-dialog
      :title="title"
      :visible.sync="dialog"
      width="90%"
      custom-class="system-media-dialog"
    >
      <system-media
        ref="media"
        :default-multiple="multiple"
        :default-ext="ext"
      >
        <template v-slot:actions="media">
          <el-button
            type="primary"
            :disabled="!media.anySelected"
            @click="onPickConfirm(media.selected)"
          >
            选定
          </el-button>
        </template>
      </system-media>
    </el-dialog>
  </div>
</template>

<script>
import SystemMedia from '@c/SystemMedia/index'
import _get from 'lodash/get'
import FilePreview from '@c/FilePreview'
import _pick from 'lodash/pick'

export default {
  name: 'FilePicker',
  components: {
    FilePreview,
    SystemMedia,
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
      dialog: false,
      formattedValue: null,
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
        return _get(this.elFormItem, 'label', '')
      },
    },
    value: null,
    /**
     * 类型筛选，强制限制上传和选择
     */
    ext: String,
    /**
     * 返回的数据对象中，只包含指定字段，url 字段会自动包含
     * 不传则包含所有字段
     */
    valueFields: {
      type: String,
      default: '',
    },
  },
  computed: {
    pickerIcon() {
      return this.multiple ? 'multi-file' : 'single-file'
    },
    canPick() {
      return (this.multiple && this.value.length < this.max) ||
        (!this.multiple && !this.value)
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
  },
  methods: {
    onPick() {
      if (!this.canPick) {
        return
      }

      this.dialog = true
    },
    onPickConfirm(selected) {
      let value
      // 复制并格式化 selected 中的对象的值
      if (this.multiple) {
        value = this.value
          .concat(
            selected
              .slice(0, this.max - this.value.length)
              .map(this.formatReturn),
          )
      } else {
        value = this.formatReturn(selected[0])
      }

      this.$emit('input', value)
      this.$refs.media.clearSelected()
      this.dialog = false
    },
    /**
     * 根据 valueFields 和 flattenValue 格式化返回值
     */
    formatReturn(value) {
      const fields = this.valueFields ? this.valueFields.split(',') : []
      if (fields.length === 0) { // 返回字段为空，返回所有字段
        return { ...value }
      } else { // 否则返回指定字段
        // 默认自动包含 url 字段
        fields.push('url')
        return _pick(value, fields)
      }
    },
    remove(index) {
      if (this.multiple) {
        this.value.splice(index, 1)
      } else {
        this.$emit('input', null)
      }
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.file-wrapper {
  display: flex;
  flex-wrap: wrap;
}

.picker {
  cursor: pointer;
}

.preview {
  position: relative;

  &:hover {
    .actions {
      opacity: 1;
    }
  }
}

.file-item {
  width: 100px;
  height: 100px;
  border: $--border-base;
  border-radius: $--border-radius-base;
  color: $--color-info;
  margin: 0 5px 5px 0;
  transition: all .3s;

  &:hover {
    border-color: $--border-color-hover;
  }

  svg {
    width: 50px;
    height: 50px;
  }
}

.actions {
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  background: rgba(0, 0, 0, 0.6);
  transition: all .3s;

  i {
    color: $--color-danger;
    font-size: 30px;
    cursor: pointer;
  }
}
</style>

<style lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.system-media-dialog {
  &.el-dialog {
    max-width: 1000px;
  }

  & > {
    .el-dialog__header {
      border-bottom: 1px solid $--color-info-light;
    }

    .el-dialog__body {
      padding: 0 20px 0 0;
    }
  }
}
</style>
