import Vue from 'vue'
import ElementUI from 'element-ui'
import './index.scss'

Vue.use(ElementUI, {
  size: 'medium',
})

// 批量注册重写的 element 组件, 这里一定要放到 Vue.use 之后...
const files = require.context('./components', false, /\.js$/i)
files.keys().map(key => Vue.component('El' + key.split('/').pop().split('.')[0], files(key).default))
