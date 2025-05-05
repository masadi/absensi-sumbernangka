export default [
  {
    path: '/pelanggaran',
    name: 'pelanggaran',
    component: () => import('@/views/pelanggaran/pd/Index.vue'),
    meta: {
      resource: 'Pelanggaran',
      action: 'read',
      pageTitle: 'Data Pelanggaran Peserta Didik',
      breadcrumb: [
        {
          text: 'Pelanggaran',
        },
        {
          text: 'Peserta Didik',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-pelanggaran',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['bk'],
      },
    },
  },
  {
    path: '/pelanggaran/cetak',
    name: 'pelanggaran-rekap',
    component: () => import('@/views/pelanggaran/Rekapitulasi.vue'),
    meta: {
      resource: 'Pelanggaran',
      action: 'read',
      pageTitle: 'Cetak Pelanggaran Peserta Didik',
      breadcrumb: [
        {
          text: 'Cetak Pelanggaran',
        },
        {
          text: 'Siswa',
          active: true,
        },
      ],
      tombol_add: {
        action: 'add-presensi-pd',
        link: '',
        variant: 'primary',
        text: 'Tambah Data',
        role: ['piket'],
      },
    },
  },
]
