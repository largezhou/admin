<template>
  <div style="height: 100%;">
    <div v-if="miniWidth && !collapse" class="mask" @click="onCollapse"/>
    <el-menu
      :default-active="activeName"
      :default-openeds="openedMenus"
      class="side-menu"
      :class="{ 'mini-width': miniWidth }"
      background-color="#304156"
      text-color="#bfcbd9"
      :collapse="collapse"
      @close="onClose"
      @open="onOpen"
      @select="onSelect"
    >
      <template v-for="menu of menus">
        <side-menu-item v-if="menu.menu" :menu="menu" :key="menu.id"/>
      </template>
    </el-menu>
  </div>
</template>

<script>
import SideMenuItem from './SideMenuItem'
import _forIn from 'lodash/forIn'
import { mapState } from 'vuex'

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
    ...mapState({
      miniWidth: (state) => state.miniWidth,
    }),
  },
  methods: {
    onClose(index) {
      this.$set(this.uniqueOpenedMenus, index, false)
    },
    onOpen(index) {
      this.$set(this.uniqueOpenedMenus, index, true)
    },
    onCollapse() {
      this.$store.commit('SET_OPENED', false)
    },
    onSelect() {
      this.miniWidth && this.onCollapse()
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
  border: none;
  height: 100%;
}

.side-menu:not(.el-menu--collapse) {
  width: 220px;
}

.mini-width {
  position: fixed;
  z-index: 2001; // 比 页面的 loading 层高一点
  left: 0;
  top: 0;
  bottom: 0;
  overflow-y: auto;

  &.el-menu--collapse {
    width: 0;

    > * {
      display: none;
    }
  }
}

.mask {
  background: #000;
  opacity: .3;
  width: 100%;
  top: 0;
  height: 100%;
  position: absolute;
  z-index: 2001;
}
</style>
