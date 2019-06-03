<template>
  <el-menu
    :default-active="activeName"
    class="side-menu"
    background-color="#304156"
    text-color="#bfcbd9"
    :collapse="collapse"
  >
    <side-menu-item
      v-for="menu of menus"
      :key="menu.id"
      :menu="menu"
    />
  </el-menu>
</template>

<script>
import SideMenuItem from './SideMenuItem'

export default {
  name: 'SideMenu',
  components: {
    SideMenuItem,
  },
  data() {
    return {}
  },
  props: {
    collapse: Boolean,
  },
  computed: {
    menus() {
      return this.$store.state.vueRouters.vueRouters.filter(i => i.is_menu)
    },
    activeName() {
      return this.$route.name
    },
  },
}
</script>

<style scoped lang="scss">
.side-menu {
  height: 100%;
}

.side-menu:not(.el-menu--collapse) {
  width: 220px;
}
</style>
