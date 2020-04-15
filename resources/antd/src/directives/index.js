import Vue from 'vue'
import * as vClickOutside from 'v-click-outside-x'

Vue.use(vClickOutside)

/**
 * 窗口 resize 回调
 */
Vue.directive('resize', require('./resize').default)
