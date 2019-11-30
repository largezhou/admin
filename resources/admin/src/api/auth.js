import axios from '@/plugins/axios'

export function login(data) {
  return axios.post('auth/login', data)
}

export function logout() {
  return axios.post('auth/logout')
}
