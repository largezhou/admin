/**
 * 把常量映射到计算属性
 *
 * @param names
 */
export const mapConstants = (names) => {
  const mapped = {}
  names.forEach((n) => {
    mapped[n] = () => {
      return eval(n)
    }
  })
  return mapped
}

export const IMAGE_EXTS = [
  'jpg', 'jpeg', 'gif', 'png', 'bmp', 'ico', 'webp', 'svg', 'tiff',
]
