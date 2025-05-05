export default [
  {
    path: '/laporan/kehadiran/guru',
    name: 'laporan-kehadiran-guru',
    component: () => import('@/views/laporan/kehadiran/guru/Index.vue'),
    meta: {
      resource: 'Kehadiran_Guru',
      action: 'read',
      pageTitle: 'Laporan',
      breadcrumb: [
        {
          text: 'Kehadiran',
        },
        {
          text: 'Guru',
          active: true,
        },
      ],
      tombol_add: {
        action: 'cleansing-guru',
        link: '',
        variant: 'primary',
        text: 'Cleansing Data',
        role: ['administrator'],
      },
    },
  },
  {
    path: '/laporan/kehadiran/pd',
    name: 'laporan-kehadiran-pd',
    component: () => import('@/views/laporan/kehadiran/pd/Index.vue'),
    meta: {
      resource: 'Kehadiran_Pd',
      action: 'read',
      pageTitle: 'Laporan',
      breadcrumb: [
        {
          text: 'Kehadiran',
        },
        {
          text: 'Peserta Didik',
          active: true,
        },
      ],
      tombol_add: {
        action: 'cleansing-pd',
        link: '',
        variant: 'primary',
        text: 'Cleansing Data',
        role: ['administrator'],
      },
    },
  },
  {
    path: '/laporan/ketidakhadiran/guru',
    name: 'laporan-ketidakhadiran-guru',
    component: () => import('@/views/laporan/ketidakhadiran/guru/Index.vue'),
    meta: {
      resource: 'Ketidakhadiran_Guru',
      action: 'read',
      pageTitle: 'Laporan',
      breadcrumb: [
        {
          text: 'Ketidakhadiran',
        },
        {
          text: 'Guru',
          active: true,
        },
      ],
    },
  },
  {
    path: '/laporan/ketidakhadiran/pd',
    name: 'laporan-ketidakhadiran-pd',
    component: () => import('@/views/laporan/ketidakhadiran/pd/Index.vue'),
    meta: {
      resource: 'Ketidakhadiran_Pd',
      action: 'read',
      pageTitle: 'Laporan',
      breadcrumb: [
        {
          text: 'Ketidakhadiran',
        },
        {
          text: 'Peserta Didik',
          active: true,
        },
      ],
    },
  },
]
