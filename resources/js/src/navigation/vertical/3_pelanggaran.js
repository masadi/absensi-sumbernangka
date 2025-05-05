export default [
  {
    icon: 'clipboard-check-icon',
    title: 'Pelanggaran',
    children: [
      {
        icon: 'hand-click-icon',
        title: 'Data Pelanggaran',
        route: 'pelanggaran',
        resource: 'Pelanggaran',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Unduh Rekap',
        route: 'pelanggaran-rekap',
        resource: 'Pelanggaran',
        action: 'read',
      },
    ]
  },
]
  