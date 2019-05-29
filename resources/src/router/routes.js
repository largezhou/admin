import Main from '@/layouts/Main/index'
import ContentView from '@/layouts/ContentView'
import './imports'

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
    component: () => import('@/views/Login'),
  },
]
