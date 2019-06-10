import axios from '@/plugins/axios'

export function getAdminPerms(params = {}) {
  return axios.get('admin-permissions', {
    params,
  })
}

export function storeAdminPerm(data) {
  return axios.post('admin-permissions', data)
}

export function destroyAdminPerm(id) {
  return axios.delete(`admin-permissions/${id}`)
}

export function updateAdminPerm(id, data) {
  return axios.put(`admin-permissions/${id}`, data)
}

export function editAdminPerm(id) {
  return axios.get(`admin-permissions/${id}/edit`)
}
