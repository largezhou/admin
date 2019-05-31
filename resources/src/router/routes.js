import Main from '@/layouts/Main/index'
import Page404 from '@v/errors/Page404'

export const pageNotFoundRoute = {
  path: '/',
  component: Main,
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
