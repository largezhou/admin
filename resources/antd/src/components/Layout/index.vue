<template>
  <a-layout class="h-100">
    <a-layout-sider
      width="220"
      breakpoint="lg"
      :collapsed-width="miniWidth ? 0 : 80"
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
        <span v-show="!collapsed" class="app-name ml-2">{{ appName }}</span>
      </router-link>
      <side-menu/>
    </a-layout-sider>
    <a-layout>
      <navbar/>
      <a-layout-content
        :style="{
          margin: '24px 16px 0'
        }"
      >
        <div
          :style="{
            padding: '24px',
            background: '#fff',
            minHeight: '360px',
          }"
        >
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
          <h1>content</h1>
        </div>
      </a-layout-content>
    </a-layout>
  </a-layout>
</template>

<script>
import SideMenu from './components/SideMenu'
import { mapGetters, mapState } from 'vuex'
import Navbar from './components/Navbar'
import { getUrl } from '@/libs/utils'
import { SYSTEM_BASIC } from '@/libs/constants'

export default {
  name: 'Layout',
  components: {
    Navbar,
    SideMenu,
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
}

.sider-mini-width {
  position: fixed;
  z-index: 1;
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
  color: #bfcbd9;
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
