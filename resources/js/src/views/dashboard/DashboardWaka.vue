<template>
  <div>
    <b-row v-if="isBusy">
      <b-col>
        <b-card no-body>
          <b-card-body>
            <div class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
    <b-row class="match-height" v-else>
      <b-col cols="12" md="8">
        <b-card no-body>
          <b-card-header>
            <h4 class="card-title">Statistik</h4>
            <b-card-text class="text-muted">
              {{periode_aktif}}
            </b-card-text>
          </b-card-header>
          <b-card-body>
            <b-row>
              <b-col v-for="item in statistik" :key="item.title" cols="6" md="3">
                <div class="d-flex">
                  <b-avatar :variant="item.color" size="4em" class="mr-1">
                    <component :is="item.icon" />
                  </b-avatar>
                  <div class="d-flex flex-column">
                    <h3>{{ item.stats }}</h3>
                    <span class="text-caption">
                      {{ item.title }}
                    </span>
                  </div>
                </div>
              </b-col>
            </b-row>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col v-for="item in aplikasi" :key="item.title" cols="6" md="2">
        <b-card no-body>
          <b-card-body class="text-center">
            <b-avatar :variant="item.color" size="4em" class="mb-1">
              <component :is="item.icon" />
            </b-avatar>
            <h3>{{ item.stats }}</h3>
            <span class="text-body-2">{{ item.title }}</span>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
    <b-row v-if="!isBusy">
      <b-col cols="6">
        <b-card no-body>
          <b-card-header>
            <h4 class="card-title">Gender Siswa</h4>
            <b-card-text class="text-muted">
              {{periode_aktif}}
            </b-card-text>
          </b-card-header>
          <b-card-body>
            <vue-apex-charts type="bar" height="400" :options="chartOptions" :series="gender_series" />
          </b-card-body>
        </b-card>
      </b-col>
      <b-col cols="6">
        <b-card no-body>
          <b-card-header>
            <h4 class="card-title">Jumlah Siswa</h4>
            <b-card-text class="text-muted">
              {{periode_aktif}}
            </b-card-text>
          </b-card-header>
          <b-card-body>
            <vue-apex-charts type="bar" height="400" :options="chartOptions" :series="siswa_series" />
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { BRow, BCol, BCard, BCardHeader, BCardText, BCardBody, BSpinner, BTableSimple, BTr, BTd, BFormCheckbox, VBTooltip, BAvatar, } from 'bootstrap-vue'
import VueApexCharts from 'vue-apexcharts'
export default {
  components: {
    BRow, 
    BCol,
    BCard,
    BCardHeader, BCardText,
    BCardBody,
    BSpinner,
    BTableSimple,
    BTr, 
    BTd,
    BFormCheckbox,
    VBTooltip,
    BAvatar,
    VueApexCharts
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  data() {
    return {
      periode_aktif: '',
      isBusy: true,
      statistik: [],
      aplikasi: [],
      data_gender: [],
      data_siswa: [],
      app: {},
      gender_series: [],
      siswa_series: [],
      chartOptions: {},
    }
  },
  created() {
    this.periode_aktif = this.user.semester.nama
    this.loadStatistics()
    this.loadAplikasi()
    this.loadGrafik()
  },
  methods: {
    loadStatistics(){
      this.$http.post('/dashboard', {
        sekolah_id: this.user.sekolah_id,
        semester_id: this.user.semester.semester_id,
        periode_aktif: this.user.semester.nama,
      }).then(response => {
        this.isBusy = false
        let getData = response.data
        this.statistik = getData.statistics
      })
    },
    loadAplikasi(){
      this.$http.get('/dashboard/aplikasi').then(response => {
        let getData = response.data
        this.aplikasi = getData.aplikasi
      })
    },
    loadGrafik(){
      this.$http.get('/dashboard/grafik').then(response => {
        let getData = response.data
        var categories = []
        var pria = []
        var wanita = []
        var kelas_10 = []
        var kelas_11 = []
        var kelas_12 = []
        getData.forEach(function(value, key) {
          categories.push(value.alias)
          pria.push(value.pria)
          wanita.push(value.wanita)
          kelas_10.push(value.kelas_10)
          kelas_11.push(value.kelas_11)
          kelas_12.push(value.kelas_12)
        })
        this.gender_series = [{
          name: 'Laki-laki',
          data: pria
        }, 
        {
          name: 'Perempuan',
          data: wanita
        }]
        this.chartOptions = {
          chart: {
            type: 'bar',
            height: 350
          },
          plotOptions: {
            bar: {
              horizontal: false,
              columnWidth: '55%',
              endingShape: 'rounded'
            },
          },
          dataLabels: {
            enabled: false
          },
          stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
          },
          xaxis: {
            categories: categories,
          },
          fill: {
            opacity: 1
          },
          tooltip: {
            y: {
              formatter: function (val) {
                return val + " Siswa"
              }
            }
          }
        }
        this.siswa_series = [{
          name: 'X',
          data: kelas_10
        }, 
        {
          name: 'XI',
          data: kelas_11
        },
        {
          name: 'XII',
          data: kelas_12
        }]
      })
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
@import '~@resources/scss/vue/libs/chart-apex.scss';
</style>