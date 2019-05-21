const utils = {}
export default utils

/**
 * 把 laravel 返回的错误消息，处理成只有一条
 * @param e axios 请求抛出的异常
 */
utils.handleValidateErrors = (e) => {
  const res = e.response
  let errors = {}
  if (res && res.status === 422) {
    ({ errors } = res.data)
    Object.keys(errors).forEach((k) => {
      errors[k] = errors[k][0]
    })
  }

  return errors
}
