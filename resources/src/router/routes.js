import Layout from '@c/Layout'
import Page404 from '@v/errors/Page404'
import Test from '@v/Test'
import { randomChars } from '@/libs/utils'

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

export const fixedRoutes = [
  pageNotFoundRoute,
]

if (process.env.NODE_ENV === 'development') {
  fixedRoutes.unshift({
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
    path: '/' + randomChars(),
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
