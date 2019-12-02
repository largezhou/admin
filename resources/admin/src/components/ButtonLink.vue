<template>
  <router-link
    class="el-button"
    :class="[
      type ? 'el-button--' + type : '',
      size ? 'el-button--' + size : '',
    ]"
    v-bind="$attrs"
  >
    <slot/>
  </router-link>
</template>

<script>
export default {
  name: 'ButtonLink',
  props: {
    type: {
      type: String,
      default: 'default',
    },
    size: {
      type: String,
      default: 'medium',
    },
  },
}
</script>

<style scoped lang="scss">
a.el-button {
  text-decoration: none;
}
</style>
