import Vue from 'vue'
import SvgIcon from '@c/SvgIcon'// svg component

// register globally
Vue.component('svg-icon', SvgIcon)

const req = require.context('./svg', false, /\.svg$/)
const requireAll = requireContext => requireContext.keys().map(requireContext)
requireAll(req)

export default req.keys().map((i) => i.split('.')[1].slice(1))
