import Layout from '@c/Layout'
import Page404 from '@v/errors/Page404'

export const pageNotFoundRoute = {
  path: '/',
  component: Layout,
  children: [
    {
      path: '*',
      component: Page404,
    },
  ],
}

export default [
  {
    path: '/login',
    name: 'login',
    component: () => import('@/views/Login'),
  },
]
