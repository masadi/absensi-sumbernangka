<template>
  <b-modal v-model="addModalShow" title="Tambah Rombongan Belajar" size="xl" @ok="handleOk" ok-title="Simpan" cancel-title="Tutup">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form @submit.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Unit" label-for="sekolah_id" :invalid-feedback="feedback.sekolah_id" :state="state.sekolah_id">
              <v-select id="sekolah_id" v-model="form.sekolah_id" :options="data_sekolah" :reduce="nama => nama.sekolah_id" label="nama" placeholder="== Pilih Unit ==" @input="changeUnit">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Tingkat" label-for="tingkat" :invalid-feedback="feedback.tingkat" :state="state.tingkat">
              <b-overlay :show="loading_tingkat" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="tingkat" v-model="form.tingkat" :options="data_tingkat" :reduce="nama => nama.id" label="nama" placeholder="== Pilih Tingkat ==">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Nama Rombel" label-for="nama" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input v-model="form.nama" placeholder="Nama Rombel" :state="state.nama"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Nama Jurusan" label-for="nama_jurusan" :invalid-feedback="feedback.nama_jurusan" :state="state.nama_jurusan">
              <b-form-input v-model="form.nama_jurusan" placeholder="Nama Jurusan" :state="state.nama_jurusan"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Wali Kelas" label-for="ptk_id" :invalid-feedback="feedback.ptk_id" :state="state.ptk_id">
              <v-select id="tingkat" v-model="form.ptk_id" :options="data_ptk" :reduce="nama => nama.ptk_id" label="nama" placeholder="== Pilih Wali Kelas ==">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
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
import { BForm, BRow, BCol, BFormGroup, BFormInput, BOverlay, BButton } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BForm, BRow, BCol, BFormGroup, BFormInput, BOverlay, BButton,
    vSelect
  },
  data() {
    return {
      addModalShow: false,
      loading_modal: false,
      loading_tingkat: false,
      form: {
        semester_id: '',
        sekolah_id: '',
        tingkat: '',
        nama: '',
        nama_jurusan: '',
        ptk_id: '',
      },
      feedback: {
        sekolah_id: '',
        tingkat: '',
        nama: '',
        nama_jurusan: '',
        ptk_id: '',
      },
      state: {
        sekolah_id: null,
        tingkat: null,
        nama: null,
        nama_jurusan: null,
        ptk_id: null,
      },
      data_sekolah: [],
      data_tingkat: [],
      data_ptk: [],
    }
  },
  created() {
    eventBus.$on('open-modal-add-rombel', this.handleEvent);
  },
  methods: {
    handleEvent(){
      this.$http.get('/referensi/get-sekolah').then(response => {
        this.data_sekolah = response.data
        this.form.semester_id = this.user.semester.semester_id
        this.addModalShow = true
      })
    },
    hideModal(){
      this.addModalShow = false
      this.form.sekolah_id = ''
      this.form.tingkat = ''
      this.form.nama = ''
      this.form.nama_jurusan = ''
      this.form.ptk_id = ''
      this.feedback.sekolah_id = ''
      this.feedback.tingkat = ''
      this.feedback.nama = ''
      this.feedback.nama_jurusan = ''
      this.feedback.ptk_id = ''
      this.state.sekolah_id = null
      this.state.tingkat = null
      this.state.nama = null
      this.state.nama_jurusan = null
      this.state.ptk_id = null
      this.data_sekolah = []
      this.data_tingkat= []
      this.data_ptk= []
    },
    changeUnit(val){
      this.form.tingkat = ''
      if(val){
        this.loading_tingkat = true
        this.$http.post('/referensi/get-tingkat', this.form).then(response => {
          let getData = response.data
          this.loading_tingkat = false
          this.data_tingkat = getData.tingkat
          this.data_ptk = getData.ptk
        })
      }
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.loading_modal = true
      this.$http.post('/referensi/simpan-rombel', this.form).then(response => {
        let data = response.data
        this.loading_modal = false
        if(data.errors){
          this.state.sekolah_id = (data.errors.sekolah_id) ? false : null
          this.state.tingkat = (data.errors.tingkat) ? false : null
          this.state.nama = (data.errors.nama) ? false : null
          this.state.nama_jurusan = (data.errors.nama_jurusan) ? false : null
          this.state.ptk_id = (data.errors.ptk_id) ? false : null
          this.feedback.sekolah_id = (data.errors.sekolah_id) ? data.errors.sekolah_id.join(', ') : ''
          this.feedback.tingkat = (data.errors.tingkat) ? data.errors.tingkat.join(', ') : ''
          this.feedback.nama = (data.errors.nama) ? data.errors.nama.join(', ') : ''
          this.feedback.nama_jurusan = (data.errors.nama_jurusan) ? data.errors.nama_jurusan.join(', ') : ''
          this.feedback.ptk_id = (data.errors.ptk_id) ? data.errors.ptk_id.join(', ') : ''
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