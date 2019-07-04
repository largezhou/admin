<template>
  <div class="file-preview flex-box">
    <img
      v-if="isImage"
      :alt="formattedFile.path"
      :title="formattedFile.path"
      :src="formattedFile.url"
    >
    <div class="ext" v-else :title="formattedFile.path">{{ upperCaseExt }}</div>
    <slot/>
  </div>
</template>

<script>
import { isImage } from '@/libs/validates'
import { getExt } from '@/libs/utils'

export default {
  name: 'FilePreview',
  data() {
    return {
      formattedFile: null,
    }
  },
  props: {
    file: null,
  },
  computed: {
    isImage() {
      return isImage(this.formattedFile.path)
    },
    upperCaseExt() {
      return (this.formattedFile.ext || 'file').toUpperCase()
    },
  },
  methods: {
    toUpperCase(str) {
      return str.toUpperCase()
    },
    /**
     * 把传入的 file 值，格式化为一个有几个必要字段（path, ext, url）的对象
     */
    formatFile() {
      let f = this.file
      if (typeof f === 'string') {
        this.formattedFile = {
          path: f,
          ext: getExt(f),
          url: f,
        }
      } else if (typeof f === 'object') {
        this.formattedFile = Object.assign({}, f, {
          ext: f.ext || getExt(f.path),
          url: f.url || f.path,
        })
      } else {
        // 避免报错
        console.error('传入的值无效: ', f)
        this.formattedFile = {
          ext: '',
          path: '',
        }
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
  overflow: hidden;
}

img {
  max-width: 100%;
  max-height: 100%;
  word-break: break-all;
}

.ext {
  overflow: hidden;
  margin: 0 10px;
  color: $--color-info;
  font-size: 20px;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>
