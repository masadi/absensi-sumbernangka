export default [
  {
    icon: 'chart-area-line-icon',
    title: 'Rekapitulasi',
    children: [
      {
        icon: 'hand-click-icon',
        title: 'PTK',
        route: 'rekapitulasi-guru',
        resource: 'Rekapitulasi',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Peserta Didik',
        route: 'rekapitulasi-pd',
        resource: 'Rekapitulasi',
        action: 'read',
      },
      /*{
        icon: 'hand-click-icon',
        title: 'Kehadiran',
        route: 'rekapitulasi-kehadiran',
        resource: 'Ptk_Pd',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Ketidakhadiran',
        route: 'rekapitulasi-ketidakhadiran',
        resource: 'Ptk_Pd',
        action: 'read',
      },*/
      {
        icon: 'hand-click-icon',
        title: 'Modul',
        route: 'penilaian-modul',
        action: 'read',
        resource: 'Modul_HIDE',
      },
      {
        icon: 'hand-click-icon',
        title: 'Kelulusan',
        route: 'penilaian-kelulusan',
        action: 'read',
        resource: 'Kelulusan',
      },
      {
        icon: 'hand-click-icon',
        title: 'Remedial',
        route: 'penilaian-remedial',
        action: 'read',
        resource: 'Remedial',
      },
    ]
  },
  /*{
    icon: 'hand-click-icon',
    title: 'Mata Ujian',
    route: 'ujian',
    resource: 'PD',
    action: 'read',
  },*/
]
  