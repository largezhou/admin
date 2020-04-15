<script>
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
    const {
      arrayWrap,
      hasChildren: utilsHasChildren,
      makeRouteName,
      startSlash,
    } = require('@/libs/utils')
    const icons = require('@/icons').default
    const { isExternal: vIsExternal } = require('@/libs/validates')

    const { props: { menu, level, q } } = context

    const hasChildren = utilsHasChildren(menu)
    const routeName = makeRouteName(menu.id)
    const validIcon = icons.indexOf(menu.icon) !== -1
    const icon = validIcon ? menu.icon : 'cog-fill'
    const topLevel = level === 1
    const isExternal = vIsExternal(menu.path)
    const rawPath = menu.path
    const path = isExternal
      ? rawPath
      : rawPath ? startSlash(rawPath) : ''

    const svgEl = topLevel
      ? (<i class="anticon anticon-desktop">
        <svg-icon iconClass={icon}/>
      </i>)
      : null

    if (hasChildren) {
      return (
        <a-sub-menu key={routeName}>
          <span slot="title">
            {svgEl}
            <span>{menu.title}</span>
          </span>
          {
            menu.children.map((subMenu) => subMenu.menu
              ? (
                <side-menu-item
                  key={menu.id}
                  menu={subMenu}
                  q={q}
                  level={level + 1}
                />
              )
              : null,
            )
          }
        </a-sub-menu>
      )
    } else if (isExternal) {
      return (
        <a-menu-item key={routeName}>
          <a href={path} target="_blank">
            {svgEl}
            <span>{menu.title}</span>
          </a>
        </a-menu-item>
      )
    } else {
      return (
        <a-menu-item key={routeName}>
          <router-link to={path}>  {svgEl}
            <span>{menu.title}</span>
          </router-link>
        </a-menu-item>
      )
    }
  },
}
</script>
