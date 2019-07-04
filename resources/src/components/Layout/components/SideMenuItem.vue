<template>
  <el-submenu v-if="hasChildren(menu)" :index="makeRouteName(menu.id)">
    <template v-slot:title>
      <i :class="icon(menu.icon)"/>
      <span slot="title">{{ menu.title }}</span>
    </template>
    <template v-for="sub of menu.children">
      <side-menu-item
        v-if="sub.menu"
        :key="sub.id"
        :menu="sub"
      />
    </template>
  </el-submenu>
  <a v-else-if="isExternal(menu.path)" :href="menu.path" target="_blank">
    <el-menu-item>
      <i :class="icon(menu.icon)"/>
      <span slot="title">{{ menu.title }}</span>
    </el-menu-item>
  </a>
  <router-link v-else :to="makePath(menu.path)">
    <el-menu-item :index="makeRouteName(menu.id)">
      <i :class="icon(menu.icon)"/>
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
  },
  methods: {
    hasChildren,
    icon(icon) {
      return icon || 'el-icon-setting'
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
</style>
