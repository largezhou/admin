import _trim from 'lodash/trim'
import ParentView from '@c/ParentView'
import Layout from '@c/Layout'
import pages from '@v/pages'
import Page404 from '@v/errors/Page404'
import _debounce from 'lodash/debounce'
import { Message } from 'element-ui'

/**
 * 把 laravel 返回的错误消息，处理成只有一条
 *
 * @param res 响应
 */
export const handleValidateErrors = (res) => {
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

export const buildRoutes = (routers, homeName, level = 0) => {
  let homeRoute = null
  const handle = (routers, homeName, level = 0) => {
    const routes = []
    routers.forEach(i => {
      const pathNoSlash = _trim(i.path, '/')

      let r = {
        path: i.path ? `/${pathNoSlash}` : '',
        name: makeRouteName(i.id),
        meta: {
          title: i.title,
          cache: !!i.cache,
          isMenu: !!i.menu,
          id: i.id,
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
        r.component = pages[pathNoSlash] || Page404
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
  const routes = handle(routers, homeName, level = 0)
  return {
    routes,
    homeRoute,
  }
}

/**
 * 用后台返回的路由 id，生成 路由名
 *
 * @param unique vue_router_id
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
 * 把嵌套数据构建成 select 的缩进形式的 options
 *
 * @param items
 * @param props
 * @returns {Array}
 */
export const nestedToSelectOptions = (items, props = {}) => {
  const defaultProps = {
    id: 'id',
    title: 'title',
    children: 'children',
    firstLevel: {
      id: 0,
      title: '一级',
      text: '一级',
    },
  }
  props = Object.assign({}, defaultProps, props)

  const _build = (items, indent) => {
    const options = []
    items.forEach(i => {
      options.push({
        id: i[props.id],
        text: '　'.repeat(indent) + i[props.title],
        title: i[props.title],
      })
      if (hasChildren(i)) {
        options.push(..._build(i[props.children], indent + 2))
      }
    })
    return options
  }

  // 如果不需要追加顶级选项，则初始缩进为 0
  const res = _build(items, props.firstLevel ? 2 : 0)
  props.firstLevel && res.unshift(props.firstLevel)
  return res
}

/**
 * 复制 source 的值到 target
 *
 * @param target
 * @param source
 * @param force 如果为 true, 则即使 source 中没有的键, 也会复制到 target, 即 undefined
 */
export const assignExists = (target, source, force = false) => {
  const res = {}
  for (let k of Object.keys(target)) {
    if (source.hasOwnProperty(k) || force) {
      res[k] = source[k]
    } else {
      res[k] = target[k]
    }
  }

  return res
}

/**
 * 获取 422 响应中的第一条错误消息
 *
 * @param res 响应
 * @returns {*}
 */
export const getFirstError = (res) => {
  if (res.status === 422) {
    return Object.values(res.data.errors)[0][0]
  } else {
    return ''
  }
}

/**
 * 获取提示消息
 *
 * @param key
 * @returns {string}
 */
export const getMessage = key => {
  /* eslint-disable no-undef */
  return messages[key] || messages.default
}

/**
 * 从嵌套的数据结构中，移除指定项
 *
 * @param items
 * @param identify 作比较的值
 * @param identifyKey 做比较的值的键名，默认为 id
 * @param childrenKey 存储子项目的键名，默认为 children
 * @returns {boolean}
 */
export const removeFromNested = (items, identify, identifyKey = 'id', childrenKey = 'children') => {
  for (let i in items) {
    const item = items[i]
    if (item[identifyKey] === identify) {
      items.splice(i, 1)
      return true
    }

    if (
      hasChildren(item, childrenKey) &&
      removeFromNested(item[childrenKey], identify, identifyKey, childrenKey)
    ) {
      return true
    }
  }
  return false
}

/**
 * 从文件名中获取文件后缀
 * @param filename
 * @returns {*}
 */
export const getExt = (filename) => {
  const t = filename.split('.')
  return t.length <= 1
    ? ''
    : t[t.length - 1]
}

const _debounceMsg = () => {
  const t = _debounceMsg.type || 'error'
  _debounceMsg.msg && (Message[t])(_debounceMsg.msg)
}

const debouncedMsg = _debounce(_debounceMsg, 10)

/**
 * 防抖提示消息
 *
 * @param msg
 * @param type
 */
export const debounceMsg = (msg, type = 'error') => {
  _debounceMsg.msg = msg
  _debounceMsg.type = type
  debouncedMsg()
}
