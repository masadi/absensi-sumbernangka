export default [
  {
    icon: 'list-details-icon',
    title: 'Laporan',
    children: [
      {
        icon: 'hand-click-icon',
        title: 'Kehadiran',
        //route: 'pelanggaran',
        resource: 'Kehadiran_Guru',
        action: 'read',
        children: [
          {
            icon: 'hand-click-icon',
            title: 'Guru',
            route: 'laporan-kehadiran-guru',
            resource: 'Kehadiran_Guru',
            action: 'read',
          },
          {
            icon: 'hand-click-icon',
            title: 'Peserta Didik',
            route: 'laporan-kehadiran-pd',
            resource: 'Kehadiran_Pd',
            action: 'read',
          },
        ],
      },
      {
        icon: 'hand-click-icon',
        title: 'Ketidakhadiran',
        //route: 'pelanggaran-rekap',
        resource: 'Kehadiran_Guru',
        action: 'read',
        children: [
          {
            icon: 'hand-click-icon',
            title: 'Guru',
            route: 'laporan-ketidakhadiran-guru',
            resource: 'Ketidakhadiran_Guru',
            action: 'read',
          },
          {
            icon: 'hand-click-icon',
            title: 'Peserta Didik',
            route: 'laporan-ketidakhadiran-pd',
            resource: 'Ketidakhadiran_Pd',
            action: 'read',
          },
        ],
      },
    ]
  },
]
  