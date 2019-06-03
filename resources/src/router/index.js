import Vue from 'vue'
import Router from 'vue-router'
import routes from '@/router/routes'
import { getToken } from '@/libs/token'
import store from '@/store'

import NProgress from 'nprogress'
import 'nprogress/nprogress.css'

NProgress.configure({ showSpinner: false })

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
  // 刷新页面, 往 query 中加入 _refresh 当前时间戳
  // 然后立马用原页面 replace 掉
  if (to.query._refresh !== undefined) {
    const query = {
      ...to.query,
    }
    delete query._refresh
    next()
    router.replace({
      path: to.path,
      query,
    })
    return
  }

  NProgress.start()

  if (getToken()) { // 有 token 暂定为已登录
    if (to.name === 'login') { // 有 token，访问登录页，跳转到首页
      next('/')
    } else { // 否则应该获取用户信息和路由配置
      const requests = []
      try {
        const loggedIn = store.getters.loggedIn
        const vueRoutersLoaded = store.state.vueRouters.loaded

        !loggedIn && requests.push(store.dispatch('getUser'))
        !vueRoutersLoaded && requests.push(store.dispatch('getVueRouters'))
        await getNeededData(requests)

        // 如果之前没有路由配置，则获取完路由配置后，要重新定位到要去的路由
        // 因为路由配置已经变了
        if (!vueRoutersLoaded) {
          router.replace(to)
        } else {
          next()
        }
      } catch ({ response: res }) {
        if (res && res.status === 401) {
          next(loginRoute(to))
        } else {
          NProgress.done()
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
  NProgress.done()
})

export default router
