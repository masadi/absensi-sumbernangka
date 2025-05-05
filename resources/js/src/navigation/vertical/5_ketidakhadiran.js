export default [
  {
    icon: 'user-x-icon',
    title: 'Ketidakhadiran',
    children: [
      {
        icon: 'hand-click-icon',
        title: 'Guru',
        route: 'ketidakhadiran-guru',
        resource: 'Ketidakhadiran_Guru',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Peserta Didik',
        route: 'ketidakhadiran-pd',
        resource: 'Ketidakhadiran_Pd',
        action: 'read',
      },
    ]
  },
]
  