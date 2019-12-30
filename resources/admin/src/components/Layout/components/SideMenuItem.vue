<script>
import {
  arrayWrap,
  hasChildren,
  makeRouteName,
  startSlash,
} from '@/libs/utils'
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
    showIcon() {
      return (this.level === 1) || this.validIcon
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
  render(h) {
    const {
      showIcon,
      icon,
      filtered,
      routeName,
      hasChildren,
      isExternal,
      q,
      level,
      path,
      menu: { title, children },
    } = this

    const titleSlot = [
      (showIcon
        ? <svg-icon icon-class={icon}/>
        : <div class="icon-placeholder"/>),
      (<span class="title" slot="title" title={title}>{title}</span>),
    ]

    const menuItem = (
      <el-menu-item v-show={filtered} index={routeName}>
        {titleSlot}
      </el-menu-item>
    )

    const subMenus = children.map((i) => (
      i.menu && (
        <side-menu-item
          ref="children"
          refInFor
          q={q}
          key={i.id}
          menu={i}
          level={level + 1}
        />
      )
    ))

    if (hasChildren) {
      return (
        <el-submenu v-show={filtered} index={routeName} popper-class="side-submenu">
          <template slot="title">{titleSlot}</template>
          {subMenus}
        </el-submenu>
      )
    } else if (isExternal) {
      return (
        <a href={path} target="_blank">{menuItem}</a>
      )
    } else {
      return (
        <router-link to={path}>{menuItem}</router-link>
      )
    }
  },
}
</script>

<style scoped lang="scss">
a {
  text-decoration: none;
}

.icon-placeholder {
  min-width: 24px;
  width: 24px;
}

.title {
  overflow: hidden;
  text-overflow: ellipsis;
}

::v-deep {
  .el-menu-item,
  .el-submenu__title {
    display: flex;
    align-items: center;

    .el-tooltip {
      display: inline-flex !important;
      align-items: center;
    }

    svg {
      width: 24px;
      min-width: 24px;
      height: 16px;
    }
  }

  .is-active {
    &.el-menu-item {
      background-color: #409EFF !important;
      color: #fff !important;
    }
  }
}
</style>

<style lang="scss">
.side-submenu {
  max-width: 200px;
  max-height: 400px;
  overflow-y: auto;
  overflow-x: hidden;
}
</style>
