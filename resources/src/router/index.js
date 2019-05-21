import Vue from 'vue'
import Router from 'vue-router'
import routes from '@/router/routes'
import { LoadingBar } from 'iview'
import { getToken } from '@/libs/token'
import store from '@/store'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: 'admin',
  routes,
})

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

export default router
