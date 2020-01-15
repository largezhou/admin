import Request from '@/plugins/request'

export function getAdminRoles(params = {}) {
  return Request.get('admin-roles', { params })
}

export function storeAdminRole(data) {
  return Request.post('admin-roles', data)
}

export function destroyAdminRole(id) {
  return Request.delete(`admin-roles/${id}`)
}

export function updateAdminRole(id, data) {
  return Request.put(`admin-roles/${id}`, data)
}

export function editAdminRole(id) {
  return Request.get(`admin-roles/${id}/edit`)
}

export function createAdminRole() {
  return Request.get('admin-roles/create')
}
