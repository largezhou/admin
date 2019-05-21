import axios from '@/plugins/axios'

export function login(data, config) {
  return axios.post('auth/login', data, config)
}
