import axios from '@/plugins/axios'

export function getVueRouters() {
  return axios.get('configs/vue-routers')
}

export function getConfigCategories(params = []) {
  return axios.get('config-categories', { params })
}

export function storeConfigCategory(data) {
  return axios.post('config-categories', data)
}

export function destroyConfigCategory(id) {
  return axios.delete(`config-categories/${id}`)
}
