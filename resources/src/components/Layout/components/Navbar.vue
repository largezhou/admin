<template>
  <el-header class="header">
    <hamburger
      class="toggle"
      :is-active="opened"
      @toggle="$store.dispatch('toggleOpened')"
    />
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
  </el-header>
</template>

<script>
import { mapState } from 'vuex'
import ParentView from '@c/ParentView'
import Hamburger from '@c/Hamburger'

export default {
  name: 'Navbar',
  components: {
    Hamburger,
  },
  computed: {
    opened() {
      return this.$store.state.menus.opened
    },
    ...mapState({
      homeRoute: (state) => state.menus.homeRoute,
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
}

.toggle {
  line-height: 54px;
  cursor: pointer;
  /*padding: 0 20px;*/
  display: inline-block;
  /*height: 100%;*/
  font-size: 24px;
  float: left;

  &:hover {
    fill: #409EFF;
  }
}

.breadcrumb {
  float: left;
  display: inline-block;
  font-size: 14px;
  line-height: 60px;
  margin-left: 8px;
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
