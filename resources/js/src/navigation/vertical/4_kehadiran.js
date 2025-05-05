export default [
  {
    icon: 'user-check-icon',
    title: 'Kehadiran',
    children: [
      {
        icon: 'hand-click-icon',
        title: 'Guru',
        route: 'kehadiran-guru',
        resource: 'Kehadiran_Guru',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Peserta Didik',
        route: 'kehadiran-pd',
        resource: 'Kehadiran_Pd',
        action: 'read',
      },
    ]
  },
]
  