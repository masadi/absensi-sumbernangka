export default [
  {
    path: '/ketidakhadiran-ptk',
    name: 'ketidakhadiran-guru',
    component: () => import('@/views/pages/Blank.vue'),
    meta: {
      resource: 'Ketidakhadiran_Guru',
      action: 'read',
      pageTitle: 'Laporan Ketidakhadiran PTK',
      breadcrumb: [
        {
          text: 'Laporan Ketidakhadiran PTK',
          active: true,
        },
      ],
    },
  },
  {
    path: '/ketidakhadiran-pd',
    name: 'ketidakhadiran-pd',
    component: () => import('@/views/pages/Blank.vue'),
    meta: {
      resource: 'Ketidakhadiran_Pd',
      action: 'read',
      pageTitle: 'Laporan Ketidakhadiran PD',
      breadcrumb: [
        {
          text: 'Laporan Ketidakhadiran PD',
          active: true,
        },
      ],
    },
  },
]
