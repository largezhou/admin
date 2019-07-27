<template>
  <el-submenu
    v-if="hasChildren(menu)"
    v-show="filtered"
    :index="makeRouteName(menu.id)"
  >
    <template #title>
      <svg-icon :icon-class="icon(menu.icon)"/>
      <span slot="title">{{ menu.title }}</span>
    </template>
    <template v-for="sub of menu.children">
      <side-menu-item
        ref="children"
        :q="q"
        v-if="sub.menu"
        :key="sub.id"
        :menu="sub"
      />
    </template>
  </el-submenu>
  <a v-else-if="isExternal(menu.path)" :href="menu.path" target="_blank">
    <el-menu-item v-show="filtered" :index="makeRouteName(menu.id)">
      <svg-icon :icon-class="icon(menu.icon)"/>
      <span slot="title">{{ menu.title }}</span>
    </el-menu-item>
  </a>
  <router-link v-else :to="makePath(menu.path)">
    <el-menu-item v-show="filtered" :index="makeRouteName(menu.id)">
      <svg-icon :icon-class="icon(menu.icon)"/>
      <span slot="title">{{ menu.title }}</span>
    </el-menu-item>
  </router-link>
</template>

<script>
import { hasChildren, makeRouteName, startSlash } from '@/libs/utils'
import { isExternal } from '@/libs/validates'

export default {
  name: 'SideMenuItem',
  props: {
    menu: Object,
    q: String,
  },
  computed: {
    filtered() {
      return !this.q ||
        (this.menu.title.indexOf(this.q) !== -1) ||
        (this.$refs.children && this.$refs.children.some((i) => i.filtered))
    },
  },
  methods: {
    hasChildren,
    icon(icon) {
      return icon || 'cog-fill'
    },
    makeRouteName,
    makePath: path => path ? startSlash(path) : '',
    isExternal,
  },
}
</script>

<style scoped lang="scss">
a {
  text-decoration: none;
}

/deep/ {
  .el-menu-item,
  .el-submenu__title {
    display: flex;
    align-items: center;

    svg {
      width: 24px;
      min-width: 24px;
      height: 16px;
    }
  }
}
</style>
