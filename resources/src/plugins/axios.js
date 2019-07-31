import axios from 'axios'
import { getToken, removeToken } from '@/libs/token'
import { Message, MessageBox } from 'element-ui'
import _trimStart from 'lodash/trimStart'
import { debounceMsg, getFirstError, handleValidateErrors } from '@/libs/utils'
import _forIn from 'lodash/forIn'
import router from '@/router'

let config = {
  baseURL: '/admin-api',
  timeout: 30 * 1000,
}

const _axios = axios.create(config)
const CancelToken = axios.CancelToken

export const requestQueue = {}
const destroyUrlFromQueue = (path) => {
  if (!path) {
    return
  }
  path = path.slice('/admin-api/'.length)
  delete requestQueue[path]
}

const showError = res => {
  const { message: msg } = res.data
  msg && Message.error(msg)
}

export const cancelAllRequest = (msg = '') => {
  Object.values(requestQueue).forEach(i => i.source.cancel(msg))
  _forIn(requestQueue, (value, url) => {
    value.source.cancel(msg)
    delete requestQueue[url]
  })
}

_axios.interceptors.request.use(
  (config) => {
    config.headers.Authorization = getToken()

    const source = CancelToken.source()
    config.cancelToken = source.token

    requestQueue[_trimStart(config.url, '/')] = {
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
    const { response: res } = err
    let { config } = err

    config = config && Object.assign({
      // 是否以 message 的方式显示第一条 422 错误消息
      showValidationMsg: undefined,
      // 请求时的表单所在的组件，配合 error key 使用，可以自动填充表单中的 errors 错误提示
      validationForm: undefined,
      // 页面中，存储错误的键，默认为 errors
      validationErrorKey: 'errors',
      // 请求出现 401 时，默认会打开弹框要求登录，设置 true 来禁止
      disableLoginDialog: undefined,
    }, config)

    if (res) {
      switch (res.status) {
        case 404:
          Message.error('请求的网址不存在')
          break
        case 401:
          cancelAllRequest('登录失效: ' + config.url)
          if (!config.disableLoginDialog) {
            MessageBox.confirm('登录已失效，是否重新登录？', '登录失效')
              .then(() => {
                removeToken()
                window.location.href = router.resolve({ name: 'login' }).href
              })
          } else {
            Message.error('登录已失效，请重新登录')
          }
          break
        case 400:
          showError(res)
          break
        case 403:
          showError(res)
          cancelAllRequest('无权访问: ' + config.url)
          break
        case 422:
          if (config.showValidationMsg) { // 如果显示验证消息，则显示首条
            Message.error(getFirstError(res))
          } else if (config.validationForm) { // 如果传入了 Vue 实例，则维护实例中的 errors
            config.validationForm[config.validationErrorKey] = handleValidateErrors(res)
          } // 否则不做处理
          break
        case 429:
          Message.error('操作太频繁，请稍后再试')
          cancelAllRequest('操作太频繁')
          break
        default:
          Message.error(`服务器异常(code: ${res.status})`)
          break
      }
    } else {
      if (err instanceof axios.Cancel) { // 手动取消时，err 为一个 Cancel 对象，有一个 message 属性
        console.log(err.toString())
      } else {
        debounceMsg('请求失败')
      }
    }

    config && destroyUrlFromQueue(config.url)

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
