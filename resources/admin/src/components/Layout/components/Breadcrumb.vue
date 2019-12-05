<template>
  <el-breadcrumb class="breadcrumb">
    <transition-group name="breadcrumb">
      <el-breadcrumb-item
        v-for="i of breadCrumb"
        :key="i.id"
        :to="i.path"
      >
        {{ i.title }}
      </el-breadcrumb-item>
    </transition-group>
  </el-breadcrumb>
</template>

<script>
import { mapState } from 'vuex'
import { randomChars } from '@/libs/utils'

export default {
  name: 'Breadcrumb',
  computed: {
    ...mapState({
      homeRoute: (state) => state.vueRouters.homeRoute,
      matchedMenusChain: (state) => state.matchedMenusChain,
    }),
    homeName() {
      return this.$store.getters.homeName
    },
    breadCrumb() {
      // 如果是匹配了菜单，则用匹配的菜单来显示面包屑导航
      const m = this.matchedMenusChain.length
        ? this.matchedMenusChain
        : this.$route.matched
          .filter((i) => i.name) // 一些过渡中间件，没有 name，也没有 meta 属性
          .map((i) => ({
            id: i.meta.id || randomChars(), // 某些固定配置的路由，没有 ID，比如个人资料编辑页
            title: i.meta.title,
          }))

      if (
        (m.length === 0) ||
        (m[m.length - 1].id !== this.homeRoute.meta.id)
      ) {
        m.unshift({
          ...this.homeRoute.meta,
          path: this.homeRoute.path,
        })
      }

      return m
    },
  },
}
</script>

<style scoped lang="scss">
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

::v-deep {
  .el-breadcrumb__item {
    padding: 2px 0;
  }
}
</style>
