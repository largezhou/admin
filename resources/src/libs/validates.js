/**
 * 判断是不是链接
 *
 * @param {string} path
 * @returns {Boolean}
 */
export function isExternal(path) {
  return /^(https?:|mailto:|tel:)/.test(path)
}

/**
 * 判断是不是正数
 * @param val
 * @returns {boolean}
 */
export function isInt(val) {
  return /^\d+$/.test(val)
}
