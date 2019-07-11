import axios from '@/plugins/axios'

export function getVueRouters(params = {}) {
  return axios.get('vue-routers', {
    params,
  })
}

export function storeVueRouter(data) {
  return axios.post('vue-routers', data)
}

export function destroyVueRouter(id) {
  return axios.delete(`vue-routers/${id}`)
}

export function updateVueRouter(id, data) {
  return axios.put(`vue-routers/${id}`, data)
}

export function editVueRouter(id) {
  return axios.get(`vue-routers/${id}/edit`)
}

export function updateVueRouters(data) {
  return axios.put('vue-routers', data)
}
