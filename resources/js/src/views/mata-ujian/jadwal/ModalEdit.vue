<template>
  <b-modal v-model="editModalShow" title="Edit Jadwal Ujian" size="xl" @ok="handleOk">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form @submit.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Rombongan Belajar" label-for="tingkat" :invalid-feedback="feedback.rombongan_belajar_id" :state="state.rombongan_belajar_id">
              <v-select id="rombongan_belajar_id" v-model="form.rombongan_belajar_id" :options="data_rombel" :reduce="nama => nama.rombongan_belajar_id" label="nama" placeholder="== Pilih Rombongan Belajar ==">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Nama Ujian" label-for="nama" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input v-model="form.nama" placeholder="Nama Ujian" :state="state.nama"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Ketua Pelaksana" label-for="ptk_id" :invalid-feedback="feedback.ptk_id" :state="state.ptk_id">
              <b-overlay :show="loading_ptk" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="ptk_id" v-model="form.ptk_id" :options="data_ptk" :reduce="nama => nama.ptk_id" label="nama" placeholder="== Pilih Ketua Pelaksana ==">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Tanggal Titimangsa" label-for="tanggal" :invalid-feedback="feedback.tanggal" :state="state.tanggal">
              <b-form-datepicker id="tanggal" v-model="form.tanggal" show-decade-nav button-variant="outline-secondary" left locale="id" @context="onContext" placeholder="Pilih Tanggal Titimangsa" :state="state.tanggal"></b-form-datepicker>
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
import { BForm, BRow, BCol, BFormGroup, BFormInput, BOverlay, BButton, BFormDatepicker } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BForm, BRow, BCol, BFormGroup, BFormInput, BOverlay, BButton, BFormDatepicker,
    vSelect
  },
  data() {
    return {
      editModalShow: false,
      loading_modal: false,
      loading_ptk: false,
      form: {
        id: '',
        nama: '',
        rombongan_belajar_id: '',
        ptk_id: '',
        tanggal: '',
      },
      feedback: {
        nama: '',
        rombongan_belajar_id: '',
        ptk_id: '',
        tanggal: '',
      },
      state: {
        nama: null,
        rombongan_belajar_id: null,
        ptk_id: null,
        tanggal: null,
      },
      data_rombel: [],
      data_ptk: [],
    }
  },
  created() {
    eventBus.$on('open-modal-edit-jadwal', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      console.log(data);
      this.form.id = data.jadwal.id
      this.form.nama = data.jadwal.nama
      this.form.rombongan_belajar_id = data.jadwal.rombongan_belajar_id
      this.form.ptk_id = data.jadwal.ptk_id
      this.form.tanggal = data.jadwal.tanggal
      this.data_rombel = data.data_rombel
      this.data_ptk = data.data_ptk
      this.editModalShow = true
    },
    hideModal(){
      this.editModalShow = false
      this.form.nama = ''
      this.form.rombongan_belajar_id = ''
      this.form.ptk_id = ''
      this.form.tanggal = ''
      this.feedback.nama = ''
      this.feedback.rombongan_belajar_id = ''
      this.feedback.nama_jurusan = ''
      this.feedback.ptk_id = ''
      this.feedback.tanggal = ''
      this.state.nama = null
      this.state.rombongan_belajar_id = null
      this.state.ptk_id = null
      this.state.tanggal = null
      this.data_tingkat= []
      this.data_ptk= []
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.loading_modal = true
      this.$http.post('/jadwal/simpan-jadwal', this.form).then(response => {
        let data = response.data
        this.loading_modal = false
        if(data.errors){
          this.state.nama = (data.errors.nama) ? false : null
          this.state.rombongan_belajar_id = (data.errors.rombongan_belajar_id) ? false : null
          this.state.ptk_id = (data.errors.ptk_id) ? false : null
          this.state.tanggal = (data.errors.tanggal) ? false : null
          this.feedback.nama = (data.errors.nama) ? data.errors.nama.join(', ') : ''
          this.feedback.rombongan_belajar_id = (data.errors.rombongan_belajar_id) ? data.errors.rombongan_belajar_id.join(', ') : ''
          this.feedback.ptk_id = (data.errors.ptk_id) ? data.errors.ptk_id.join(', ') : ''
          this.feedback.tanggal = (data.errors.tanggal) ? data.errors.tanggal.join(', ') : ''
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
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
  },
}
</script>