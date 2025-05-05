<template>
  <b-card no-body>
    <b-card-body>
      <b-row>
        <b-col cols="12">
          <b-form-group label="Tingkat Kelas" label-for="tingkat" label-cols-md="3">
            <v-select id="tingkat" v-model="form.tingkat" :options="data_tingkat" :reduce="label => label.code" placeholder="== Pilih Tingkat Kelas ==" @input="changeTingkat">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
          </b-form-group>
        </b-col>
        <b-col cols="12">
          <b-form-group label="Rombongan Belajar" label-for="rombongan_belajar_id" label-cols-md="3">
            <b-overlay :show="loading_rombel" opacity="0.6" size="md" spinner-variant="secondary">
              <v-select id="rombongan_belajar_id" v-model="form.rombongan_belajar_id" :reduce="nama => nama.rombongan_belajar_id" label="nama" :options="data_rombel" placeholder="== Pilih Rombongan Belajar ==" @input="changeRombel">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-overlay>
          </b-form-group>
        </b-col>
        <b-col cols="12">
          <b-form-group label="Rombongan Belajar" label-for="rombongan_belajar_id" label-cols-md="3">
            <b-input-group>
              <b-form-datepicker v-model="form.start" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="start" @context="onContext" placeholder="Filter Tanggal" />
              <b-input-group-prepend is-text>s/d</b-input-group-prepend>
              <b-form-datepicker v-model="form.end" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="end" @context="onContext" placeholder="Filter Tanggal" />
            </b-input-group>
          </b-form-group>
        </b-col>
        <b-col cols="12">
          <b-form-group label="Peserta Didik" label-for="peserta_didik_id" label-cols-md="3">
            <b-overlay :show="loading_siswa" opacity="0.6" size="md" spinner-variant="secondary">
              <v-select id="peserta_didik_id" v-model="form.peserta_didik_id" :reduce="nama => nama.peserta_didik_id" label="nama" :options="data_siswa" placeholder="Semua Peserta Didik" @input="changePd">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-overlay>
          </b-form-group>
        </b-col>
      </b-row>
    </b-card-body>
    <b-card-footer>
      <b-button variant="success" v-if="showTingkat" @click="downloadTingkat">{{ titleTingkat }}</b-button>
      <b-button variant="info" v-if="showRombel" @click="downloadRombel">{{ titleRombel }}</b-button>
      <b-button variant="warning" v-if="showPd" @click="downloadPd">{{ titlePd }}</b-button>
    </b-card-footer>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BCardFooter, BOverlay, BRow, BCol, BFormGroup, BInputGroup, BInputGroupPrepend, BFormDatepicker, BButton} from 'bootstrap-vue'
import vSelect from 'vue-select'
export default {
  components: {
    BCard,
    BCardBody,
    BCardFooter,
    BOverlay,
    BRow,
    BCol, 
    BFormGroup,
    BInputGroup,
    BInputGroupPrepend,
    BFormDatepicker,
    BButton,
    vSelect,
  },
  data() {
    return {
      loading_rombel: false,
      loading_siswa: false,
      data_tingkat: [
        {label: 'Kelas 10', code: 10},
        {label: 'Kelas 11', code: 11},
        {label: 'Kelas 12', code: 12},
        {label: 'Kelas 13', code: 13},
      ],
      data_rombel: [],
      data_siswa: [],
      form: {
        sekolah_id: '',
        semester_id: '',
        periode_aktif: '',
        tingkat: '',
        rombongan_belajar_id: '',
        start: '',
        end: '',
        peserta_didik_id: '',
      },
      showTingkat: false,
      titleTingkat: '',
      showRombel: false,
      titleRombel: '',
      showPd: false,
      titlePd: '',
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    loadPostsData() {
      this.form.sekolah_id = this.user.sekolah_id
      this.form.semester_id = this.user.semester.semester_id
      this.form.periode_aktif = this.user.semester.nama
    },
    changeTingkat(val){
      this.showTingkat = false
      this.titleTingkat = ''
      this.data_rombel = []
      this.form.rombongan_belajar_id = ''
      if(val){
        this.loading_rombel = true
        this.$http.post('/referensi/get-rombel', this.form).then(response => {
          this.loading_rombel = false
          let getData = response.data
          this.data_rombel = getData.rombel
          this.showTingkat = true
          this.titleTingkat = `Unduh Rekap Tingkat ${this.form.tingkat}`
        }).catch(error => {
          console.log(error);
        })
      }
    },
    changeRombel(val){
      this.showRombel = false
      this.titleRombel = ''
      this.data_siswa = []
      this.form.peserta_didik_id = ''
      if(val){
        this.loading_siswa = true
        let filterObject = this.filterObject('rombongan_belajar_id', val, this.data_rombel)
        this.$http.post('/referensi/get-siswa', {
          rombongan_belajar_id: val,
          semester_id: this.form.semester_id,
          periode_aktif: this.form.periode_aktif,
        }).then(response => {
          this.loading_siswa = false
          let getData = response.data
          this.data_siswa = getData.siswa
          this.showRombel = true
          this.titleRombel = `Unduh Rekap Kelas ${filterObject.nama}`
        });
      }
    },
    changePd(val){
      this.showPd = false
      this.titlePd = ''
      if(val){
        let filterObject = this.filterObject('peserta_didik_id', val, this.data_siswa)
        console.log(filterObject);
        this.showPd = true
        this.titlePd = `Unduh Rekap ${filterObject.nama}`
      }
    },
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
    /*
    Route::get('/rekap-tingkat/{tingkat?}/{start?}/{end?}', [UnduhanController::class, 'rekap_tingkat'])->name('rekap-tingkat');
    Route::get('/rekap-rombel/{rombongan_belajar_id?}/{start?}/{end?}', [UnduhanController::class, 'rekap_rombel'])->name('rekap-rombel');
    Route::get('/rekap-pd/{peserta_didik_id?}/{start?}/{end?}', [UnduhanController::class, 'rekap_pd'])->name('rekap-pd');
    */
    downloadTingkat(){
      window.open(`/unduhan/rekap-tingkat/${this.user.sekolah_id}/${this.user.semester.semester_id}/${this.form.tingkat}/${this.form.start}/${this.form.end}`)
      console.log('downloadTingkat');
    },
    downloadRombel(){
      window.open(`/unduhan/rekap-rombel/${this.user.sekolah_id}/${this.user.semester.semester_id}/${this.form.rombongan_belajar_id}/${this.form.start}/${this.form.end}`)
      console.log('downloadRombel');
    },
    downloadPd(){
      window.open(`/unduhan/rekap-pd/${this.user.sekolah_id}/${this.user.semester.semester_id}/${this.form.peserta_didik_id}/${this.form.start}/${this.form.end}`)
      console.log('downloadPd');
    },
    filterObject(data, id, array){
      let filteredData = array.filter((item) => {
        return item[data] === id;
      });
      if(filteredData.length){
        return filteredData[0]
      }
      return null
    },
  },
}
</script>