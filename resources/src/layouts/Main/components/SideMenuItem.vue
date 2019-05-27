<template>
  <Submenu
    v-if="hasChildren(menu)"
    :key="menu.id"
    :name="menu.id"
  >
    <template slot="title">
      <Icon :type="icon(menu)"/>
      {{ menu.title }}
    </template>
    <template v-if="hasChildren(menu)">
      <SideMenuItem
        v-for="sub of menu.children"
        :key="sub.id"
        :name="sub.id"
        :menu="sub"
      />
    </template>
  </Submenu>
  <MenuItem
    v-else
    :key="menu.id"
    :name="menu.id"
    :to="menu.uri || ''"
  >
    <Icon :type="icon(menu)"/>
    {{ menu.title }}
  </MenuItem>
</template>

<script>
export default {
  name: 'SideMenuItem',
  props: {
    menu: Object,
    name: [String, Number],
  },
  methods: {
    hasChildren(menu) {
      return Array.isArray(menu.children) && menu.children.length > 0
    },
    icon(menu) {
      return menu.icon || 'md-menu'
    },
  },
}
</script>

<style scoped>

</style>
