<template>
  <div class="content-header">
    <slot name="title">
      <span>{{ name }}</span>
    </slot>
    <flex-spacer/>
    <el-button-group>
      <slot name="actions"/>
    </el-button-group>
  </div>
</template>

<script>
import _get from 'lodash/get'
import FlexSpacer from '@c/FlexSpacer'

export default {
  name: 'ContentHeader',
  components: {
    FlexSpacer,
  },
  computed: {
    name() {
      return _get(this.$route, 'meta.title', '')
    },
  },
}
</script>

<style scoped lang="scss">
.content-header {
  margin-top: -20px;
  margin-bottom: -20px;
  height: 60px;
  display: flex;
  align-items: center;
}
</style>
