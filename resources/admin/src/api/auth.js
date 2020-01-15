import Request from '@/plugins/request'

export function login(data) {
  return Request.post('auth/login', data)
}

export function logout() {
  return Request.post('auth/logout')
}
