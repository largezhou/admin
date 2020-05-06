<template>
  <a-menu
    :selected-keys="activeNames"
    :open-keys.sync="openedMenus"
    mode="inline"
    theme="dark"
  >
    <template v-for="menu of menus">
      <side-menu-item
        :key="menu.id"
        :menu="menu"
        :q="q"
        v-if="menu.menu"
      />
    </template>
  </a-menu>
</template>

<script>
import { hasChildren, makeRouteName } from '@/libs/utils'
import { mapState } from 'vuex'
import _trimEnd from 'lodash/trimEnd'
import _forIn from 'lodash/forIn'
import SideMenuItem from './SideMenuItem'

export default {
  name: 'SideMenu',
  components: {
    SideMenuItem,
  },
  data() {
    return {
      openedMenusBak: [],
      openedMenus: [],
      menus: this.$store.state.vueRouters.vueRouters,
    }
  },
  props: {
    q: String,
  },
  computed: {
    ...mapState({
      collapsed: (state) => !state.sideMenu.opened,
    }),
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
      this.miniWidth && this.$store.commit('SET_OPENED', false)
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
    activeNames: {
      handler(newVal) {
        if (this.collapsed) {
          return
        }
        this.openedMenus = Array.from(new Set(this.openedMenus.concat(...newVal)))
      },
      immediate: true,
    },
    collapsed: {
      async handler(newVal) {
        await this.$nextTick()
        if (newVal) {
          [this.openedMenusBak, this.openedMenus] = [this.openedMenus, []]
        } else {
          this.openedMenus = [...this.activeNames]
        }
      },
      immediate: true,
    },
  },
}
</script>
