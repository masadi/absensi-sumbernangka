import Vue from 'vue'
import VueRouter from 'vue-router'

// Routes
import { canNavigate } from '@/libs/acl/routeProtection'
import { isUserLoggedIn, getUserData } from '@/auth/utils'
import beranda from './routes/1_beranda'
import referensi from './routes/2_referensi'
import ujian from './routes/9_ujian'
import laporan from './routes/3_laporan'
import pelanggaran from './routes/3_pelanggaran'
//import kehadiran from './routes/4_kehadiran'
//import ketidakhadiran from './routes/5_ketidakhadiran'
import rekapitulasi from './routes/6_rekapitulasi'
import pengaturan from './routes/7_pengaturan'
//import admin from './routes/8_admin'
//import bottom from './routes/99_bottom'
import pages from './routes/pages'
Vue.use(VueRouter)

const router = new VueRouter({
  mode: 'history',
  base: process.env.BASE_URL,
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    { path: '/', redirect: { name: 'homepage' } },
    ...beranda,
    ...referensi,
    ...ujian,
    ...laporan,
    ...pelanggaran,
    //...kehadiran,
    //...ketidakhadiran,
    ...rekapitulasi,
    ...pengaturan,
    //...admin,
    ...pages,
    {
      path: '*',
      redirect: 'error-404',
    },
  ],
})
router.beforeEach((to, _, next) => {
  const isLoggedIn = isUserLoggedIn()
  const title = to.meta.pageTitle || to.meta.webTitle
  if (title) {
    document.title = `${title} | ${app_name}`
  } else {
    document.title = app_name
  }
  if (!canNavigate(to)) {
    // Redirect to login if not logged in
    if (!isLoggedIn) return next({ name: 'auth-login' })

    // If logged in => not authorized
    return next({ name: 'misc-not-authorized' })
  }

  // Redirect if logged in
  if (to.meta.redirectIfLoggedIn && isLoggedIn) {
    const userData = getUserData()
    next({ name: 'dashboard' })
  }

  return next()
})

// ? For splash screen
// Remove afterEach hook if you are not using splash screen
router.afterEach(() => {
  // Remove initial loading
  const appLoading = document.getElementById('loading-bg')
  if (appLoading) {
    appLoading.style.display = 'none'
  }
})

export default router
