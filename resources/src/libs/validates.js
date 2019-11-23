import { getExt } from '@/libs/utils'
import { IMAGE_EXTS } from '@/libs/constants'

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
 * 判断是不是整数
 * @param val
 * @returns {boolean}
 */
export function isInt(val) {
  return /^[+-]?\d+$/.test(val)
}

/**
 * 简单根据后缀判断是不是可在浏览器预览的图片
 * @param file
 * @param isExt 传入的 file 是不是后缀
 */
export function isImage(file, isExt = false) {
  let ext

  if (isExt) {
    ext = file
  } else if (file instanceof File) {
    ext = getExt(file.name)
  } else {
    ext = getExt(file)
  }

  return IMAGE_EXTS.indexOf(ext.toLowerCase()) !== -1
}
