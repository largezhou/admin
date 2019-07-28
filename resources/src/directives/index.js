import Vue from 'vue'
import * as vClickOutside from 'v-click-outside-x'

Vue.use(vClickOutside)

/**
 * 窗口 resize 回调
 */
Vue.directive('resize', require('./resize').default)

/**
 * 有所有权限，或者满足所有指定权限，才能显示
 */
Vue.directive('can', require('./can').default)

/**
 * 超级管理员，或者只要是指定角色之一，就可以显示
 */
Vue.directive('role-in', require('./role-in').default)
