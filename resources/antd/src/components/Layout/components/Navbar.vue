<template>
  <a-layout-header
    :style="{
      background: '#fff',
      padding: 0,
    }"
  >
    <a-button @click.stop="$store.dispatch('toggleOpened')">
      <a-icon :type="collapsed ? 'menu-unfold' : 'menu-fold'"/>
    </a-button>
  </a-layout-header>
</template>

<script>
import { mapState } from 'vuex'

export default {
  name: 'Navbar',
  computed: {
    ...mapState({
      collapsed: (state) => !state.sideMenu.opened,
    }),
  },
}
</script>

<style scoped lang="less">
button {
  line-height: 64px;
  height: 100%;
  border: none;
  border-radius: 0;
  padding: 0 24px;
  font-size: 16px;
}
</style>
