<template>
  <Submenu
    v-if="hasChildren(menu)"
    :key="menu.id"
    :name="makeRouteName(menu.id)"
  >
    <template slot="title">
      <Icon :type="icon(menu)"/>
      {{ menu.title }}
    </template>
    <template v-if="hasChildren(menu)">
      <SideMenuItem
        v-for="sub of menu.children"
        :key="sub.id"
        :menu="sub"
      />
    </template>
  </Submenu>
  <MenuItem
    v-else
    :key="menu.id"
    :name="makeRouteName(menu.id)"
    :to="menu.uri || ''"
  >
    <Icon :type="icon(menu)"/>
    {{ menu.title }}
  </MenuItem>
</template>

<script>
import { makeRouteName } from '@/router/routes'

export default {
  name: 'SideMenuItem',
  props: {
    menu: Object,
  },
  methods: {
    hasChildren(menu) {
      return Array.isArray(menu.children) && menu.children.length > 0
    },
    icon(menu) {
      return menu.icon || 'md-menu'
    },
    makeRouteName: unique => makeRouteName(unique),
  },
}
</script>
