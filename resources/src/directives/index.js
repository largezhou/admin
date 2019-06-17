import Vue from 'vue'

// 批量注册 自定义指令
const files = require.context('./', false, /\.js$/i)
files.keys().map(key => {
  if (key === './index.js') {
    return
  }
  Vue.directive(key.split('/').pop().split('.')[0], files(key).default)
})
