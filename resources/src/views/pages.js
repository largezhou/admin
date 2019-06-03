export default {
  'index': () => import('@v/Index'),
  'vue-routers/create': () => import('@v/vue-routers/Form'),
  'vue-routers': () => import('@v/vue-routers/Index'),
  'vue-routers/:id(\\d+)/edit': () => import('@v/vue-routers/Form'),
}
