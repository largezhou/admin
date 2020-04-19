<template>
  <div class="content-header">
    <span class="title">{{ realName }}</span>
    <div class="flex-spacer"/>
    <div>
      <slot name="actions"/>
    </div>
  </div>
</template>

<script>
import _get from 'lodash/get'
import { mapState } from 'vuex'
import _last from 'lodash/last'

export default {
  name: 'ContentHeader',
  props: {
    name: String,
  },
  computed: {
    ...mapState({
      matchedMenusChain: (state) => state.matchedMenusChain,
    }),
    realName() {
      if (this.name) {
        return this.name
      }

      let title = ''
      if (this.matchedMenusChain.length) {
        title = _last(this.matchedMenusChain).title
      }

      return title || _get(this.$route, 'meta.title', '')
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
</style>
