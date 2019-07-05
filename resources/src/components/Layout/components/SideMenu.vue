<template>
  <div style="height: 100%;">
    <div v-if="miniWidth && !collapse" class="mask" @click="onCollapse"/>
    <el-menu
      ref="menu"
      :default-active="activeName"
      :default-openeds="openedMenus"
      class="side-menu"
      :class="{ 'mini-width': miniWidth }"
      background-color="#304156"
      text-color="#bfcbd9"
      :collapse="collapse"
      @select="onSelect"
    >
      <div class="pa-2">
        <el-input v-model="q" placeholder="搜索菜单"/>
      </div>
      <template v-for="menu of menus">
        <side-menu-item
          :q="q"
          v-if="menu.menu"
          :menu="menu"
          :key="menu.id"
        />
      </template>
    </el-menu>
  </div>
</template>

<script>
import SideMenuItem from './SideMenuItem'
import { mapState } from 'vuex'

export default {
  name: 'SideMenu',
  components: {
    SideMenuItem,
  },
  data() {
    return {
      openedMenus: [],
      q: '',
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
    ...mapState({
      miniWidth: (state) => state.miniWidth,
    }),
  },
  methods: {
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
        // 如果直接用 matched 中的作为默认展开的菜单，
        // 在路由切换时，其他打开的菜单全部会关掉，
        // 所以手动处理一下
        const t = new Set(this.openedMenus)
        newVal.matched.slice(0, -1).forEach(i => {
          i.name && t.add(i.name)
        })
        // this.openedMenus = [...t]
        // 直接上面这样，在首次进入页面时，如果手动展开了一个菜单
        // openedMenus 不会有改变，所以，，，没什么是一个 nextTick 解决不了的？
        this.$nextTick(() => {
          this.openedMenus = [...t]
        })
      },
      immediate: true,
    },
    collapse(newVal) {
      // 菜单从折叠状态展开时，如果没有激活的菜单，
      // 无法根据 default-opened 自动展开菜单，所以手动处理一下
      if (!newVal) {
        this.openedMenus = [...this.openedMenus]
      }
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
