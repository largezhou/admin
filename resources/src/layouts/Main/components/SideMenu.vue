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
          :menu="menu"
        />
      </template>
    </Menu>
  </div>
</template>

<script>
import SideMenuItem from '@/layouts/Main/components/SideMenuItem'

export default {
  name: 'SideMenu',
  components: {
    SideMenuItem,
  },
  computed: {
    menus() {
      return this.$store.state.menus.menus.filter(i => i.is_menu)
    },
    openNames() {
      return this.$route.matched.filter(i => i.name).map(i => i.name)
    },
    activeName() {
      return this.$route.name
    },
  },
}
</script>
