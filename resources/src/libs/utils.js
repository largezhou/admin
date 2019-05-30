import _trimStart from 'lodash/trimStart'
import ParentView from '@/layouts/ParentView'
import Main from '@/layouts/Main/index'
import ContentView from '@/layouts/ContentView'

/**
 * 把 laravel 返回的错误消息，处理成只有一条
 *
 * @param e axios 请求抛出的异常
 */
export const handleValidateErrors = (e) => {
  const res = e.response
  let errors = {}
  if (res && res.status === 422) {
    ({ errors } = res.data)
    Object.keys(errors).forEach((k) => {
      errors[k] = errors[k][0]
    })
  }

  return errors
}

export const buildRoutesFromMenus = (menus, homeName, level = 0) => {
  let homeRoute = null
  const handle = (menus, homeName, level = 0) => {
    const routes = []
    menus.forEach(i => {
      let r = {
        path: i.uri ? startSlash(i.uri) : '',
        name: makeRouteName(i.id),
        meta: {
          title: i.title,
          cache: !!i.cache,
        },
      }

      if (Array.isArray(i.children) && i.children.length > 0) {
        r.component = ParentView
        r.children = handle(i.children || [], homeName, level + 1)
      } else {
        r.component = ContentView
      }

      if (r.name === homeName) {
        homeRoute = r
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
  const routes = handle(menus, homeName, level = 0)
  return {
    routes,
    homeRoute,
  }
}

/**
 * 用后台返回的菜单 id，生成 路由名
 *
 * @param unique menu_id
 * @returns {string}
 */
export const makeRouteName = unique => 'routes-' + unique

/**
 * 给路径前面加上 "/" 符号
 *
 * @param path
 * @returns {string}
 */
export const startSlash = path => '/' + _trimStart(path, '/')
