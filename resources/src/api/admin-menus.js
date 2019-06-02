import axios from '@/plugins/axios'

export function getMenus() {
  return axios.get('menus')
}

export function storeMenu(data) {
  return axios.post('menus', data)
}

export function destroyMenu(id) {
  return axios.delete(`menus/${id}`)
}

export function updateMenu(id, data) {
  return axios.put(`menus/${id}`, data)
}

export function editMenu(id) {
  return axios.get(`menus/${id}/edit`)
}
