<template>
  <div class="file-wrapper">
    <file-preview
      v-for="(item, i) of media"
      :class="{ selected: isSelected(item) }"
      :key="item.id"
      @click.native="onSelect(item, i)"
      :file="item"
    />
  </div>
</template>

<script>
import _findIndex from 'lodash/findIndex'
import { mapConstants } from '@/libs/constants'
import { isImage } from '@/libs/validates'
import FilePreview from '@c/FilePreview'

export default {
  name: 'Files',
  components: { FilePreview },
  props: {
    media: Array,
    multiple: Boolean,
    selected: Array,
    ext: String,
  },
  computed: {
    ...mapConstants(['IMAGE_EXTS']),
  },
  methods: {
    onSelect(media, index) {
      // 不是特定的类型，不能选择
      if (
        this.ext &&
        this.ext.split(',').indexOf(media.ext ? media.ext.toLowerCase() : '') === -1
      ) {
        return
      }

      const i = this.findInSelected(media)

      if (i !== -1) { // 已经选了，则取消选择
        this.selected.splice(i, 1)
      } else { // 否则加入选择
        if (this.multiple) {
          this.selected.push(media)
        } else {
          this.$emit('update:selected', [media])
        }
      }

      this.$emit('select', this.selected, media, index, (i === -1))
    },
    findInSelected(media) {
      return _findIndex(this.selected, (i) => i.id === media.id)
    },
    isSelected(media) {
      return this.findInSelected(media) !== -1
    },
    isImage,
    toUpperCase(str) {
      return str.toUpperCase()
    },
  },
  watch: {
    multiple(newVal) {
      // 切换为单选时，只保留第一个
      if (!newVal) {
        this.selected.splice(1)
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

  .file-preview {
    border: 3px solid $--color-info-light;
    cursor: pointer;
    border-radius: 0;

    &.selected {
      border-color: $--color-primary;
      border-style: dashed;
    }
  }
}
</style>
