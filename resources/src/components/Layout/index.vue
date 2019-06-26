<template>
  <el-container class="layout">
    <el-aside width="auto">
      <side-menu :collapse="collapse"/>
    </el-aside>
    <el-container direction="vertical">
      <navbar/>
      <el-main>
        <div v-if="miniWidth" class="my-2">
          <breadcrumb/>
        </div>
        <transition name="fade-transform" mode="out-in">
          <template v-if="$route.query._refresh"/>
          <router-view v-else/>
        </transition>
      </el-main>
    </el-container>
  </el-container>
</template>
<script>
import SideMenu from './components/SideMenu'
import Navbar from './components/Navbar'
import { mapState } from 'vuex'
import Breadcrumb from '@c/Layout/components/Breadcrumb'

export default {
  name: 'Layout',
  components: {
    Breadcrumb,
    Navbar,
    SideMenu,
  },
  computed: {
    ...mapState({
      miniWidth: (state) => state.miniWidth,
      collapse: (state) => !state.sideMenu.opened,
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

.el-main {
  padding: 10px;
  overflow-x: hidden;
  overflow-y: scroll;
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
