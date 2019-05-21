import axios from '@/plugins/axios'

export function getUser() {
  return axios.get('user')
}
