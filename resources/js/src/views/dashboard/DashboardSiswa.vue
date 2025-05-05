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
      <b-col cols="12" md="6">
        <b-card no-body>
          <b-card-header>
            <h4 class="card-title">Biodata</h4>
          </b-card-header>
          <b-card-body>
            <b-table-simple>
              <b-tr>
                <b-td>Nama</b-td>
                <b-td>{{ profile.nama }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>NISN</b-td>
                <b-td>{{ profile.nisn }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>Tempat, Tanggal Lahir</b-td>
                <b-td>{{ profile.tetala }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>ID Card</b-td>
                <b-td><b-button :href="`/cetak/id-card/pd/${profile.peserta_didik_id}`" target="_blank" size="sm" variant="success">Cetak ID Card</b-button></b-td>
              </b-tr>
            </b-table-simple>
          </b-card-body>
        </b-card>
      </b-col>
      <b-col cols="12" md="6">
        <b-card no-body>
          <b-card-header>
            <h4 class="card-title">Rekapitulasi Presensi</h4>
          </b-card-header>
          <b-card-body>
            <b-table-simple>
              <b-tr>
                <b-td>Total Hari Aktif</b-td>
                <b-td>: {{ rekap.aktif }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>Total Hari Libur</b-td>
                <b-td>: {{  rekap.libur }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>Total Tanpa Keterangan</b-td>
                <b-td>: {{ rekap.alpa }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>Total Sakit</b-td>
                <b-td>: {{ rekap.sakit }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>Total Izin</b-td>
                <b-td>: {{ rekap.izin }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>Total Cuti</b-td>
                <b-td>: {{ rekap.izin }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>Total Tidak Hadir</b-td>
                <b-td>: {{ rekap.tidak_hadir }}</b-td>
              </b-tr>
              <b-tr>
                <b-td>Total Hadir</b-td>
                <b-td>: {{ rekap.hadir }}</b-td>
              </b-tr>
            </b-table-simple>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import { BRow, BCol, BCard, BCardHeader, BCardText, BCardBody, BSpinner, BTableSimple, BTr, BTd, BFormCheckbox, VBTooltip, BAvatar, BButton} from 'bootstrap-vue'
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
    BButton
  },
  directives: {
    'b-tooltip': VBTooltip,
  },
  data() {
    return {
      isBusy: true,
      profile: null,
      statistik: [],
      rekap: {
        aktif: 0,
        libur: 0,
        alpa: 0,
        sakit: 0,
        izin: 0,
        cuti: 0,
        tidak_hadir: 0,
        hadir: 0,
      },
    }
  },
  created() {
    this.periode_aktif = this.user.semester.nama
    this.loadStatistics()
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
        this.profile = getData.profile
        this.rekap = getData.rekap
      })
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>