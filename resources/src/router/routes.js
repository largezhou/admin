import Main from '@/layouts/Main/index'
import ContentView from '@/layouts/ContentView'
import ParentView from '@/layouts/ParentView'
import './imports'

export const makeRouteName = unique => `route-${unique}`
export const homeName = makeRouteName(1)
export const menus = [
  {
    id: 1,
    uri: '/index',
    title: '首页',
  },
  {
    id: 2,
    uri: '/configs',
    title: '所有配置',
  },
  {
    id: 3,
    title: '用户管理',
    children: [
      {
        id: 4,
        uri: '/users',
        title: '用户列表',
      },
      {
        id: 5,
        uri: '/users/create',
        title: '添加用户',
      },
    ],
  },
  {
    id: 7,
    title: '后台设置',
    children: [
      {
        id: 8,
        title: '菜单管理',
        children: [
          {
            id: 9,
            title: '所有菜单',
            uri: '/menus',
            cache: true,
          },
          {
            id: 10,
            title: '添加菜单',
            uri: '/menus/create',
          },
        ],
      },
    ],
  },
  {
    id: 6,
    uri: '/posts',
    title: '帖子列表',
    cache: true,
  },
]

let _homeRoute = null
const buildRoutesFromMenus = (menus, level = 0) => {
  const routes = []
  menus.forEach(i => {
    let r = {
      path: i.uri || '',
      name: makeRouteName(i.id),
      meta: {
        title: i.title,
        cache: i.cache,
      },
    }

    if (Array.isArray(i.children) && i.children.length > 0) {
      r.component = ParentView
      r.children = buildRoutesFromMenus(i.children || [], level + 1)
    } else {
      r.component = ContentView
    }

    if (r.name === homeName) {
      _homeRoute = r
    }

    if (level === 0) {
      r = {
        path: '/',
        component: Main,
        children: [r],
      }
      if (homeName) {
        r.redirect = { name: homeName }
      }
      routes.push(r)
    } else {
      routes.push(r)
    }
  })
  return routes
}

export const menuRoutes = buildRoutesFromMenus(menus)
export const homeRoute = _homeRoute

export const anyRoute = {
  path: '/',
  component: Main,
  children: [
    {
      path: '*',
      component: ContentView,
    },
  ],
}

export default [
  {
    path: '/login',
    name: 'login',
    meta: {
      noAuth: true,
    },
    component: () => import('@/views/Login'),
  },
]
