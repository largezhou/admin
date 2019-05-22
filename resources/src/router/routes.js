import Main from '@/layouts/Main'

export default [
  {
    path: '/login',
    name: 'login',
    meta: {
      noAuth: true,
    },
    component: () => import('@/views/Login'),
  },
  {
    path: '/',
    name: 'xxx',
    redirect: '/index',
    component: Main,
    children: [
      {
        path: '/index',
        name: 'index',
        component: () => import('@/views/Index'),
      },
    ],
  },
]
