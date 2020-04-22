<template>
  <a-layout-header
    class="pa-0 header"
    :style="{ width: `calc(100% - ${siderWidth}px)` }"
  >
    <a-button @click.stop="$store.dispatch('toggleOpened')">
      <a-icon :type="collapsed ? 'menu-unfold' : 'menu-fold'"/>
    </a-button>
    <div class="flex-spacer"/>
    <a-dropdown
      :trigger="['click']"
      placement="bottomRight"
      class="mr-2"
    >
      <a class="ant-dropdown-link" @click="e => e.preventDefault()">{{ user.name }}
        <a-icon type="down"/>
      </a>
      <a-menu slot="overlay">
        <a-menu-item>
          <router-link :to="{ name: 'editMyProfile' }">个人资料</router-link>
        </a-menu-item>
        <a-menu-divider/>
        <a-menu-item @click="onLogout">退出登录</a-menu-item>
      </a-menu>
    </a-dropdown>
  </a-layout-header>
</template>

<script>
import { mapState } from 'vuex'
import { getUrl } from '@/libs/utils'

export default {
  name: 'Navbar',
  props: {
    siderWidth: Number,
  },
  computed: {
    ...mapState({
      miniWidth: (state) => state.miniWidth,
      collapsed: (state) => !state.sideMenu.opened,
      user: (state) => state.users.user,
    }),
    avatar() {
      return this.user.avatar && getUrl(this.user.avatar)
    },
  },
  methods: {
    onLogout() {
      this.$store.dispatch('logout')
    },
  },
}
</script>

<style scoped lang="less">
button {
  line-height: 64px;
  height: 100%;
  border: none;
  border-radius: 0;
  padding: 0 24px;
  font-size: 16px;
}

.header {
  display: flex;
  background: #fff;
  align-items: center;
  position: fixed;
  transition: width 0.2s;
  z-index: 1;
}
</style>
