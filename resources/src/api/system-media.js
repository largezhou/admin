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
