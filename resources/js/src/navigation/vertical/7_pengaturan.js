export default [
  {
    icon: 'settings-icon',
    title: 'Pengaturan',
    children: [
      {
        icon: 'hand-click-icon',
        title: 'Umum',
        route: 'pengaturan',
        resource: 'Pengaturan_Umum',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Hari Libur',
        route: 'pengaturan-libur',
        resource: 'Pengaturan_libur',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Jam',
        route: 'pengaturan-jam',
        resource: 'Pengaturan_Jam',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Remedial',
        route: 'walas-remedial',
        resource: 'Wali',
        action: 'read',
      },
    ]
  },
]
  