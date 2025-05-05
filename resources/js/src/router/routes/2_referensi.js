export default [
  {
    path: '/referensi/unit',
    name: 'referensi-unit',
    component: () => import('@/views/referensi/unit/Index.vue'),
    meta: {
      resource: 'Ref_Unit',
      action: 'read',
      pageTitle: 'Referensi Unit',
      breadcrumb: [
        {
          text: 'Referensi',
        },
        {
          text: 'Unit Lembaga',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-unit',
        link: '',
        variant: 'primary',
        text: 'Tambah Data'
      },
    },
  },
  {
    path: '/referensi/ptk',
    name: 'referensi-ptk',
    component: () => import('@/views/referensi/ptk/Index.vue'),
    meta: {
      resource: 'Ref_Guru',
      action: 'read',
      pageTitle: 'Data PTK',
      breadcrumb: [
        {
          text: 'Referensi',
        },
        {
          text: 'Data PTK',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-ptk',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['administrator'],
      },
    },
  },
  {
    path: '/referensi/pd',
    name: 'referensi-pd',
    component: () => import('@/views/referensi/pd/Index.vue'),
    meta: {
      resource: 'Ref_Pd',
      action: 'read',
      pageTitle: 'Data Peserta Didik',
      breadcrumb: [
        {
          text: 'Referensi',
        },
        {
          text: 'Data Peserta Didik',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-pd',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['administrator'],
      },
    },
  },
  {
    path: '/referensi/rombongan-belajar',
    name: 'referensi-rombongan-belajar',
    component: () => import('@/views/referensi/rombongan-belajar/Index.vue'),
    meta: {
      resource: 'Ref_Rombel',
      action: 'read',
      pageTitle: 'Data Rombongan Belajar',
      breadcrumb: [
        {
          text: 'Referensi',
        },
        {
          text: 'Rombongan Belajar',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-rombel',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['administrator'],
      },
    },
  },
  {
    path: '/admin-unit',
    name: 'admin-unit',
    component: () => import('@/views/referensi/admin/Index.vue'),
    meta: {
      resource: 'Admin_Unit',
      action: 'read',
      pageTitle: 'Admin Unit',
      breadcrumb: [
        {
          text: 'Referensi',
        },
        {
          text: 'Admin Unit',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-admin',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['administrator'],
      },
    },
  },
  {
    path: '/guru-bp',
    name: 'guru-bp',
    component: () => import('@/views/referensi/bp/Index.vue'),
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
      tombol_add: {
        action: 'add-bp',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['administrator'],
      },
    },
  },
  {
    path: '/akun-pd',
    name: 'akun-pd',
    component: () => import('@/views/referensi/akun/Index.vue'),
    meta: {
      resource: 'Admin_Unit',
      action: 'read',
      pageTitle: 'Akun Peserta Didik',
      breadcrumb: [
        {
          text: 'Referensi',
        },
        {
          text: 'Akun Peserta Didik',
          active: true,
        },
      ],
      tombol_add: {
        action: 'generate-akun',
        link: '',
        variant: 'primary',
        text: 'Generate Akun',
        role: ['administrator'],
      },
    },
  },
]
