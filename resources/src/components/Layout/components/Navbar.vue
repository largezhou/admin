<template>
  <el-header class="header">
    <navbar-items>
      <hamburger :is-active="opened" @toggle="$store.dispatch('toggleOpened')"/>
      <refresh/>
      <to-test/>
    </navbar-items>
    <navbar-items>
      <el-breadcrumb class="breadcrumb">
        <transition-group name="breadcrumb">
          <el-breadcrumb-item
            v-for="i of breadCrumb"
            :key="i.name"
            :to="breadLink(i)"
          >
            {{ i.meta.title }}
          </el-breadcrumb-item>
        </transition-group>
      </el-breadcrumb>
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
            <router-link to="/index">首页</router-link>
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
import ParentView from '@c/ParentView'
import Hamburger from '@c/Hamburger'
import NavbarItems from '@c/Layout/components/NavbarItems'
import Refresh from '@c/Refresh'
import ToTest from '@c/ToTest'
import FlexSpacer from '@c/FlexSpacer'
import { logout } from '@/api/auth'
import { getMessage } from '@/libs/utils'
import { removeToken } from '@/libs/token'

export default {
  name: 'Navbar',
  components: {
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
      homeRoute: (state) => state.vueRouters.homeRoute,
      user: (state) => state.users.user,
    }),
    homeName() {
      return this.$store.getters.homeName
    },
    breadCrumb() {
      const m = this.$route.matched.filter(i => {
        i.c = i.components.default
        return i.name
      })
      if (this.$route.name !== this.homeName) {
        m.unshift(this.homeRoute)
      }
      return m
    },
  },
  methods: {
    breadLink(route) {
      if (route.c === ParentView) {
        return ''
      } else {
        return route.path
      }
    },
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

/* breadcrumb transition */
.breadcrumb-enter-active,
.breadcrumb-leave-active {
  transition: all .5s;
}

.breadcrumb-enter,
.breadcrumb-leave-active {
  opacity: 0;
  transform: translateX(20px);
}

.breadcrumb-move {
  transition: all .5s;
}

.breadcrumb-leave-active {
  position: absolute;
}
</style>
