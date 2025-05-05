export default [
  {
    path: '/',
    name: 'homepage',
    component: () => import('@/views/utama/Index.vue'),
    meta: {
      layout: 'full',
      pageTitle: 'Halaman Utama',
      redirectIfLoggedIn: true,
    },
  },
  {
    path: '/presensi',
    name: 'presensi',
    component: () => import('@/views/utama/Presensi.vue'),
    meta: {
      layout: 'full',
      pageTitle: 'Presensi SMP AL-FALAH',
    },
  },
  {
    path: '/monitoring',
    name: 'monitoring',
    component: () => import('@/views/utama/Monitoring.vue'),
    meta: {
      layout: 'full',
      pageTitle: 'Monitoring Presensi SMP AL-FALAH',
    },
  },
  {
    path: '/dashboard',
    name: 'dashboard',
    component: () => import('@/views/dashboard/Index.vue'),
    meta: {
      //navActiveLink: 'dashboard',
      resource: 'Web',
      action: 'read',
      pageTitle: 'Beranda',
      breadcrumb: [
        {
          text: 'Beranda',
          active: true,
        },
      ],
      tombol_add: {
        action: 'generate-scan',
        link: '',
        variant: 'primary',
        text: 'Generate Scan',
        role: ['administrator'],
      },
    },
  },
]
