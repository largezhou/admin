<template>
  <div v-if="hasChildren" :class="{ collapse: collapse && topLevel }">
    <el-submenu v-if="hasChildren" v-show="filtered" :index="routeName">
      <template #title>
        <svg-icon v-if="showIcon" class="el-icon-menu" :icon-class="icon"/>
        <span>{{ menu.title }}</span>
      </template>
      <template v-for="subMenu of menu.children">
        <side-menu-item
          ref="children"
          :q="q"
          v-if="subMenu.menu"
          :menu="subMenu"
          :key="subMenu.id"
          :level="level + 1"
          :collapse="collapse"
        />
      </template>
    </el-submenu>
  </div>
  <a v-else-if="isExternal" :href="path" target="_blank">
    <el-menu-item v-show="filtered" :index="routeName">
      <svg-icon v-if="showIcon" class="el-icon-menu" :icon-class="icon"/>
      <template #title>{{ menu.title }}</template>
    </el-menu-item>
  </a>
  <router-link v-else :to="path">
    <el-menu-item v-show="filtered" :index="routeName">
      <svg-icon v-if="showIcon" class="el-icon-menu" :icon-class="icon"/>
      <template #title>{{ menu.title }}</template>
    </el-menu-item>
  </router-link>
</template>

<script>
import {
  arrayWrap,
  hasChildren,
  makeRouteName,
  startSlash,
} from '@/libs/utils'
import icons from '@/icons'
import { isExternal } from '@/libs/validates'

export default {
  name: 'SideMenuItem',
  props: {
    menu: Object,
    q: String,
    level: {
      type: [Number],
      default: 1,
    },
    collapse: Boolean,
  },
  computed: {
    filtered() {
      return !this.q ||
        (this.menu.title.indexOf(this.q) !== -1) ||
        // 用渲染函数写的，在子元素只有一个时，children 不是数组，，，所以，包裹一下
        (this.$refs.children && arrayWrap(this.$refs.children).some((i) => i.filtered))
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
    topLevel() {
      return this.level === 1
    },
    showIcon() {
      return this.topLevel || this.validIcon
    },
    path() {
      const { path } = this.menu
      if (this.isExternal) {
        return path
      } else {
        return path ? startSlash(path) : ''
      }
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

.collapse {
  ::v-deep {
    .el-submenu__title {
      span {
        overflow: hidden;
        visibility: hidden;
        display: inline-block;
        width: 0;
        height: 0;
      }
    }
  }
}
</style>
