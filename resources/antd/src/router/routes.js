import Page404 from '@v/errors/Page404'
import { randomChars } from '@/libs/utils'
import Layout from '@c/Layout'

const randomPath = '/' + randomChars()

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
  {
    path: randomPath,
    component: Layout,
    children: [
      {
        path: '/user/edit',
        name: 'editMyProfile',
        meta: {
          title: '编辑资料',
        },
        component: () => import('@v/admin-users/EditProfile'),
      },
    ],
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
