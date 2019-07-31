/**
 * 把常量映射到计算属性
 *
 * @param names
 */
export const mapConstants = (names) => {
  if (typeof names === 'string') {
    names = [names]
  }
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

/**
 * 所有权限
 *
 * @type {string}
 */
export const PERMISSION_PASS_ALL = 'pass-all'

/**
 * 超管角色
 *
 * @type {string}
 */
export const ROLE_ADMIN = 'administrator'

/**
 * 配置的类型
 */
export const CONFIG_TYPES = {
  INPUT: 'input',
  TEXTAREA: 'textarea',
  FILE: 'file',
  SINGLE_SELECT: 'single_select',
  MULTIPLE_SELECT: 'multiple_select',
  OTHER: 'other',
}

/**
 * 系统基础设置的相关常量
 * @type {{}}
 */
export const SYSTEM_BASIC = {
  /**
   * 系统设置的分类 slug
   */
  SLUG: 'system_basic',
  APP_NAME_SLUG: 'app_name',
  APP_LOGO_SLUG: 'app_logo',
  HOME_ROUTE_SLUG: 'home_route',
  CDN_DOMAIN_SLUG: 'cdn_domain',

  DEFAULT_APP_NAME: '管理后台',
  DEFAULT_HOME_ROUTE: '1',
  DEFAULT_CDN_DOMAIN: '/',
}
