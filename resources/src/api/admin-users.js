import axios from '@/plugins/axios'

export function getUser() {
  return axios.get('user')
}

export function getAdminUsers(params = {}) {
  return axios.get('admin-users', { params })
}

export function storeAdminUser(data) {
  return axios.post('admin-users', data)
}

export function destroyAdminUser(id) {
  return axios.delete(`admin-users/${id}`)
}

export function updateAdminUser(id, data) {
  return axios.put(`admin-users/${id}`, data)
}

export function showAdminUser(id) {
  return axios.get(`admin-users/${id}`)
}

export function editAdminUser(id) {
  return axios.get(`admin-users/${id}/edit`)
}

export function editUser() {
  return axios.get('user/edit')
}

export function updateUser(data) {
  return axios.put('user', data)
}
