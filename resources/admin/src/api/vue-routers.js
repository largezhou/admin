import Request from '@/plugins/request'

export function getVueRouters(params = {}) {
  return Request.get('vue-routers', {
    params,
  })
}

export function storeVueRouter(data) {
  return Request.post('vue-routers', data)
}

export function destroyVueRouter(id) {
  return Request.delete(`vue-routers/${id}`)
}

export function updateVueRouter(id, data) {
  return Request.put(`vue-routers/${id}`, data)
}

export function editVueRouter(id) {
  return Request.get(`vue-routers/${id}/edit`)
}

export function updateVueRouters(data) {
  return Request.put('vue-routers', data)
}

export function createVueRouter() {
  return Request.get('vue-routers/create')
}

export function importVueRouters(data) {
  return Request.post('vue-routers/by-import', data)
}
