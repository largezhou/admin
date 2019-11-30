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
      <template v-if="!disableView">
        <i
          v-if="isImage"
          class="el-icon-view"
          @click.stop="onPreview"
          title="查看"
        />
        <a
          v-else
          class="el-icon-view"
          :href="url"
          target="_blank"
          title="查看"
        />
      </template>
      <slot :file="formattedFile"/>
    </div>
  </div>
</template>

<script>
import { isImage } from '@/libs/validates'
import { mapState } from 'vuex'
import { getUrl } from '@/libs/utils'
import GlobalDialog from '@c/GlobalDialog'

export default {
  name: 'FilePreview',
  data() {
    return {
      formattedFile: {
        path: '',
        url: '',
      },

      width: 0,
      height: 0,
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

      this.setImgSize()

      let vm
      vm = GlobalDialog.new({
        customClass: 'image-preview-dialog',
        directives: [
          {
            name: 'resize',
            value: this.setImgSize,
          },
        ],
        content: (h) => {
          return h('img', {
            domProps: {
              src: this.formattedFile.url,
              width: this.width,
              height: this.height,
            },
          })
        },
      })
      vm.$el.classList.add('image-preview-dialog-wrapper')
    },
    setImgSize() {
      const maxWidth = Math.min(1000, window.innerWidth * 0.9)
      const maxHeight = window.innerHeight * 0.9

      const imgEl = this.$el.querySelector('.img')
      this.width = imgEl.naturalWidth || 1
      this.height = imgEl.naturalHeight || 1
      const ratio = this.width / this.height

      if (this.width > maxWidth) {
        this.width = maxWidth
        this.height = this.width / ratio
      }

      if (this.height > maxHeight) {
        this.height = maxHeight
        this.width = this.height * ratio
      }
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
  background: rgba(0, 0, 0, 0.6);
  transition: all .3s;

  > * {
    font-size: 24px;
    cursor: pointer;
    text-decoration: none;
    color: $--color-primary;
  }
}
</style>

<style lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.image-preview-dialog-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
}

.image-preview-dialog {
  margin: 0 !important;
  display: inline-block;
  width: auto !important;
  border-radius: $--border-radius-base;

  .el-dialog__header {
    display: none;
  }

  .el-dialog__body {
    padding: 0;
    font-size: 0;
  }

  img {
    border-radius: $--border-radius-base;
  }
}
</style>
