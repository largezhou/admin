import Vue from 'vue'
import SvgIcon from '@c/SvgIcon'
import { requireAll } from '@/libs/utils'
// svg component

// register globally
Vue.component('svg-icon', SvgIcon)

const req = require.context('./svg', false, /\.svg$/)
requireAll(req)

export default req.keys().map((i) => i.split('.')[1].slice(1))
