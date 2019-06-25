import axios from 'axios'
import { getToken } from '@/libs/token'
import store from '@/store'
import { Message } from 'element-ui'
import _trimStart from 'lodash/trimStart'

let config = {
  baseURL: '/admin-api',
  timeout: 30 * 1000,
}

const _axios = axios.create(config)
const CancelToken = axios.CancelToken

const queue = {}
const destroyUrlFromQueue = path => {
  path = path.slice('/admin-api/'.length)
  delete queue[path]
}

const showError = res => {
  const { message: msg } = res.data
  msg && Message.error(msg)
}

_axios.interceptors.request.use(
  (config) => {
    config.headers.Authorization = getToken()

    const source = CancelToken.source()
    config.cancelToken = source.token

    queue[_trimStart(config.url, '/')] = {
      source,
    }

    return config
  },
  (error) => {
    return Promise.reject(error)
  },
)

_axios.interceptors.response.use(
  (res) => {
    destroyUrlFromQueue(res.config.url)
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
          store.dispatch('feLogout')
          Message.error('登录已失效，请重新登录')
          break
        case 400:
          showError(res)
          break
        case 403:
          showError(res)
          Object.values(queue).forEach(i => i.source.cancel('无权访问: ' + res.config.url))
          break
        case 500:
          Message.error('服务器异常')
          break
        default:
        // null
      }
    } else {
      if (err instanceof axios.Cancel) { // 手动取消时，err 为一个 Cancel 对象，有一个 message 属性
        console.log(err.toString())
      } else {
        Message.error('网络异常')
      }
    }
    destroyUrlFromQueue(err.config.url)
    return Promise.reject(err)
  },
)

export default _axios
