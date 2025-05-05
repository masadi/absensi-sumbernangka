<template>
  <b-card no-body>
    <b-card-header>
      <h4 class="mb-0">Statistik Kehadiran Peserta Didik</h4>
      <b-card-text class="mb-0" style="width:30%;">
        <b-overlay :show="loading" opacity="0.6" size="md" spinner-variant="secondary">
          <v-select :options="data_bulan" v-model="form.bulan" placeholder="== Filter Bulan ==" :searchable="false" @input="changeBulan" :reduce="label => label.code"></v-select>
        </b-overlay>
      </b-card-text>
    </b-card-header>
    <template v-if="isBusy">
      <div class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
    </template>
    <template v-else>
      <vue-apex-charts type="bar" height="350" :options="goalOverviewRadialBar.chartOptions" :series="goalOverviewRadialBar.series" />
    </template>
  </b-card>
</template>

<script>
import { BCard, BCardHeader,  BSpinner, BCardText, BOverlay } from 'bootstrap-vue'
import vSelect from 'vue-select'
import moment from 'moment'
import VueApexCharts from 'vue-apexcharts'

export default {
  components: {
    vSelect,
    VueApexCharts,
    BCard,
    BCardHeader,
    BSpinner,
    BCardText,
    BOverlay,
  },
  data() {
    return {
      isBusy: true,
      loading: true,
      goalOverviewRadialBar: {
        series: [],
        chartOptions: {},
      },
      form: {
        bulan: '',
      },
      data_bulan: [],
    }
  },
  created() {
    moment.locale('id')
    for (let i = 0; i < 11; i++) { 
      let num = (i + 1);
      let code = num.toString();
      this.data_bulan.push({'label': moment.months(i), 'code': code.padStart(2, '0')})
    }
    this.loadPostsData()
  },
  methods: {
    loadPostsData(){
      this.isBusy = true
      this.$http.post('/statistik/pd', this.form).then(res => { 
        this.isBusy = false
        this.loading = false
        let getData = res.data
        this.goalOverviewRadialBar.chartOptions = getData.chartOptions
        this.goalOverviewRadialBar.series = getData.series
      })
    },
    changeBulan(val){
      //this.form.bulan = val
      this.loadPostsData()
    }
  },
}
</script>
