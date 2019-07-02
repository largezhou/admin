<template>
  <div class="file-wrapper">
    <div
      class="file-preview"
      v-for="(item, i) of media"
      :class="{ selected: isSelected(item) }"
      :key="item.id"
      @click="onSelect(item, i)"
    >
      <img v-if="isImage(item.ext, true)" :src="item.url">
      <div class="ext" v-else :title="item.ext">{{ toUpperCase(item.ext || 'file') }}</div>
    </div>
  </div>
</template>

<script>
import _findIndex from 'lodash/findIndex'
import { mapConstants } from '@/libs/constants'
import { isImage } from '@/libs/validates'

export default {
  name: 'Files',
  props: {
    media: Array,
    multiple: Boolean,
    selected: Array,
  },
  computed: {
    ...mapConstants(['IMAGE_EXTS']),
  },
  methods: {
    onSelect(media, index) {
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
}

.file-preview {
  width: 100px;
  height: 100px;
  display: inline-flex;
  align-items: center;
  border: 3px solid $--color-info-light;
  margin-right: 5px;
  margin-bottom: 5px;
  justify-content: center;
  cursor: pointer;

  img {
    max-width: 100%;
    max-height: 100%;
  }

  &.selected {
    border-color: $--color-primary;
    border-style: dashed;
  }
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
