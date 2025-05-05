<template>
  <b-modal v-model="scanModalShow" title="Input Absen Masuk Manual" size="lg" @ok="handleOk">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form @submit.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Nama" label-for="nama" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" placeholder="Nama..." aria-disabled="true"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Jam Hadir" label-for="jam_hadir" :invalid-feedback="feedback.jam_hadir" :state="state.jam_hadir">
              <b-form-input id="jam_hadir" v-model="form.jam_hadir"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Keterangan" label-for="keterangan" :invalid-feedback="feedback.keterangan" :state="state.keterangan">
              <b-form-textarea id="keterangan" v-model="form.keterangan" placeholder="keterangan..." rows="3" max-rows="6"></b-form-textarea>
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
        <b-button variant="primary" @click="ok()">Simpan</b-button>
      </b-overlay>
    </template>  
  </b-modal>
</template>

<script>
import { BForm, BRow, BCol, BFormGroup, BFormInput, BFormTextarea, BOverlay, BButton} from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BForm, BRow, BCol, BFormGroup, BFormInput, BFormTextarea, BOverlay, BButton
  },
  data() {
    return {
      scanModalShow: false,
      loading_modal: false,
      form: {
        id: '',
        nama: '',
        ptk_id: '',
        peserta_didik_id: '',
        semester_id: '',
        jam_hadir: '',
        keterangan: '',
        created_at: '',
      },
      feedback: {
        nama: '',
        jam_hadir: '',
        keterangan: '',
      },
      state: {
        nama: null,
        jam_hadir: null,
        keterangan: null,
      },
    }
  },
  created() {
    eventBus.$on('open-modal-scan', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      var d = new Date();
      this.form.id = data.id
      if(data.ptk_id){
        this.form.nama = data.ptk.nama
      } else {
        this.form.nama = data.pd.nama
      }
      this.form.ptk_id = data.ptk_id
      this.form.peserta_didik_id = data.peserta_didik_id
      this.form.jam_hadir = `${d.getHours()}:${d.getMinutes()}:${d.getSeconds()}`
      this.form.semester_id = data.semester_id
      this.form.created_at = data.created_at
      this.scanModalShow = true
    },
    hideModal(){
      this.scanModalShow = false
      this.form.nama = ''
      this.form.jam_hadir = ''
      this.form.keterangan = ''
      this.feedback.nama = ''
      this.feedback.jam_hadir = ''
      this.feedback.keterangan = ''
      this.state.nama = null
      this.state.jam_hadir = null
      this.state.keterangan = null
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.loading_modal = true
      this.$http.post('/laporan/scan-manual', this.form).then(response => {
        let data = response.data
        this.loading_modal = false
        if(data.errors){
          this.state.nama = (data.errors.nama) ? false : null
          this.state.jam_hadir = (data.errors.jam_hadir) ? false : null
          this.state.keterangan = (data.errors.keterangan) ? false : null
          this.feedback.nama = (data.errors.nama) ? data.errors.nama.join(', ') : ''
          this.feedback.jam_hadir = (data.errors.jam_hadir) ? data.errors.jam_hadir.join(', ') : ''
          this.feedback.keterangan = (data.errors.keterangan) ? data.errors.keterangan.join(', ') : ''
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