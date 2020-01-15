import Request from '@/plugins/request'

export function getAdminPerms(params = {}) {
  return Request.get('admin-permissions', {
    params,
  })
}

export function storeAdminPerm(data) {
  return Request.post('admin-permissions', data)
}

export function destroyAdminPerm(id) {
  return Request.delete(`admin-permissions/${id}`)
}

export function updateAdminPerm(id, data) {
  return Request.put(`admin-permissions/${id}`, data)
}

export function editAdminPerm(id) {
  return Request.get(`admin-permissions/${id}/edit`)
}
