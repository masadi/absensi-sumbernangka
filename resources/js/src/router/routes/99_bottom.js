export default [
  {
    path: '/profile',
    name: 'profile',
    component: () => import('@/views/pages/Profile.vue'),
    meta: {
      resource: 'Profile',
      action: 'read',
      pageTitle: 'Profile',
      breadcrumb: [
        {
          text: 'Profile',
          active: true,
        },
      ],
    },
  },
]
