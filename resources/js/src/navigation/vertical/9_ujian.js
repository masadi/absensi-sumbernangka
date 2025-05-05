export default [
  {
    icon: 'checklist-icon',
    title: 'Mata Ujian',
    children: [
      {
        icon: 'hand-click-icon',
        title: 'Jadwal Ujian',
        route: 'mata-ujian-jadwal',
        resource: 'Ref_Rombel',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Soal Ujian',
        route: 'mata-ujian-soal',
        resource: 'Ref_Rombel',
        action: 'read',
      },
      {
        icon: 'hand-click-icon',
        title: 'Pantau Ujian',
        route: 'mata-ujian-pantau-ujian',
        resource: 'Ref_Rombel',
        action: 'read',
      },
    ]
  }
]
  