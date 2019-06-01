<template>
  <div
    v-if="hasChildren(menu)"
    :key="menu.id"
    :name="makeRouteName(menu.id)"
  >
    <template slot="title">
      <div :type="icon(menu)"/>
      {{ menu.title }}
    </template>
    <template v-if="hasChildren(menu)">
      <SideMenuItem
        v-for="sub of menu.children.filter(i => i.is_menu)"
        :key="sub.id"
        :menu="sub"
      />
    </template>
  </div>
  <router-link
    v-else
    :key="menu.id"
    :name="makeRouteName(menu.id)"
    :to="makePath(menu.uri)"
  >
    <div :type="icon(menu)"/>
    {{ menu.title }}
  </router-link>
</template>

<script>
import { makeRouteName, startSlash } from '@/libs/utils'

export default {
  name: 'SideMenuItem',
  props: {
    menu: Object,
  },
  computed: {},
  methods: {
    hasChildren(menu) {
      return Array.isArray(menu.children) && menu.children.length > 0
    },
    icon(menu) {
      return menu.icon || 'md-menu'
    },
    makeRouteName,
    makePath: path => path ? startSlash(path) : '',
  },
}
</script>
