import Request from '@/plugins/request'

export function getVueRouters() {
  return Request.get('configs/vue-routers')
}

export function getConfigCategories(params = {}) {
  return Request.get('config-categories', { params })
}

export function storeConfigCategory(data) {
  return Request.post('config-categories', data)
}

export function destroyConfigCategory(id) {
  return Request.delete(`config-categories/${id}`)
}

export function updateConfigCategory(id, data) {
  return Request.put(`config-categories/${id}`, data)
}

export function getConfigs(params = {}) {
  return Request.get('configs', { params })
}

export function updateConfig(id, data) {
  return Request.put(`configs/${id}`, data)
}

export function createConfig() {
  return Request.get('configs/create')
}

export function editConfig(id) {
  return Request.get(`configs/${id}/edit`)
}

export function storeConfig(data) {
  return Request.post('configs', data)
}

export function getConfigsByCategorySlug(categorySlug) {
  return Request.get(`configs/${categorySlug}`)
}

export function updateConfigValues(data) {
  return Request.put('configs/values', data)
}

export function getConfigsValueByCategorySlug(categorySlug) {
  return Request.get(`configs/${categorySlug}/values`)
}
