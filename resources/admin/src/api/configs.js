import axios from '@/plugins/axios'

export function getVueRouters() {
  return axios.get('configs/vue-routers')
}

export function getConfigCategories(params = {}) {
  return axios.get('config-categories', { params })
}

export function storeConfigCategory(data) {
  return axios.post('config-categories', data)
}

export function destroyConfigCategory(id) {
  return axios.delete(`config-categories/${id}`)
}

export function updateConfigCategory(id, data) {
  return axios.put(`config-categories/${id}`, data)
}

export function getConfigs(params = {}) {
  return axios.get('configs', { params })
}

export function updateConfig(id, data) {
  return axios.put(`configs/${id}`, data)
}

export function createConfig() {
  return axios.get('configs/create')
}

export function editConfig(id) {
  return axios.get(`configs/${id}/edit`)
}

export function storeConfig(data) {
  return axios.post('configs', data)
}

export function getConfigsByCategorySlug(categorySlug) {
  return axios.get(`configs/${categorySlug}`)
}

export function updateConfigValues(data) {
  return axios.put('configs/values', data)
}

export function getConfigsValueByCategorySlug(categorySlug) {
  return axios.get(`configs/${categorySlug}/values`)
}
