export default [
  {
    path: '/pengaturan/admin-unit',
    name: 'admin-unit',
    component: () => import('@/views/pages/Blank.vue'),
    meta: {
      resource: 'Admin_Unit',
      action: 'read',
      pageTitle: 'Admin Unit',
      breadcrumb: [
        {
          text: 'Admin Unit',
          active: true,
        },
      ],
    },
  },
  {
    path: '/pengaturan/guru-bp',
    name: 'guru-bp',
    component: () => import('@/views/pages/Blank.vue'),
    meta: {
      resource: 'Admin_Unit',
      action: 'read',
      pageTitle: 'Guru BP/BK',
      breadcrumb: [
        {
          text: 'Guru BP/BK',
          active: true,
        },
      ],
    },
  },
]
