import _trim from 'lodash/trim'
import ParentView from '@c/ParentView'
import Layout from '@c/Layout'
import pages from '@v/pages'
import Page404 from '@v/errors/Page404'

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

export const hasChildren =
  (item, childrenKey = 'children') =>
    Array.isArray(item[childrenKey]) && item[childrenKey].length > 0

export const buildRoutes = (menus, homeName, level = 0) => {
  let homeRoute = null
  const handle = (menus, homeName, level = 0) => {
    const routes = []
    menus.forEach(i => {
      const uriNoSlash = _trim(i.uri, '/')

      let r = {
        path: i.uri ? `/${uriNoSlash}` : '',
        name: makeRouteName(i.id),
        meta: {
          title: i.title,
          cache: !!i.cache,
          isMenu: !!i.is_menu,
        },
      }

      if (hasChildren(i)) {
        r.children = handle(i.children || [], homeName, level + 1)
      }

      // 父路由
      if (hasChildren(i)) {
        // 跳转到他第一个子路由
        r.redirect = r.children[0].path
        // 使用过渡组件
        r.component = ParentView
        // 如果没有 path，则随机 path 避免匹配根路径
        r.path = r.path || ('/' + randomChars())
      } else {
        r.component = pages[uriNoSlash] || Page404
      }

      if (r.name === homeName) {
        homeRoute = r
      }

      // 顶级的路由，用 Layout 组件包裹
      if (level === 0) {
        r = {
          path: '/',
          component: Layout,
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
export const startSlash = path => '/' + _trim(path, '/')

export const randomChars = () => Math.random().toString(36).substring(7)

/**
 * 构建菜单的 select options
 *
 * @param menus
 * @param indent
 * @returns {Array}
 */
export const buildMenuOptions = (menus, indent = 2) => {
  const _build = (menus, indent) => {
    const options = []
    menus.forEach(i => {
      if (!i.is_menu) {
        return
      }
      options.push({
        id: i.id,
        text: '　'.repeat(indent) + i.title,
        title: i.title,
      })
      if (hasChildren(i)) {
        options.push(..._build(i.children, indent + 2))
      }
    })
    return options
  }

  const res = _build(menus, indent)
  res.unshift({
    id: 0,
    title: '一级',
    text: '一级',
  })
  return res
}

/**
 * 判断是不是链接
 *
 * @param {string} path
 * @returns {Boolean}
 */
export function isExternal(path) {
  return /^(https?:|mailto:|tel:)/.test(path)
}
