import axios from 'axios'
import { getToken } from '@/libs/token'
import { Message } from 'iview'

let config = {
  baseURL: '/admin-api',
  timeout: 60 * 1000,
}

const _axios = axios.create(config)

_axios.interceptors.request.use(
  (config) => {
    config.headers.Authorization = getToken()
    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

_axios.interceptors.response.use(
  (res) => {
    return res
  },
  (err) => {
    const res = err.response

    if (res) {
      switch (res.status) {
        case 404:
          Message.error('请求的网址不存在')
          break
        case 401:
          // store.dispatch('frontendLogout')
          Message.error('登录已失效')
          break
        case 400:
          const { message: msg } = res.data
          msg && Message.error(msg)
          break
        case 500:
          Message.error('服务器异常')
          break
        default:
        // null
      }
    } else {
      Message.error('网络异常')
    }
    return Promise.reject(err)
  },
)

export default _axios
