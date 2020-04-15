const key = 'admin-logged-in'

export function loggedIn() {
  return localStorage.getItem(key) === '1'
}

export function setLoggedIn() {
  return localStorage.setItem(key, '1')
}

export function removeLoggedIn() {
  return localStorage.removeItem(key)
}
