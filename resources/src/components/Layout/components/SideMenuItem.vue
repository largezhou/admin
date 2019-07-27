<template>
  <el-submenu
    v-if="hasChildren"
    v-show="filtered"
    :index="routeName"
  >
    <template #title>
      <svg-icon v-if="showIcon" :icon-class="icon"/>
      <span slot="title">{{ menu.title }}</span>
    </template>
    <template v-for="sub of menu.children">
      <side-menu-item
        ref="children"
        :q="q"
        v-if="sub.menu"
        :key="sub.id"
        :menu="sub"
        :level="level + 1"
      />
    </template>
  </el-submenu>
  <a v-else-if="isExternal" :href="menu.path" target="_blank">
    <el-menu-item v-show="filtered" :index="routeName">
      <svg-icon v-if="showIcon" :icon-class="icon"/>
      <div v-else style="width: 24px;"/>
      <span slot="title">{{ menu.title }}</span>
    </el-menu-item>
  </a>
  <router-link v-else :to="path">
    <el-menu-item v-show="filtered" :index="routeName">
      <svg-icon v-if="showIcon" :icon-class="icon"/>
      <div v-else style="width: 24px;"/>
      <span slot="title">{{ menu.title }}</span>
    </el-menu-item>
  </router-link>
</template>

<script>
import { hasChildren, makeRouteName, startSlash } from '@/libs/utils'
import { isExternal } from '@/libs/validates'
import icons from '@/icons'

export default {
  name: 'SideMenuItem',
  props: {
    menu: Object,
    q: String,
    level: {
      type: [Number],
      default: 1,
    },
  },
  computed: {
    filtered() {
      return !this.q ||
        (this.menu.title.indexOf(this.q) !== -1) ||
        (this.$refs.children && this.$refs.children.some((i) => i.filtered))
    },
    hasChildren() {
      return hasChildren(this.menu)
    },
    routeName() {
      return makeRouteName(this.menu.id)
    },
    icon() {
      return this.validIcon ? this.menu.icon : 'cog-fill'
    },
    validIcon() {
      const { icon } = this.menu
      return icon && (icons.indexOf(icon) !== -1)
    },
    showIcon() {
      return (this.level === 1) || this.validIcon
    },
    path() {
      const { path } = this.menu
      return path ? startSlash(path) : ''
    },
    isExternal() {
      return isExternal(this.menu.path)
    },
  },
}
</script>

<style scoped lang="scss">
a {
  text-decoration: none;
}

/deep/ {
  .el-menu-item,
  .el-submenu__title {
    display: flex;
    align-items: center;

    svg {
      width: 24px;
      min-width: 24px;
      height: 16px;
    }
  }
}
</style>
