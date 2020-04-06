import Layout from '@c/Layout'
import Page404 from '@v/errors/Page404'
import Test from '@v/Test'
import { randomChars } from '@/libs/utils'

const randomPath = '/' + randomChars()

export const pageNotFoundRoute = {
  path: '/',
  component: Layout,
  children: [
    {
      path: '*',
      meta: {
        title: '页面没有找到',
      },
      component: Page404,
    },
  ],
}

/**
 * 后置路由，会添加到后端路由的后面
 */
export const appendRoutes = [
  {
    path: randomPath,
    component: Layout,
    children: [
      {
        path: '/configs/:categorySlug',
        name: 'updateConfigForm',
        component: () => import('@v/configs/ConfigValuesForm'),
      },
    ],
  },
  pageNotFoundRoute,
]

if (process.env.NODE_ENV === 'development') {
  appendRoutes.unshift({
    path: '/test/test',
    component: Layout,
    children: [
      {
        path: '/',
        component: Test,
      },
    ],
  })
}

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
      {
        path: '/permission-test',
        name: 'permissionTest',
        meta: {
          title: '权限测试',
        },
        component: () => import('@v/PermissionTest'),
      },
    ],
  },
]
