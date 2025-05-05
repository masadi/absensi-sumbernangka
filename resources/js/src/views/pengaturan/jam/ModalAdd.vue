<template>
  <b-modal v-model="addModalShow" title="Tambah Jam" size="lg" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Unit" label-for="sekolah_id" label-cols-md="4" :invalid-feedback="feedback.sekolah_id" :state="state.sekolah_id">
              <v-select id="sekolah_id" v-model="form.sekolah_id" :reduce="nama => nama.sekolah_id" label="nama" :options="data_sekolah" placeholder="== Pilih Unit ==" :state="state.sekolah_id">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nama" label-for="nama" label-cols-md="4" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Pilih Hari" label-for="hari" label-cols-md="4" :invalid-feedback="feedback.hari" :state="state.hari" v-slot="{ ariaDescribedby }">
              <b-form-checkbox-group id="hari" v-model="form.hari" :options="data_hari" :aria-describedby="ariaDescribedby" name="hari" text-field="nama" value-field="nama" :state="state.hari" stacked></b-form-checkbox-group>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Mulai Scan Masuk (Jam 1)" label-for="scan_masuk_start" label-cols-md="4" :invalid-feedback="feedback.scan_masuk_start" :state="state.scan_masuk_start">
              <b-form-timepicker v-model="form.scan_masuk_start" locale="id" :state="state.scan_masuk_start" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Akhir Scan Masuk (Jam 1)" label-for="scan_masuk_end" label-cols-md="4" :invalid-feedback="feedback.scan_masuk_end" :state="state.scan_masuk_end">
              <b-form-timepicker v-model="form.scan_masuk_end" locale="id" :state="state.scan_masuk_end" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Jam Masuk (Jam 1)" label-for="waktu_akhir_masuk" label-cols-md="4" :invalid-feedback="feedback.waktu_akhir_masuk" :state="state.waktu_akhir_masuk">
              <b-form-timepicker v-model="form.waktu_akhir_masuk" locale="id" :state="state.waktu_akhir_masuk" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Mulai Scan Masuk (Jam 2)" label-for="scan_istirahat_start" label-cols-md="4" :invalid-feedback="feedback.scan_istirahat_start" :state="state.scan_istirahat_start">
              <b-form-timepicker v-model="form.scan_istirahat_start" locale="id" :state="state.scan_istirahat_start" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Akhir Scan Masuk (Jam 2)" label-for="scan_istirahat_end" label-cols-md="4" :invalid-feedback="feedback.scan_istirahat_end" :state="state.scan_istirahat_end">
              <b-form-timepicker v-model="form.scan_istirahat_end" locale="id" :state="state.scan_istirahat_end" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Jam Masuk (Jam 2)" label-for="waktu_akhir_istirahat" label-cols-md="4" :invalid-feedback="feedback.waktu_akhir_istirahat" :state="state.waktu_akhir_istirahat">
              <b-form-timepicker v-model="form.waktu_akhir_istirahat" locale="id" :state="state.waktu_akhir_istirahat" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Mulai Scan Pulang" label-for="scan_pulang_start" label-cols-md="4" :invalid-feedback="feedback.scan_pulang_start" :state="state.scan_pulang_start">
              <b-form-timepicker v-model="form.scan_pulang_start" locale="id" :state="state.scan_pulang_start" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Akhir Scan Pulang" label-for="scan_pulang_end" label-cols-md="4" :invalid-feedback="feedback.scan_pulang_end" :state="state.scan_pulang_end">
              <b-form-timepicker v-model="form.scan_pulang_end" locale="id" :state="state.scan_pulang_end" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
        </b-row>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
          <b-button @click="cancel()">Tutup</b-button>
        </b-overlay>
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block">
          <b-button variant="success" @click="ok()">Simpan</b-button>
        </b-overlay>
      </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormInput, BRow, BCol, BFormGroup, BFormCheckboxGroup, BFormCheckbox, BFormTimepicker, BButton } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BOverlay,
    BForm,
    BFormInput,
    BRow,
    BCol,
    BFormGroup,
    BFormCheckboxGroup,
    BFormCheckbox,
    BFormTimepicker,
    BButton,
    vSelect,
  },
  data() {
    return {
      addModalShow: false,
      loading_form: false,
      form: {
        semester_id: '',
        sekolah_id: '',
        nama: '',
        hari: [],
        scan_masuk_start: '05:00:00',
        scan_masuk_end: '07:30:00',
        waktu_akhir_masuk: '07:00:00',
        scan_istirahat_start: '10:00:00',
        scan_istirahat_end: '10:30:00',
        waktu_akhir_istirahat: '10:45:00',
        scan_pulang_start: '11:00:00',
        scan_pulang_end: '14:00:00',
      },
      feedback: {
        sekolah_id: '',
        nama: '',
        hari: '',
        scan_masuk_start: '',
        scan_masuk_end: '',
        waktu_akhir_masuk: '',
        scan_istirahat_start: '',
        scan_istirahat_end: '',
        waktu_akhir_istirahat: '',
        scan_pulang_start: '',
        scan_pulang_end: '',
      },
      state: {
        sekolah_id: null,
        nama: null,
        hari: null,
        scan_masuk_start: null,
        scan_masuk_end: null,
        waktu_akhir_masuk: null,
        scan_istirahat_start: null,
        scan_istirahat_end: null,
        waktu_akhir_istirahat: null,
        scan_pulang_start: null,
        scan_pulang_end: null,
      },
      mainProps: {width: 125, height: 125 },
      preview_url: null,
      data_sekolah: [],
      data_hari: [],
    }
  },
  created() {
    eventBus.$on('open-modal-add-jam', this.handleEvent);
    this.form.semester_id = this.user.semester.semester_id
  },
  methods: {
    handleEvent(){
      this.$http.get('/jam/referensi').then(response => {
        let getData = response.data
        this.data_sekolah = getData.sekolah
        this.data_hari = getData.data_hari
        this.addModalShow = true
      })
    },
    hideModal(){
      this.addModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form.semester_id = ''
      this.form.sekolah_id = ''
      this.form.nama = ''
      this.form.hari = []
      this.form.scan_masuk_start = '05:00:00'
      this.form.scan_masuk_end = '07:30:00'
      this.form.waktu_akhir_masuk = '07:00:00'
      this.form.scan_istirahat_start = '10:00:00'
      this.form.scan_istirahat_end = '10:30:00'
      this.form.waktu_akhir_istirahat = '10:45:00'
      this.form.scan_pulang_start = '11:00:00'
      this.feedback.sekolah_id = ''
      this.feedback.nama = ''
      this.feedback.hari = ''
      this.feedback.scan_masuk_start = ''
      this.feedback.scan_masuk_end = ''
      this.feedback.waktu_akhir_masuk = ''
      this.feedback.scan_istirahat_start = ''
      this.feedback.scan_istirahat_end = ''
      this.feedback.waktu_akhir_istirahat = ''
      this.feedback.scan_pulang_start = ''
      this.feedback.scan_pulang_end = ''
      this.state.sekolah_id = null
      this.state.nama = null
      this.state.hari = null
      this.state.scan_masuk_start = null
      this.state.scan_masuk_end = null
      this.state.waktu_akhir_masuk = null
      this.state.scan_istirahat_start = null
      this.state.scan_istirahat_end = null
      this.state.waktu_akhir_istirahat = null
      this.state.scan_pulang_start = null
      this.state.scan_pulang_end = null
      this.preview_url = null
    },
    onFileChange(e) {
      this.form.image = e.target.files[0];
      this.preview_url = URL.createObjectURL(this.form.image)
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/jam/simpan', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.sekolah_id = (getData.errors.sekolah_id) ? false : null
          this.state.nama = (getData.errors.nama) ? false : null
          this.state.hari = (getData.errors.hari) ? false : null
          this.state.scan_masuk_start = (getData.errors.scan_masuk_start) ? false : null
          this.state.scan_masuk_end = (getData.errors.scan_masuk_end) ? false : null
          this.state.waktu_akhir_masuk = (getData.errors.waktu_akhir_masuk) ? false : null
          this.state.scan_istirahat_start = (getData.errors.scan_istirahat_start) ? false : null
          this.state.scan_istirahat_end = (getData.errors.scan_istirahat_end) ? false : null
          this.state.waktu_akhir_istirahat = (getData.errors.waktu_akhir_istirahat) ? false : null
          this.state.scan_pulang_start = (getData.errors.scan_pulang_start) ? false : null
          this.state.scan_pulang_end = (getData.errors.scan_pulang_end) ? false : null
          this.feedback.sekolah_id = (getData.errors.sekolah_id) ? getData.errors.sekolah_id.join(', ') : ''
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.feedback.hari = (getData.errors.hari) ? getData.errors.hari.join(', ') : ''
          this.feedback.scan_masuk_start = (getData.errors.scan_masuk_start) ? getData.errors.scan_masuk_start.join(', ') : ''
          this.feedback.scan_masuk_end = (getData.errors.scan_masuk_end) ? getData.errors.scan_masuk_end.join(', ') : ''
          this.feedback.waktu_akhir_masuk = (getData.errors.waktu_akhir_masuk) ? getData.errors.waktu_akhir_masuk.join(', ') : ''
          this.feedback.scan_istirahat_start = (getData.errors.scan_istirahat_start) ? getData.errors.scan_istirahat_start.join(', ') : ''
          this.feedback.scan_istirahat_end = (getData.errors.scan_istirahat_end) ? getData.errors.scan_istirahat_end.join(', ') : ''
          this.feedback.waktu_akhir_istirahat = (getData.errors.waktu_akhir_istirahat) ? getData.errors.waktu_akhir_istirahat.join(', ') : ''
          this.feedback.scan_pulang_start = (getData.errors.scan_pulang_start) ? getData.errors.scan_pulang_start.join(', ') : ''
          this.feedback.scan_pulang_end = (getData.errors.scan_pulang_end) ? getData.errors.scan_pulang_end.join(', ') : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.hideModal()
            this.$emit('reload')
          })
        }
      }).catch(error => {
        console.log(error);
      })
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>