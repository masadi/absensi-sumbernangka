<template>
  <b-modal v-model="showModal" :title="title" size="lg" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12" class="mb-2">
            <b-img thumbnail center width="150" :src="photo" :alt="form.nama" />
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nama Lengkap" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" placeholder="Nama Lengkap" :state="state.nama" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nomor Induk" label-for="no_induk" label-cols-md="3" :invalid-feedback="feedback.no_induk" :state="state.no_induk">
              <b-form-input id="no_induk" v-model="form.no_induk" placeholder="Nomor Induk" :state="state.no_induk" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="NISN" label-for="nisn" label-cols-md="3" :invalid-feedback="feedback.nisn" :state="state.nisn">
              <b-form-input id="nisn" v-model="form.nisn" placeholder="NISN" :state="state.nisn" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Whatsapp" label-for="wa" label-cols-md="3" :invalid-feedback="feedback.wa" :state="state.wa">
              <b-form-input id="wa" v-model="form.wa" placeholder="Whatsapp" :state="state.wa" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Foto" label-for="foto" label-cols-md="3" :invalid-feedback="feedback.foto" :state="state.foto">
              <b-form-file v-model="form.foto" :state="state.foto" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
            </b-form-group>
          </b-col>
        </b-row>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
      <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
        <b-button @click="cancel()" class="float-right">Tutup</b-button>
      </b-overlay>
      <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block">
        <b-button variant="success" @click="ok()" class="float-right">Simpan</b-button>
      </b-overlay>    
    </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormGroup, BFormInput, BButton, BRow, BCol, BImg, BFormFile } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BOverlay, BForm, BFormGroup, BFormInput, BButton, BRow, BCol, BImg, BFormFile,
  },
  data() {
    return {
      showModal: false,
      loading_form: false,
      title: null,
      photo: null,
      form: {
        peserta_didik_id: null,
        nama: null,
        no_induk: null,
        nisn: null,
        wa: null,
        foto: null,
      },
      feedback: {
        nama: null,
        no_induk: null,
        nisn: null,
        wa: null,
        foto: null,
      },
      state: {
        nama: null,
        no_induk: null,
        nisn: null,
        wa: null,
        foto: null,
      },
    }
  },
  created() {
    eventBus.$on('open-modal-edit-pd', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.title = `Edit Biodata ${data.nama}`
      this.photo = `/storage/${data.photo}`
      this.form.peserta_didik_id = data.peserta_didik_id
      this.form.nama = data.nama
      this.form.no_induk = data.no_induk
      this.form.nisn = data.nisn
      this.form.wa = data.wa
      this.showModal = true
    },
    onFileChange(e) {
      this.form.foto = e.target.files[0];
    },
    hideModal(){
      this.showModal = false
      this.resetModal()
    },
    resetModal(){
      this.title = null
      this.photo = null
      this.form.peserta_didik_id = null
      this.form.nama = null
      this.form.no_induk = null
      this.form.nisn = null
      this.form.wa = null
      this.form.foto = null
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      const data = new FormData();
      data.append('peserta_didik_id', (this.form.peserta_didik_id) ? this.form.peserta_didik_id : '');
      data.append('nama', (this.form.nama) ? this.form.nama : '');
      data.append('no_induk', (this.form.no_induk) ? this.form.no_induk : '');
      data.append('nisn', (this.form.nisn) ? this.form.nisn : '');
      data.append('wa', (this.form.wa) ? this.form.wa : '');
      data.append('foto', (this.form.foto) ? this.form.foto : '');
      this.$http.post('/referensi/update-siswa', data).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.state.no_induk = (getData.errors.no_induk) ? false : null
          this.state.nisn = (getData.errors.nisn) ? false : null
          this.state.wa = (getData.errors.wa) ? false : null
          this.state.foto = (getData.errors.foto) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.feedback.no_induk = (getData.errors.no_induk) ? getData.errors.no_induk.join(', ') : ''
          this.feedback.nisn = (getData.errors.nisn) ? getData.errors.nisn.join(', ') : ''
          this.feedback.wa = (getData.errors.wa) ? getData.errors.wa.join(', ') : ''
          this.feedback.foto = (getData.errors.foto) ? getData.errors.foto.join(', ') : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
            buttonsStyling: false,
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