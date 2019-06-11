<template>
  <el-header class="header">
    <navbar-items>
      <hamburger :is-active="opened" @toggle="$store.dispatch('toggleOpened')"/>
      <refresh/>
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
  </el-header>
</template>

<script>
import { mapState } from 'vuex'
import ParentView from '@c/ParentView'
import Hamburger from '@c/Hamburger'
import NavbarItems from '@c/Layout/components/NavbarItems'
import Refresh from '@c/Refresh'

export default {
  name: 'Navbar',
  components: {
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
