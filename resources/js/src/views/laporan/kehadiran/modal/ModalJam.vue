<template>
  <b-modal v-model="jamModalShow" title="Edit Jam Scan" size="lg" @ok="handleOk">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form @submit.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Nama" label-for="nama" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input v-model="form.nama" placeholder="Nama" :state="state.nama" disabled></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Tanggal Scan" label-for="tanggal" :invalid-feedback="feedback.tanggal" :state="state.tanggal">
              <b-form-input v-model="form.tanggal" placeholder="Tanggal Scan" :state="state.tanggal" disabled></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Jam Hadir*" label-for="tanggal" :invalid-feedback="feedback.jam_masuk" :state="state.jam_masuk">
              <b-form-timepicker v-model="form.jam_masuk" locale="id" :state="state.jam_masuk" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Jam Pulang*" label-for="tanggal" :invalid-feedback="feedback.jam_pulang" :state="state.jam_pulang">
              <b-form-timepicker v-model="form.jam_pulang" locale="id" :state="state.jam_pulang" show-seconds></b-form-timepicker>
            </b-form-group>
          </b-col>
        </b-row>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
      <b-overlay :show="loading_modal" rounded opacity="0.6" size="sm" spinner-variant="secondary">
        <b-button @click="cancel()">Tutup</b-button>
      </b-overlay>
      <b-overlay :show="loading_modal" rounded opacity="0.6" size="sm" spinner-variant="primary">
        <b-button variant="primary" @click="ok()">Perbaharui</b-button>
      </b-overlay>
    </template>  
  </b-modal>
</template>

<script>
import { BForm, BRow, BCol, BFormGroup, BFormInput, BOverlay, BButton, BFormTimepicker } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BForm, BRow, BCol, BFormGroup, BFormInput, BOverlay, BButton, BFormTimepicker
  },
  data() {
    return {
      jamModalShow: false,
      loading_modal: false,
      form: {
        id: '',
        aksi: '',
        nama: '',
        tanggal: '',
        jam_masuk: '',
        jam_pulang: '',
      },
      feedback: {
        nama: '',
        tanggal: '',
        jam_pulang: '',
        jam_masuk: '',
      },
      state: {
        nama: null,
        tanggal: null,
        jam_masuk: null,
        jam_pulang: null,
      },
    }
  },
  created() {
    eventBus.$on('open-modal-edit-laporan', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      const now = new Date()
      this.form.id = data.id
      if(data.ptk_id){
        this.form.aksi = 'guru'
        this.form.nama = data.ptk.nama
      } else {
        this.form.aksi = 'pd'
        this.form.nama = data.pd.nama
      }
      this.form.tanggal = data.tanggal_scan_str
      if(data.jam_masuk !== '-'){
        this.form.jam_masuk = data.jam_masuk
      } else {
        this.form.jam_masuk = `${now.getHours()}:${now.getMinutes()}:${now.getSeconds()}`
      }
      if(data.jam_pulang !== '-'){
        this.form.jam_pulang = data.jam_pulang
      } else {
        this.form.jam_pulang = `${now.getHours()}:${now.getMinutes()}:${now.getSeconds()}`
      }
      this.jamModalShow = true
    },
    hideModal(){
      this.jamModalShow = false
      this.form.id = ''
      this.form.nama = ''
      this.form.tanggal = ''
      this.form.jam_masuk = ''
      this.form.jam_pulang = ''
      this.feedback.nama = ''
      this.feedback.tanggal = ''
      this.feedback.jam_masuk = ''
      this.feedback.jam_pulang = ''
      this.state.nama = null
      this.state.tanggal = null
      this.state.jam_masuk = null
      this.state.jam_pulang = null
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.loading_modal = true
      this.$http.post('/laporan/update-jam', this.form).then(response => {
        let data = response.data
        this.loading_modal = false
        if(data.errors){
          this.state.nama = (data.errors.nama) ? false : null
          this.state.tanggal = (data.errors.tanggal) ? false : null
          this.state.jam_masuk = (data.errors.jam_masuk) ? false : null
          this.state.jam_pulang = (data.errors.jam_pulang) ? false : null
          this.feedback.nama = (data.errors.nama) ? data.errors.nama.join(', ') : ''
          this.feedback.tanggal = (data.errors.tanggal) ? data.errors.tanggal.join(', ') : ''
          this.feedback.jam_masuk = (data.errors.jam_masuk) ? data.errors.jam_masuk.join(', ') : ''
          this.feedback.jam_pulang = (data.errors.jam_pulang) ? data.errors.jam_pulang.join(', ') : ''
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.$emit('reload')
            this.hideModal()
          })
        }
      })
    },
  },
}
</script>