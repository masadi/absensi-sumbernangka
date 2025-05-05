<template>
  <b-container fluid class="mt-3" data-aos="fade-up" data-aos-duration="1500">
    <b-card class="text-center">
      <h1 class="mt-2" v-html="`Selamat Datang di Portal ${appName}`"></h1>
      <b-button variant="primary" class="my-1 mr-1" size="lg" :href="scan_url" target="_blank">Presensi</b-button>
      <b-button variant="warning" class="my-1 mr-1" size="lg" :href="`${scan_url}/monitoring`" target="_blank">Monitoring</b-button>
      <!--
      <b-button variant="primary" class="my-1 mr-1" size="lg" :to="{name: 'presensi'}" target="_blank">Presensi</b-button>
      <b-button variant="warning" class="my-1 mr-1" size="lg" :to="{name: 'monitoring'}" target="_blank">Monitoring</b-button>
      -->
      <b-button variant="success" class="my-1 " size="lg" :to="{name: 'auth-login'}">Login ke Portal</b-button>
    </b-card>
    <statistik-pd data-aos="fade-down" data-aos-duration="1500"></statistik-pd>
    <statistik-ptk data-aos="fade-left" data-aos-duration="1500"></statistik-ptk>
  </b-container>
</template>

<script>
import { BContainer, BCard, BButton, BRow, BCol } from 'bootstrap-vue'
import StatistikPd from '@/views/components/StatistikPd.vue'
import StatistikPtk from '@/views/components/StatistikPtk.vue'
import { $themeConfig } from '@themeConfig'
import AOS from 'aos'
import 'aos/dist/aos.css'
export default {
  components: {
    BContainer,
    BCard,
    BButton,
    BRow,
    BCol,
    StatistikPtk,
    StatistikPd,
  },
  mounted() {
    AOS.init()
  },
  data() {
    return {
      appName: $themeConfig.app.appName,
      scan_url: $themeConfig.app.scanUrl,
    }
  }
}
</script>