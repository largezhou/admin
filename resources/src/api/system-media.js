import axios from '@/plugins/axios'

export function getCategories(params = {}) {
  return axios.get('system-media-categories', {
    params,
  })
}

export function getCategoryMedia(categoryId, params = {}) {
  return axios.get(`system-media-categories/${categoryId}/system-media`, {
    params,
  })
}

export function getMedia(params = {}) {
  return axios.get('system-media', {
    params,
  })
}

export function batchUpdateMedia(data) {
  return axios.put('system-media', data)
}

// DELETE 方法不能传数据，利用 Laravel 的请求方法伪造
export function batchDestroyMedia(id) {
  return axios.post('system-media', {
    _method: 'DELETE',
    id,
  })
}
