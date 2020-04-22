<template>
  <div>
    <div class="content-header">
      <span class="title">{{ realName }}</span>
      <div class="flex-spacer"/>
      <div>
        <slot name="actions"/>
      </div>
    </div>
    <div :class="{ center }">
      <slot/>
    </div>
  </div>
</template>

<script>

import { mapState } from 'vuex'
import _last from 'lodash/last'

export default {
  name: 'TableContent',
  props: {
    title: String,
    center: Boolean,
    scrollX: Boolean,
  },
  created() {
    this.$layout.scrollX = this.scrollX
  },
  computed: {
    ...mapState({
      matchedMenusChain: (state) => state.matchedMenusChain,
    }),
    realName() {
      if (this.title) {
        return this.title
      }

      let title = ''
      if (this.matchedMenusChain.length) {
        title = _last(this.matchedMenusChain).title
      }

      return title || this.$route?.meta?.title || ''
    },
  },
}
</script>

<style scoped lang="less">
.content-header {
  height: 40px;
  display: flex;
  align-items: center;
}

.title {
  font-size: 20px;
  font-weight: 600;
}

.center {
  display: flex;
  justify-content: center;
}
</style>
