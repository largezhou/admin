<template>
  <div class="file-preview flex-box">
    <div
      v-if="!path"
      class="invalid"
      :title="path"
    >
      无效
    </div>
    <img
      v-else-if="isImage"
      class="img"
      :alt="path"
      :title="path"
      :src="url"
    >
    <div class="path" v-else :title="path">{{ path }}</div>

    <div class="actions flex-box">
      <i
        v-if="isImage || disableView"
        class="el-icon-view view"
        @click.stop="onPreview"
      />
      <slot :file="formattedFile"/>
    </div>
  </div>
</template>

<script>
import { isImage } from '@/libs/validates'
import { mapState } from 'vuex'
import { getUrl } from '@/libs/utils'

export default {
  name: 'FilePreview',
  data() {
    return {
      formattedFile: {
        path: '',
        url: '',
      },
    }
  },
  props: {
    file: null,
    disableView: Boolean,
    ...mapState(['miniWidth']),
  },
  computed: {
    isImage() {
      return isImage(this.formattedFile.path)
    },
    url() {
      return this.formattedFile.url
    },
    path() {
      return this.formattedFile.path
    },
  },
  methods: {
    toUpperCase(str) {
      return str.toUpperCase()
    },
    /**
     * 把传入的 file 值，格式化为一个有几个必要字段（path, url）的对象
     */
    formatFile() {
      let f = this.file
      if (!f) {
        return
      }

      if (typeof f === 'string') {
        this.formattedFile = {
          path: f,
          url: getUrl(f),
        }
      } else if (typeof f === 'object') {
        this.formattedFile = Object.assign({}, f, {
          path: f.path || '',
          url: f.url || getUrl(f.path),
        })
      }
    },
    onPreview() {
      if (!this.isImage) {
        return
      }

      const maxWidth = Math.min(1000, window.innerWidth * 0.9)
      const maxHeight = window.innerHeight * 0.9

      const imgEl = this.$el.querySelector('.img')
      let width = imgEl.naturalWidth || 1
      let height = imgEl.naturalHeight || 1
      const ratio = width / height

      if (width > maxWidth) {
        width = maxWidth
        height = width / ratio
      }

      if (height > maxHeight) {
        height = maxHeight
        width = height * ratio
      }

      this.$msgbox({
        message: this.$createElement('img', {
          domProps: {
            src: this.formattedFile.url,
            width,
            height,
          },
        }),
        showConfirmButton: false,
        callback: () => {}, // 避免取消的时候，控制台显示一个 reject 错误
        customClass: `image-preview-dialog${this.miniWidth ? ' mini-width' : ''}`,
      })
    },
  },
  watch: {
    file: {
      handler() {
        this.formatFile()
      },
      immediate: true,
    },
  },
}
</script>

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.file-preview {
  width: 100px;
  height: 100px;
  min-width: 100px;
  min-height: 100px;
  overflow: hidden;
  border: $--border-base;
  border-radius: $--border-radius-base;
  margin: 0 5px 5px 0;
  transition: all .3s;
  position: relative;

  &:hover {
    .actions {
      opacity: 1;
    }
  }

  img {
    max-width: 100%;
    max-height: 100%;
    word-break: break-all;
  }

  .path {
    overflow: hidden;
    margin: 0 5px;
    color: $--color-info;
    font-size: 12px;
    word-break: break-all;
    line-height: initial;
  }

  .invalid {
    font-size: 20px;
    font-style: italic;
  }
}

.actions {
  width: 100%;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  opacity: 0;
  background: rgba(0, 0, 0, 0.3);
  transition: all .3s;

  i {
    font-size: 24px;
    cursor: pointer;
  }

  .view {
    color: $--color-primary;
  }
}
</style>

<style lang="scss">
.image-preview-dialog {
  width: auto;
  height: auto;
  padding: 0;
  border: none;

  .el-message-box__content {
    padding: 0;
  }

  .el-message-box__btns {
    display: none;
  }

  .el-message-box__message {
    font-size: 0;
  }
}
</style>
