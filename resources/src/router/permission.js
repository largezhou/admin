import router from './index'
import { getToken } from '@/libs/token'
import store from '@/store'
import { LoadingBar } from 'iview'

router.beforeEach(async (to, from, next) => {
  LoadingBar.start()
  if (getToken()) {
    if (to.name === 'login') {
      next('/')
    } else {
      if (store.getters.loggedIn) {
        next()
      } else {
        try {
          await store.dispatch('getUser')
        } catch (e) {
          LoadingBar.error()
          return next(false)
        }
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
  LoadingBar.finish()
})
