import Index from '@/views/Index'
import Login from '@/views/Login'

export default [
  {
    path: '/login',
    name: 'login',
    component: Login,
  },
  {
    path: '/',
    name: 'index',
    meta: {
      auth: true,
    },
    component: Index,
  },
]
