export default [
  {
    path: '/pengaturan',
    name: 'pengaturan',
    component: () => import('@/views/pages/Pengaturan.vue'),
    meta: {
      pageTitle: 'Pengaturan Umum',
      resource: 'Pengaturan_Umum',
      action: 'read',
      breadcrumb: [
        {
          text: 'Pengaturan',
        },
        {
          text: 'Umum',
          active: true,
        },
      ],
    },
  },
  {
    path: '/pengaturan-libur',
    name: 'pengaturan-libur',
    component: () => import('@/views/pengaturan/libur/Calendar.vue'),
    meta: {
      pageTitle: 'Pengaturan Hari Libur',
      resource: 'Pengaturan_libur',
      action: 'read',
      breadcrumb: [
        {
          text: 'Pengaturan',
        },
        {
          text: 'Hari Libur',
          active: true,
        },
      ],
      tombol_add: {
        action: 'cetak-rekap',
        link: '',
        variant: 'primary',
        text: 'Cetak Rekap'
      },
    },
  },
  {
    path: '/pengaturan-jam',
    name: 'pengaturan-jam',
    component: () => import('@/views/pengaturan/jam/Index.vue'),
    meta: {
      pageTitle: 'Pengaturan Jam',
      resource: 'Pengaturan_Jam',
      action: 'read',
      breadcrumb: [
        {
          text: 'Pengaturan',
        },
        {
          text: 'Jam',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-jam',
        link: '',
        variant: 'primary',
        text: 'Tambah Data'
      },
    },
  },
]
