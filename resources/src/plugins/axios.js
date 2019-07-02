import axios from 'axios'
import { getToken } from '@/libs/token'
import { Message } from 'element-ui'
import _trimStart from 'lodash/trimStart'
import { getFirstError, handleValidateErrors } from '@/libs/utils'

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
    const { response: res, config } = err

    if (res) {
      switch (res.status) {
        case 404:
          Message.error('请求的网址不存在')
          break
        case 401:
          Message.error('登录已失效，请重新登录')
          break
        case 400:
          showError(res)
          break
        case 403:
          showError(res)
          Object.values(queue).forEach(i => i.source.cancel('无权访问: ' + res.config.url))
          break
        case 422:
          if (config.showValidationMsg) { // 如果显示验证消息，则显示首条
            Message.error(getFirstError(res))
          } else if (config.validationForm) { // 如果传入了 Vue 实例，则维护实例中的 errors
            config.validationForm[config.validationErrorKey || 'errors'] = handleValidateErrors(res)
          } // 否则不做处理
          break
        default:
          Message.error(`服务器异常(code: ${res.status})`)
          break
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

class Request {
  method

  url
  data
  _config

  methodsWithData = [
    'put', 'patch', 'post',
  ]

  withData() {
    return this.methodsWithData.indexOf(this.method) !== -1
  }

  config(config = {}) {
    this._config = Object.assign({}, this._config, config)
    return this
  }

  async request() {
    let args = []
    if (this.withData()) {
      args = [this.url, this.data, this._config]
    } else {
      args = [this.url, this._config]
    }
    return _axios[this.method](...args)
  }

  then(resolve, reject) {
    try {
      resolve(this.request())
    } catch (e) {
      reject(e)
    }
  }

  static create(method) {
    return class extends Request {
      constructor(url, data, config) {
        super()
        this.method = method
        this.url = url
        if (this.withData()) {
          this.data = data
          this._config = config
        } else {
          this._config = data
        }
      }
    }
  }
}

const requestMethods = ['request', 'delete', 'get', 'head', 'options', 'post', 'put', 'patch']
const requests = {}
requestMethods.forEach((method) => {
  requests[method] = function () {
    return new (Request.create(method))(...arguments)
  }
})

export default requests
