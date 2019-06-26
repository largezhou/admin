<template>
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
</template>

<script>
import ParentView from '@c/ParentView'
import { mapState } from 'vuex'

export default {
  name: 'Breadcrumb',
  computed: {
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
