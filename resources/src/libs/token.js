const key = 'admin-token'

export function getToken() {
  return localStorage.getItem(key)
}

export function setToken(token) {
  return localStorage.setItem(key, token)
}

export function removeToken() {
  return localStorage.removeItem(key)
}
