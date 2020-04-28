<template>
  <a-layout class="h-100">
    <a-layout-sider
      :width="fullWidth"
      breakpoint="lg"
      :collapsed-width="collapsedWith"
      :collapsible="true"
      :collapsed="collapsed"
      :trigger="null"
      :class="{ sider: true, 'sider-mini-width': miniWidth }"
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
    <a-layout
      :class="{ 'layout-main': true }"
      :style="{ paddingLeft: `${siderWidth}px`}"
    >
      <a-layout-header class="pa-0">
        <navbar/>
      </a-layout-header>
      <a-layout-content class="py-1 px-2">
        <breadcrumb class="px-2 py-1"/>
        <div class="pa-2" style="background: #fff">
          <transition name="fade-transform" mode="out-in">
            <router-view :key="$route.name"/>
          </transition>
        </div>
      </a-layout-content>
      <a-layout-footer style="text-align: left;">LZ - admin</a-layout-footer>
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
import Vue from 'vue'

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
    siderWidth() {
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
  z-index: 2;
}

.sider-mini-width {
  z-index: 2;
}

.layout-main {
  transition: all 0.2s;
}

.ant-layout-content {
  min-height: auto;
  background: #f0f2f5;
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

.content-main {
  padding-top: 64px;
  display: flex;
  flex-direction: column;
  min-height: 100%;
}

/* fade-transform */
.fade-transform-leave-active,
.fade-transform-enter-active {
  transition: all .3s;
}

.fade-transform-enter {
  opacity: 0;
  transform: translateX(-30px);
}

.fade-transform-leave-to {
  opacity: 0;
  transform: translateX(30px);
}
</style>
