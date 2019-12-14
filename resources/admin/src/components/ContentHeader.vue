<template>
  <div class="content-header">
    <slot name="title">
      <span>{{ realName }}</span>
    </slot>
    <flex-spacer/>
    <collapse-button-group>
      <slot name="actions"/>
    </collapse-button-group>
  </div>
</template>

<script>
import _get from 'lodash/get'
import FlexSpacer from '@c/FlexSpacer'
import CollapseButtonGroup from '@c/CollapseButtonGroup'
import { mapState } from 'vuex'
import _last from 'lodash/last'

export default {
  name: 'ContentHeader',
  components: {
    CollapseButtonGroup,
    FlexSpacer,
  },
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

<style scoped lang="scss">
.el-card__header {
  .content-header {
    margin-top: -20px;
    margin-bottom: -20px;
    padding: 0;
  }
}

.content-header {
  height: 60px;
  display: flex;
  align-items: center;
  padding: 0 20px;
}
</style>
