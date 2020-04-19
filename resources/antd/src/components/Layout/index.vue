<template>
  <a-layout>
    <a-layout-sider
      :width="fullWidth"
      breakpoint="lg"
      :collapsed-width="collapsedWith"
      :collapsible="true"
      :collapsed="collapsed"
      :trigger="null"
      class="sider"
      :class="{ 'sider-mini-width': miniWidth }"
      v-click-outside="onClickOutside"
    >
      <router-link to="/" class="flex-box logo" :title="appName">
        <span v-if="appLogo" class="flex-box logo-wrapper">
          <img :src="appLogo" class="logo-img">
        </span>
        <span v-show="appLogo && !collapsed" class="ml-2 h-100"/>
        <span v-show="!appLogo || !collapsed" class="app-name">{{ appName }}</span>
      </router-link>
      <side-menu/>
    </a-layout-sider>
    <a-layout class="layout-main" :style="{ paddingLeft: `${layoutWidth}px`}">
      <navbar/>
      <breadcrumb class="mx-2 my-1"/>
      <a-layout-content class="mb-3 mx-2">
        <div class="pa-2" style="background: #fff">
          <router-view/>
        </div>
      </a-layout-content>
      <a-layout-footer style="text-align: center;">Footer</a-layout-footer>
    </a-layout>
  </a-layout>
</template>

<script>
import SideMenu from './components/SideMenu'
import { mapGetters, mapState } from 'vuex'
import Navbar from './components/Navbar'
import { getUrl } from '@/libs/utils'
import { SYSTEM_BASIC } from '@/libs/constants'
import Breadcrumb from './components/Breadcrumb'

export default {
  name: 'Layout',
  components: {
    Navbar,
    SideMenu,
    Breadcrumb,
  },
  computed: {
    ...mapState({
      miniWidth: (state) => state.miniWidth,
      collapsed: (state) => !state.sideMenu.opened,
    }),
    ...mapGetters([
      'appName',
      'getConfig',
    ]),
    appLogo() {
      return getUrl(this.getConfig(SYSTEM_BASIC.SLUG + '.' + SYSTEM_BASIC.APP_LOGO_SLUG))
    },
    collapsedWith() {
      return this.miniWidth ? 0 : 80
    },
    fullWidth() {
      return 220
    },
    layoutWidth() {
      return this.miniWidth ? 0 : (this.collapsed ? this.collapsedWith : this.fullWidth)
    },
  },
  methods: {
    onClickOutside() {
      if (!this.miniWidth || this.collapsed) {
        return
      }
      this.$store.commit('SET_OPENED', false)
    },
  },
}
</script>

<style scoped lang="less">
.sider {
  height: 100vh;
  overflow-y: auto;
  overflow-x: hidden;
  position: fixed;
}

.sider-mini-width {
  z-index: 1;
}

.layout-main {
  transition: all 0.2s;
  min-height: 100vh;
}

.logo {
  height: 64px;
  padding: 0 14px;
  justify-content: left;
}

.app-name {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
  color: #fff;
  font-size: 16px;
  line-height: 36px;
  display: inline-block;
  width: 100%;
}

.logo-img {
  max-width: 100%;
  max-height: 100%;
  border-radius: 4px;
}

.logo-wrapper {
  min-width: 52px;
  width: 52px;
  height: 64px;
  min-height: 64px;
}
</style>
