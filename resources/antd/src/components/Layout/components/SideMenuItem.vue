<script>
import {
  arrayWrap,
  hasChildren as uHasChildren,
  makeRouteName,
  startSlash,
} from '@/libs/utils'
import icons from '@/icons'
import { isExternal as vIsExternal } from '@/libs/validates'

/**
 * 是否没有被过滤掉
 *
 * @param {{}} menu
 * @param {string} q
 *
 * @return {boolean}
 */
const checkNotFiltered = (menu, q) => {
  return !q ||
    (menu.title.indexOf(q) !== -1) ||
    arrayWrap(menu.children).some((i) => checkNotFiltered(i, q))
}

export default {
  functional: true,
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
  render(h, context) {
    const { props: { menu, level, q } } = context

    const notFiltered = checkNotFiltered(menu, q)
    const hasChildren = uHasChildren(menu)
    const routeName = makeRouteName(menu.id)
    const validIcon = icons.indexOf(menu.icon) !== -1
    const icon = validIcon ? menu.icon : 'cog-fill'
    const topLevel = level === 1
    const isExternal = vIsExternal(menu.path)
    const rawPath = menu.path
    const path = isExternal
      ? rawPath
      : rawPath ? startSlash(rawPath) : ''

    const svgNode = topLevel
      ? (
        <i class="anticon anticon-desktop">
          <svg-icon iconClass={icon}/>
        </i>
      )
      : null
    const titleNodes = [svgNode, <span>{menu.title}</span>]

    if (hasChildren) {
      const childrenNodes = menu.children.map((sub) => sub.menu
        ? (
          <side-menu-item
            key={menu.id}
            menu={sub}
            q={q}
            level={level + 1}
          />
        )
        : null,
      )

      return (
        <a-sub-menu v-show={notFiltered} key={routeName}>
          <span slot="title">{titleNodes}</span>
          {childrenNodes}
        </a-sub-menu>
      )
    } else if (isExternal) {
      return (
        <a-menu-item v-show={notFiltered} key={routeName}>
          <a href={path} target="_blank">{titleNodes}</a>
        </a-menu-item>
      )
    } else {
      return (
        <a-menu-item v-show={notFiltered} key={routeName}>
          <router-link to={path}>{titleNodes}</router-link>
        </a-menu-item>
      )
    }
  },
}
</script>

<style scoped lang="less">
.anticon {
  vertical-align: initial;
}
</style>
