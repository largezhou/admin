<template>
  <el-container class="layout">
    <el-aside width="auto">
      <side-menu :collapse="collapse"/>
    </el-aside>
    <el-container direction="vertical">
      <navbar/>
      <el-main>
        <transition name="fade-transform" mode="out-in">
          <router-view :key="$route.name"/>
        </transition>
      </el-main>
    </el-container>
  </el-container>
</template>
<script>

import SideMenu from './components/SideMenu'
import Navbar from './components/Navbar'
import { mapState } from 'vuex'

export default {
  name: 'Layout',
  components: {
    Navbar,
    SideMenu,
  },
  computed: {
    ...mapState({
      collapse: (state) => !state.menus.opened,
    }),
  },
}
</script>

<style scoped lang="scss">
.layout {
  height: 100%;
}

.el-aside {
  color: #333;
}

/* fade-transform */
.fade-transform-leave-active,
.fade-transform-enter-active {
  transition: all .4s;
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
