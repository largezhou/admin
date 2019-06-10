export default {
  'index': () => import('@v/Index'),
  'vue-routers/create': () => import(/* webpackChunkName: 'vur-routers' */ '@v/vue-routers/Form'),
  'vue-routers': () => import(/* webpackChunkName: 'vur-routers' */ '@v/vue-routers/Index'),
  'vue-routers/:id(\\d+)/edit': () => import(/* webpackChunkName: 'vur-routers' */ '@v/vue-routers/Form'),

  'admin-permissions': () => import(/* webpackChunkName: 'admin-permissions' */ '@v/admin-permissions/Index'),
  'admin-permissions/create': () => import(/* webpackChunkName: 'admin-permissions' */ '@v/admin-permissions/Form'),
  'admin-permissions/:id(\\d+)/edit': () => import(/* webpackChunkName: 'admin-permissions' */ '@v/admin-permissions/Form'),
}
