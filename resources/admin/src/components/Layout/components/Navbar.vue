<template>
  <el-header class="header">
    <navbar-items>
      <hamburger :is-active="opened" @toggle="$store.dispatch('toggleOpened')"/>
      <refresh/>
      <to-test/>
    </navbar-items>
    <navbar-items v-if="!miniWidth">
      <breadcrumb/>
    </navbar-items>
    <flex-spacer/>
    <navbar-items>
      <el-dropdown trigger="click">
        <div class="avatar flex-box">
          <img
            v-if="avatar"
            :src="avatar"
            :alt="user.name"
            :title="user.name"
          >
          <span class="username" v-else>{{ user.name }}</span>
          <i class="el-icon-arrow-down el-icon--right"/>
        </div>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item class="button-item">
            <button-link :to="{ name: 'editMyProfile' }">个人资料</button-link>
          </el-dropdown-item>
          <el-dropdown-item class="button-item">
            <el-button type="default" @click="onLogout">退出</el-button>
          </el-dropdown-item>
        </el-dropdown-menu>
      </el-dropdown>
    </navbar-items>
  </el-header>
</template>

<script>
import { mapState } from 'vuex'
import Hamburger from './Hamburger'
import NavbarItems from '@c/Layout/components/NavbarItems'
import Refresh from '@c/Refresh'
import ToTest from '@c/ToTest'
import FlexSpacer from '@c/FlexSpacer'
import Breadcrumb from '@c/Layout/components/Breadcrumb'
import ButtonLink from '@c/ButtonLink'
import { getUrl } from '@/libs/utils'

export default {
  name: 'Navbar',
  components: {
    ButtonLink,
    Breadcrumb,
    FlexSpacer,
    ToTest,
    Refresh,
    NavbarItems,
    Hamburger,
  },
  computed: {
    opened() {
      return this.$store.state.sideMenu.opened
    },
    ...mapState({
      user: (state) => state.users.user,
      miniWidth: (state) => state.miniWidth,
    }),
    homeName() {
      return this.$store.getters.homeName
    },
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

<style scoped lang="scss">
@import '~element-ui/packages/theme-chalk/src/common/var';

.header {
  overflow: hidden;
  z-index: 1;
  background: #fff;
  box-shadow: $--box-shadow-light;
  padding: 0;
  display: flex;
}

.avatar {
  color: $--color-primary;
  cursor: pointer;
  height: 100%;

  img {
    border-radius: $--border-radius-base;
    max-width: 40px;
    max-height: 40px;
  }
}

.username {
  white-space: nowrap;
}
</style>
