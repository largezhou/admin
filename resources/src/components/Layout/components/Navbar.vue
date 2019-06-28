<template>
  <el-header class="header">
    <navbar-items>
      <hamburger :is-active="opened" @toggle="$store.dispatch('toggleOpened')"/>
      <refresh/>
      <to-test/>
      <reset-system/>
    </navbar-items>
    <navbar-items v-if="!miniWidth">
      <breadcrumb/>
    </navbar-items>
    <flex-spacer/>
    <navbar-items>
      <el-dropdown trigger="click">
        <el-button type="text">
          {{ user.name }}
          <i class="el-icon-arrow-down el-icon--right"/>
        </el-button>
        <el-dropdown-menu slot="dropdown">
          <el-dropdown-item class="link-item">
            <router-link :to="{ name: 'editMyProfile' }">个人资料</router-link>
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
import Hamburger from '@c/Hamburger'
import NavbarItems from '@c/Layout/components/NavbarItems'
import Refresh from '@c/Refresh'
import ToTest from '@c/ToTest'
import FlexSpacer from '@c/FlexSpacer'
import Breadcrumb from '@c/Layout/components/Breadcrumb'
import ResetSystem from '@c/ResetSystem'

export default {
  name: 'Navbar',
  components: {
    ResetSystem,
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
  },
  methods: {
    async onLogout() {
      this.$store.dispatch('logout')
    },
  },
}
</script>

<style scoped lang="scss">
@import '~@/plugins/element/index';

.header {
  line-height: 60px;
  overflow: hidden;
  position: relative;
  background: #fff;
  box-shadow: $--box-shadow-light;
  padding-left: 0;
  display: flex;
  border-bottom: 1px solid #dcdfe6;
}
</style>
