import router from './index'
import { getToken } from '@/libs/token'
import store from '@/store'

router.beforeEach(async (to, from, next) => {
  if (getToken()) {
    if (to.name === 'login') {
      next('/')
    } else {
      if (store.getters.loggedIn) {
        next()
      } else {
        await store.dispatch('getUser')
        next()
      }
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
