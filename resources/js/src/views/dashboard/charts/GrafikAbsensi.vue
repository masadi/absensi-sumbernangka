<template>
  <b-card>
    <div class="text-center text-danger my-2" v-if="isBusy">
      <b-spinner class="align-middle"></b-spinner>
      <strong>Loading...</strong>
    </div>
    <div id="chart" class="text-center" v-else>
      <h2>Grafik Absensi</h2>
      <vue-apex-charts type="pie" :options="this.chartOptions" :series="this.series"></vue-apex-charts>
    </div>
  </b-card>
</template>

<script>
import { BCard, BSpinner } from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'
export default {
  components: {
    VueApexCharts,
    BCard,
    BSpinner,
  },
  data() {
    return {
      isBusy: true,
      series: [],
      chartOptions: {
        chart: {
          type: 'pie',
        },
        labels: [],
        legend: {
          position: 'bottom',
        },
      },
    }
  },
  created() {
    this.loadRekap()
  },
  methods: {
    loadRekap(){
      this.isBusy = true
      this.$http.post('/presensi/get-rekap').then(response => {
        this.isBusy = false
        let getData = response.data
        this.series = getData.series
        this.chartOptions.labels = getData.labels
      })
    },
  },
}
</script>