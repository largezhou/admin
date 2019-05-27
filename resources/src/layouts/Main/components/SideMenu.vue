<template>
  <div :test="openNames">
    <Menu
      theme="dark"
      width="auto"
      :open-names="openNames"
      :active-name="activeName"
    >
      <template v-for="menu of menus">
        <SideMenuItem
          :key="menu.id"
          :name="menu.id"
          :menu="menu"
        />
      </template>
    </Menu>
  </div>
</template>

<script>
import { menus } from '@/router/routes'
import SideMenuItem from '@/layouts/Main/components/SideMenuItem'

export default {
  name: 'SideMenu',
  components: {
    SideMenuItem,
  },
  computed: {
    menus: () => menus,
    openNames() {
      return this.$route.matched.filter(i => i.name).map(i => i.name)
    },
    activeName() {
      return this.$route.name
    },
  },
}
</script>

<style scoped>

</style>
