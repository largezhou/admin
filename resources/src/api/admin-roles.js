import axios from '@/plugins/axios'

export function getAdminRoles(params = {}) {
  return axios.get('admin-roles', { params })
}

export function storeAdminRole(data) {
  return axios.post('admin-roles', data)
}

export function destroyAdminRole(id) {
  return axios.delete(`admin-roles/${id}`)
}

export function updateAdminRole(id, data) {
  return axios.put(`admin-roles/${id}`, data)
}

export function editAdminRole(id) {
  return axios.get(`admin-roles/${id}/edit`)
}

export function createAdminRole() {
  return axios.get('admin-roles/create')
}
