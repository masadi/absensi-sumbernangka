export default [
  {
    path: '/mata-ujian/jadwal-ujian',
    name: 'mata-ujian-jadwal',
    component: () => import('@/views/mata-ujian/jadwal/Index.vue'),
    meta: {
      resource: 'Ref_Rombel',
      action: 'read',
      pageTitle: 'Data Jadwal Ujian',
      breadcrumb: [
        {
          text: 'Mata Ujian',
        },
        {
          text: 'Jadwal Ujian',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-jadwal',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['unit'],
      },
    },
  },
  {
    path: '/mata-ujian/soal-ujian',
    name: 'mata-ujian-soal',
    component: () => import('@/views/mata-ujian/soal/Index.vue'),
    meta: {
      resource: 'Ref_Rombel',
      action: 'read',
      pageTitle: 'Data Soal Ujian',
      breadcrumb: [
        {
          text: 'Mata Ujian',
        },
        {
          text: 'Soal Ujian',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-soal',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['administrator', 'unit'],
      },
    },
  },
  {
    path: '/mata-ujian/pantau-ujian',
    name: 'mata-ujian-pantau-ujian',
    component: () => import('@/views/mata-ujian/pantau-ujian/Index.vue'),
    meta: {
      //navActiveLink: 'dashboard',
      resource: 'Ref_Rombel',
      action: 'read',
      pageTitle: 'Daftar Ujian Aktif',
      breadcrumb: [
        {
          text: 'Mata Ujian',
        },
        {
          text: 'Pantau Ujian',
          active: true,
        },
      ],
    },
  },
  {
    path: '/mata-ujian/detil-ujian/:id',
    name: 'mata-ujian-detil-ujian',
    component: () => import('@/views/mata-ujian/pantau-ujian/Detil.vue'),
    meta: {
      navActiveLink: 'mata-ujian-pantau-ujian',
      resource: 'Ref_Rombel',
      action: 'read',
      pageTitle: 'Pantau Ujian',
      breadcrumb: [
        {
          text: 'Mata Ujian',
        },
        {
          text: 'Pantau Proses Ujian',
          active: true,
        },
      ],
    },
  },
]
