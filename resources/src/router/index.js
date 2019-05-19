import Vue from 'vue'
import Router from 'vue-router'
import Index from '@/views/Index'
import Login from '@/views/Login'

Vue.use(Router)

const login = {
  path: '/login',
  name: 'login',
  component: Login,
}

export default new Router({
  mode: 'history',
  base: 'admin',
  routes: [
    login,
    {
      path: '/',
      meta: {
        auth: true,
      },
      component: Index,
    },
  ],
})
