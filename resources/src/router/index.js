import Vue from 'vue'
import Router from 'vue-router'
import routes from '@/router/routes'
import { getToken } from '@/libs/token'
import store from '@/store'

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

const getNeededData = async requests => {
  await Promise.all(requests)
}

router.beforeEach(async (to, from, next) => {
  log('router start')

  if (getToken()) { // 有 token 暂定为已登录
    if (to.name === 'login') { // 有 token，访问登录页，跳转到首页
      next('/')
    } else { // 否则应该获取用户信息和菜单
      const requests = []
      try {
        const loggedIn = store.getters.loggedIn
        const menusLoaded = store.state.menus.loaded

        !loggedIn && requests.push(store.dispatch('getUser'))
        !menusLoaded && requests.push(store.dispatch('getMenus'))
        await getNeededData(requests)

        // 如果之前没有菜单，则获取玩菜单后，要重新定位到要去的路由
        // 因为路由配置已经变了
        if (!menusLoaded) {
          router.replace(to)
        } else {
          next()
        }
      } catch ({ response: res }) {
        if (res && res.status === 401) {
          next(loginRoute(to))
        } else {
          log('router error')
          next(false)
        }
      }
    }
  } else if (to.name !== 'login') { // 没 token 访问后台，跳到登录页
    next(loginRoute(to))
  } else { // 没 token 访问登录页，通过
    next()
  }
})

router.afterEach(() => {
  log('router done')
})

export default router
