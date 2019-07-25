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
import { hasChildren, makeRouteName } from '@/libs/utils'
import _trimEnd from 'lodash/trimEnd'
import _forIn from 'lodash/forIn'

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
      const exact = this.matchedMenu
      return exact
        ? makeRouteName(exact.id)
        : this.$route.name
    },
    ...mapState({
      miniWidth: (state) => state.miniWidth,
    }),
    /**
     * 通过 path，query 来对比过后匹配到的菜单
     */
    matchedMenu() {
      const current = this.$route
      const curPath = _trimEnd(current.path, '/')
      const curQuery = current.query

      // 所有匹配到的，数据结构为 { 权重: 菜单对象 }
      // 例如当前的 query 为 '?a=1&b=1'
      // 菜单中有 ['?', '?a=1', '?b=1', '?b=1&a=1', '?a=1&c=1']
      // 则各个菜单的匹配权重为 [0, 1, 1, 2, -99999]
      const weightMatchedMap = {}

      this.pathsWithPreSlash.forEach((i) => {
        const path = _trimEnd(i.route.path, '/')
        const query = i.route.query

        if (path === curPath) {
          let weight = 0
          // 比较 query 中的键值
          _forIn(query, (value, key) => {
            if (curQuery[key] === value) { // 键值相等，则 +1
              weight++
            } else { // 如果当前 query 中没有键，或者有键但是值不相同，则不能匹配，减去足够大
              weight -= 99999
            }
          })

          if (weightMatchedMap[weight] === undefined) {
            weightMatchedMap[weight] = i
          }
        }
      })

      // 返回权重最大的，作为匹配值
      const maxWeight = Object.keys(weightMatchedMap).sort((a, b) => b - a)[0]
      return weightMatchedMap[maxWeight]
    },
    /**
     * 通过 path，query 来对比过后匹配到的菜单的菜单链
     */
    matchedMenusChain() {
      const t = []
      let menu = this.matchedMenu
      while (menu) {
        t.push(menu)
        menu = menu.parent
      }
      return t
    },
    /**
     * 所有 path 中以 '/' 开头的菜单，用来做匹配
     */
    pathsWithPreSlash() {
      return this.getPathsWithPreSlash()
    },
  },
  methods: {
    onCollapse() {
      this.$store.commit('SET_OPENED', false)
    },
    onSelect() {
      this.miniWidth && this.onCollapse()
    },
    /**
     * 获取所有 path 以 '/' 开头的菜单
     *
     * @param menus
     * @param parent
     * @return {Array}
     */
    getPathsWithPreSlash(menus = this.menus, parent = null) {
      let t = []

      menus.forEach((i) => {
        const route = (i.path && i.path.indexOf('/') === 0) ? this.$router.resolve(i.path).route : null
        const data = {
          ...i,
          route,
          parent,
        }
        route && t.push(data)

        if (hasChildren(i)) {
          t = t.concat(this.getPathsWithPreSlash(i.children, data))
        }
      })

      return t
    },
  },
  watch: {
    $route: {
      handler(newVal) {
        // 如果直接用 matched 中的作为默认展开的菜单，
        // 在路由切换时，其他打开的菜单全部会关掉，
        // 所以手动处理一下
        const t = new Set(this.openedMenus)
        if (this.matchedMenu) {
          this.matchedMenusChain.slice(1).forEach((i) => {
            t.add(makeRouteName(i.id))
          })
        } else {
          newVal.matched.slice(0, -1).forEach(i => {
            i.name && t.add(i.name)
          })
        }

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
    matchedMenusChain: {
      handler(newVal) {
        this.$store.commit('SET_MATCHED_MENUS_CHAIN', newVal)
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

/deep/ {
  .el-submenu .el-menu-item {
    padding-right: 20px;
  }
}
</style>
