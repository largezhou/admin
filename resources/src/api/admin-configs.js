import axios from '@/plugins/axios'

export function getVueRouters() {
  return axios.get('configs/vue-routers')
}
