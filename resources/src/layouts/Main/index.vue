<template>
  <div class="layout">
    <div class="sider">
      <SideMenu/>
    </div>
    <div class="content-layout">
      <header class="header"/>
      <div class="content">
        <div class="bread-crumb" :test="breadCrumb">
          <div
            v-for="item of breadCrumb"
            :key="item.id"
          >
            {{ item.meta.title }}
          </div>
        </div>
        <div>
          <div style="height: 600px">
            <keep-alive v-if="$route.meta && $route.meta.cache">
              <router-view/>
            </keep-alive>
            <router-view v-else/>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>

import SideMenu from '@/layouts/Main/components/SideMenu'

export default {
  name: 'Main',
  components: {
    SideMenu,
  },
  computed: {
    homeName() {
      return this.$store.getters.homeName
    },
    homeRoute() {
      return this.$store.state.menus.homeRoute
    },
    breadCrumb() {
      const m = this.$route.matched.filter(i => i.name)
      if (this.$route.name !== this.homeName) {
        m.unshift(this.homeRoute)
      }
      return m
    },
  },
}
</script>

<style scoped lang="scss">
.layout {
  background: #f5f7f9;
  position: relative;
  overflow: hidden;
}

.sider {
  position: fixed;
  height: 100vh;
  left: 0;
  overflow: auto;
}

.content-layout {
  margin-left: 200px;
}

.header {
  background: #fff;
  box-shadow: 0 2px 3px 2px rgba(0, 0, 0, .1);
}

.content {
  padding: 0 16px 16px 16px;
}

.bread-crumb {
  margin: 16px 0;
}
</style>
