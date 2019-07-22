<template>
  <div class="file-preview flex-box">
    <img
      v-if="isImage"
      :alt="formattedFile.path"
      :title="formattedFile.path"
      :src="formattedFile.url"
    >
    <div class="path" v-else :title="formattedFile.path">{{ formattedFile.path }}</div>
    <slot/>
  </div>
</template>

<script>
import { isImage } from '@/libs/validates'

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
      if (typeof f === 'string') {
        this.formattedFile = {
          path: f,
          url: f,
        }
      } else if (typeof f === 'object') {
        this.formattedFile = Object.assign({}, f, {
          url: f.url || f.path,
        })
      } else {
        // 避免报错
        console.error('传入的值无效: ', f)
        this.formattedFile = {
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
  min-width: 100px;
  min-height: 100px;
  overflow: hidden;
  border: $--border-base;
  border-radius: $--border-radius-base;
  margin: 0 5px 5px 0;
  transition: all .3s;
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
</style>
