<template>
  <el-menu
    :default-active="activeName"
    :default-openeds="openedMenus"
    class="side-menu"
    background-color="#304156"
    text-color="#bfcbd9"
    :collapse="collapse"
    @close="onClose"
    @open="onOpen"
  >
    <template v-for="menu of menus">
      <side-menu-item v-if="menu.menu" :menu="menu" :key="menu.id"/>
    </template>
  </el-menu>
</template>

<script>
import SideMenuItem from './SideMenuItem'
import _forIn from 'lodash/forIn'

export default {
  name: 'SideMenu',
  components: {
    SideMenuItem,
  },
  data() {
    return {
      uniqueOpenedMenus: {},
    }
  },
  props: {
    collapse: Boolean,
  },
  computed: {
    menus() {
      return this.$store.state.vueRouters.vueRouters
    },
    activeName() {
      return this.$route.name
    },
    openedMenus() {
      const res = []
      _forIn(this.uniqueOpenedMenus, (val, key) => {
        val && res.push(key)
      })
      return res
    },
  },
  methods: {
    onClose(index) {
      this.$set(this.uniqueOpenedMenus, index, false)
    },
    onOpen(index) {
      this.$set(this.uniqueOpenedMenus, index, true)
    },
  },
  watch: {
    $route: {
      handler(newVal) {
        newVal.matched.forEach(i => {
          i.name && this.$set(this.uniqueOpenedMenus, i.name, true)
        })
      },
      immediate: true,
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
