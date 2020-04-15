import Page404 from '@v/errors/Page404'
import { randomChars } from '@/libs/utils'
import Layout from '@c/Layout'

/**
 * 前置路由，会添加到后端路由前面
 */
export default [
  {
    path: '/login',
    name: 'login',
    meta: {
      title: '登录',
    },
    component: () => import('@v/Login'),
  },
]

export const pageNotFoundRoute = {
  path: '*',
  meta: {
    title: '页面没有找到',
  },
  component: Page404,
}

/**
 * 后置路由，会添加到后端路由的后面
 */
export const appendRoutes = [
  pageNotFoundRoute,
]
