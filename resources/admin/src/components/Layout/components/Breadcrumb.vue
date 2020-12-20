<template>
  <transition-group class="ant-breadcrumb" tag="div" name="breadcrumb">
    <a-breadcrumb-item v-for="i of breadCrumb" :key="i.id">
      <router-link v-if="i.path" :to="i.path">{{ i.title }}</router-link>
      <span v-else>{{ i.title }}</span>
    </a-breadcrumb-item>
  </transition-group>
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
        ? [...this.matchedMenusChain]
        : this.$route.matched
          .filter((i) => i.name) // 一些过渡中间件，没有 name，也没有 meta 属性
          .map((i) => ({
            id: i.meta.id || randomChars(), // 某些固定配置的路由，没有 ID，比如个人资料编辑页
            title: i.meta.title,
            path: i.path,
          }))

      // 前面加上 “首页” 路由
      if (
        (m.length === 0) ||
        (m[m.length - 1].id !== this.homeRoute.meta.id)
      ) {
        m.unshift({
          ...this.homeRoute.meta,
          path: this.homeRoute.path,
        })
      }

      // 去掉最后一个面包屑的链接
      m[m.length - 1].path = null

      return m
    },
  },
}
</script>

<style lang="less">
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
