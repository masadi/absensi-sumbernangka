export default [
  {
    path: '/kehadiran-ptk',
    name: 'kehadiran-guru',
    component: () => import('@/views/pages/Blank.vue'),
    meta: {
      resource: 'Kehadiran_Guru',
      action: 'read',
      pageTitle: 'Laporan Kehadiran PTK',
      breadcrumb: [
        {
          text: 'Laporan Kehadiran PTK',
          active: true,
        },
      ],
    },
  },
  {
    path: '/kehadiran-pd',
    name: 'kehadiran-pd',
    component: () => import('@/views/pages/Blank.vue'),
    meta: {
      resource: 'Kehadiran_Pd',
      action: 'read',
      pageTitle: 'Laporan Kehadiran PD',
      breadcrumb: [
        {
          text: 'Laporan Kehadiran PD',
          active: true,
        },
      ],
    },
  },
]
