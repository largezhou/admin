import Request from '@/plugins/request'

export function getUser() {
  return Request.get('user')
}

export function getAdminUsers(params = {}) {
  return Request.get('admin-users', { params })
}

export function storeAdminUser(data) {
  return Request.post('admin-users', data)
}

export function destroyAdminUser(id) {
  return Request.delete(`admin-users/${id}`)
}

export function updateAdminUser(id, data) {
  return Request.put(`admin-users/${id}`, data)
}

export function showAdminUser(id) {
  return Request.get(`admin-users/${id}`)
}

export function editAdminUser(id) {
  return Request.get(`admin-users/${id}/edit`)
}

export function editUser() {
  return Request.get('user/edit')
}

export function updateUser(data) {
  return Request.put('user', data)
}

export function createAdminUser() {
  return Request.get('admin-users/create')
}
