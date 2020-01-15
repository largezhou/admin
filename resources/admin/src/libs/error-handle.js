import Axios from 'axios'
import _get from 'lodash/get'
import Vue from 'vue'

/**
 * 判断是不是网络错误
 * @param {Error} err
 */
const isNetworkError = err => {
  return _get(err, 'response.status') ||
    (err.message === 'Network Error') ||
    (err instanceof Axios.Cancel)
}

// 拦截所有没有处理的 Promise 错误，如果是网络错误，则忽略
window.addEventListener('unhandledrejection', function (event) {
  if (isNetworkError(event.reason)) {
    event.preventDefault()
  }
})

// 处理由于网络错误导致的 Vue 中的错误
Vue.config.errorHandler = (err, vm, info) => {
  if (!isNetworkError(err)) {
    throw err
  }
}
