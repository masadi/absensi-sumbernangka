export default [
  {
    icon: 'list-check-icon',
    title: 'Referensi',
    children: [
      {
        icon: 'hand-click-icon',
        title: 'Unit',
        route: 'referensi-unit',
        resource: 'Ref_Unit',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'PTK',
        route: 'referensi-ptk',
        resource: 'Ref_Guru',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Peserta Didik',
        route: 'referensi-pd',
        resource: 'Ref_Pd',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Rombongan Belajar',
        route: 'referensi-rombongan-belajar',
        resource: 'Ref_Rombel',
        action: 'read',
      },
      {
        icon: 'users-icon',
        route: 'admin-unit',
        title: 'Admin Unit',
        resource: 'Admin_Unit',
        action: 'read',
      },
      {
        icon: 'shield-check-icon',
        route: 'guru-bp',
        title: 'Guru BP/BK',
        resource: 'Admin_Unit',
        action: 'read',
      },
      {
        icon: 'users-icon',
        route: 'akun-pd',
        title: 'Akun PD',
        resource: 'Admin_Unit',
        action: 'read',
      },
    ],
  },
]
  