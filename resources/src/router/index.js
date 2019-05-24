import Vue from 'vue'
import Router from 'vue-router'
import routes from '@/router/routes'
import { LoadingBar } from 'iview'
import { getToken } from '@/libs/token'
import store from '@/store'
import _get from 'lodash/get'

Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: process.env.NODE_ENV === 'development' ? 'admin-dev' : 'admin',
  routes,
})

const loginRoute = to => ({
  name: 'login',
  query: {
    redirect: to.path,
  },
})

const dontNeedAuth = to => to.matched.some(r => _get(r, 'meta.noAuth'))

router.beforeEach(async (to, from, next) => {
  LoadingBar.start()
  if (getToken()) { // 有 token 认为是已登录
    if (to.name === 'login') { // 访问登录页，则跳转到首页
      next('/')
    } else {
      if (store.getters.loggedIn) { // 否则，如果有用户信息，则表示真的已登录，则通过
        next()
      } else { // 否则则获取用户信息
        try {
          await store.dispatch('getUser')
        } catch (e) { // 获取用户信息失败
          if (dontNeedAuth(to)) { // 如果去的页面不需要登录，则通过
            next()
          } else { // 否则跳转到登录页
            LoadingBar.error()
            next(next(loginRoute(to)))
          }
        }
        next() // 获取用户信息成功，通过
      }
    }
  } else if (dontNeedAuth(to)) { // 没有登录，且去的页面不需要登录，则通过
    next()
  } else { // 否则跳转到登录页
    next(loginRoute(to))
  }
  // 某些情况下，不会执行 afterEach 导致进度条不能结束
  LoadingBar.finish()
})

export default router
