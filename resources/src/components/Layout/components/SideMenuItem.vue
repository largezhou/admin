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
  <router-link
    v-else
    :to="makePath(menu.path)"
  >
    <el-menu-item :index="makeRouteName(menu.id)">
      <i :class="icon(menu.icon)"/>
      <span slot="title">{{ menu.title }}</span>
    </el-menu-item>
  </router-link>
</template>

<script>
import { hasChildren, makeRouteName, startSlash } from '@/libs/utils'

export default {
  name: 'SideMenuItem',
  props: {
    menu: Object,
  },
  computed: {},
  methods: {
    hasChildren,
    icon(icon) {
      return icon || 'el-icon-setting'
    },
    makeRouteName,
    makePath: path => path ? startSlash(path) : '',
  },
}
</script>

<style scoped lang="scss">
a {
  text-decoration: none;
}
</style>
