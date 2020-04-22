export default {
  index: () => import('@v/Index'),

  'admin-permissions': () => import('@v/admin-permissions/Index'),
  'admin-permissions/create': () => import('@v/admin-permissions/Form'),
  'admin-permissions/:id(\\d+)/edit': () => import('@v/admin-permissions/Form'),

  'admin-roles': () => import('@v/admin-roles/Index'),
  'admin-roles/create': () => import('@v/admin-roles/Form'),
  'admin-roles/:id(\\d+)/edit': () => import('@v/admin-roles/Form'),
}
