<template>
  <div style="height: 100%;">
    <div v-show="miniWidth && !collapse" class="mask" @click="onCollapse"/>
    <el-menu
      ref="menu"
      :default-active="activeName"
      :default-openeds="activeNames"
      class="side-menu"
      :class="{ 'mini-width': miniWidth }"
      background-color="#304156"
      text-color="#bfcbd9"
      :collapse="collapse"
      @select="onSelect"
      unique-opened
    >
      <side-menu-title class="px-2 pt-2" :collapse="collapse"/>
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
import SideMenuTitle from '@c/Layout/components/SideMenuTitle'

export default {
  name: 'SideMenu',
  components: {
    SideMenuTitle,
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
      return this.activeNames[this.activeNames.length - 1] || null
    },
    activeNames() {
      return this.matchedMenu
        ? this.matchedMenusChain.map((i) => makeRouteName(i.id))
        : this.$route.matched
          .filter((i) => i.meta && i.meta.id)
          .map((i) => makeRouteName(i.meta.id))
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

      const notMatched = '-99999'

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
            } else { // 如果当前 query 中没有键，或者有键但是值不相同，则不能匹配，直接设为足够小，并中断
              weight = notMatched
              return false
            }
          })

          if (weightMatchedMap[weight] === undefined) {
            weightMatchedMap[weight] = i
          }
        }
      })

      // 返回权重最大的，作为匹配值
      const maxWeight = Object.keys(weightMatchedMap).sort((a, b) => b - a)[0]
      if (maxWeight === notMatched) {
        return null
      } else {
        return weightMatchedMap[maxWeight] || null
      }
    },
    /**
     * 通过 path，query 来对比过后匹配到的菜单的菜单链
     */
    matchedMenusChain() {
      const t = []
      let menu = this.matchedMenu
      while (menu) {
        t.unshift(menu)
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
  width: 100%;
  top: 0;
  height: 100%;
  position: absolute;
  z-index: 2001;
}

::v-deep {
  .el-submenu {
    .el-menu-item {
      padding-right: 20px;
    }

    .el-submenu__title {
      padding-right: 34px;
    }

    &.is-active {
      .el-submenu__title {
        color: #fff !important;
      }
    }
  }
}
</style>
