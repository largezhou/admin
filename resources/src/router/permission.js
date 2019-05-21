import router from './index'
import { getToken } from '@/libs/token'

router.beforeEach((to, from, next) => {
  if (getToken()) {
    if (to.name === 'login') {
      next('/')
    } else {
      next()
    }
  } else if (to.matched.some(r => (r.meta && r.meta.auth))) {
    next({
      name: 'login',
      query: {
        redirect: to.path,
      },
    })
  } else {
    next()
  }
})
