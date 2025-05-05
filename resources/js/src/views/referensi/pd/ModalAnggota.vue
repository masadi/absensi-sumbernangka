<template>
  <b-modal v-model="addModalShow" :title="title" size="lg" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Rombel Saat Ini" label-for="rombel_saat_ini" label-cols-md="4" :invalid-feedback="feedback.rombel_saat_ini" :state="state.rombel_saat_ini">
              <b-form-input v-model="form.rombel_saat_ini" placeholder="Rombel Saat Ini" disabled></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tingkat Kelas" label-for="tingkat" label-cols-md="4" :invalid-feedback="feedback.tingkat" :state="state.tingkat">
              <b-overlay :show="loading_tingkat" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="tingkat" v-model="form.tingkat" :reduce="nama => nama.id" label="nama" :options="data_tingkat" placeholder="== Pilih Tingkat Kelas ==" :state="state.tingkat" @input="changeTingkat">
                  <template slot="no-options">
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Rombel Tujuan" label-for="rombongan_belajar_id" label-cols-md="4" :invalid-feedback="feedback.rombongan_belajar_id" :state="state.rombongan_belajar_id">
              <b-overlay :show="loading_rombel" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="rombongan_belajar_id" v-model="form.rombongan_belajar_id" :reduce="nama => nama.rombongan_belajar_id" label="nama" :options="data_rombel" placeholder="== Pilih Rombel Tujuan ==" :state="state.rombongan_belajar_id">
                  <template slot="no-options">
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
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
import { BOverlay, BForm, BRow, BCol, BFormGroup, BFormInput, BButton } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
import { faL } from '@fortawesome/free-solid-svg-icons'
export default {
  components: {
    vSelect,
    BOverlay,
    BForm,
    BRow,
    BCol,
    BFormGroup,
    BFormInput,
    BButton,
  },
  data() {
    return {
      loading_form: false,
      loading_tingkat: false,
      loading_rombel: false,
      form: {
        semester_id: '',
        peserta_didik_id: '',
        rombel_saat_ini: '',
        tingkat: '',
        rombongan_belajar_id: '',
      },
      feedback: {
        rombel_saat_ini: '',
        tingkat: '',
        rombongan_belajar_id: '',
      },
      state: {
        rombel_saat_ini: null,
        tingkat: null,
        rombongan_belajar_id: null,
      },
      data_tingkat: [],
      data_rombel: [],
      addModalShow: false,
      title: '',
    }
  },
  created() {
    eventBus.$on('open-modal-anggota-pd', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.title = `Pindah Rombel ${data.nama}`
      this.form.semester_id = data.kelas.semester_id
      this.form.peserta_didik_id = data.peserta_didik_id
      this.form.rombel_saat_ini = data.kelas.nama
      this.form.sekolah_id = data.sekolah_id
      this.form.bentuk_pendidikan_id = data.sekolah.bentuk_pendidikan_id
      this.getTingkat()
      this.addModalShow = true
    },
    getTingkat(){
      this.loading_tingkat = true
      this.$http.post('/referensi/get-tingkat', this.form).then(response => {
        this.loading_tingkat = false
        let getData = response.data
        this.data_tingkat = getData
      }).catch(error => {
        console.log(error);
      })
    },
    hideModal(){
      this.addModalShow = false
      this.resetModal()
    },
    resetModal(){
      this.form.peserta_didik_id = ''
      this.form.rombel_saat_ini = ''
      this.form.tingkat = ''
      this.form.rombongan_belajar_id = ''
      this.data_tingkat = []
      this.data_rombel = []
    },
    changeTingkat(val){
      this.form.rombongan_belajar_id = ''
      if(val){
        this.loading_rombel = true
        this.$http.post('/referensi/get-rombel', this.form).then(response => {
          this.loading_rombel = false
          let getData = response.data
          this.data_rombel = getData.rombel
        }).catch(error => {
          console.log(error);
        })
      }
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/referensi/pindah-rombel', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.feedback.tingkat = (getData.errors.tingkat) ? getData.errors.tingkat.join(', ') : ''
          this.state.tingkat = (getData.errors.tingkat) ? false : null
          this.feedback.rombongan_belajar_id = (getData.errors.rombongan_belajar_id) ? getData.errors.rombongan_belajar_id.join(', ') : ''
          this.state.rombongan_belajar_id = (getData.errors.rombongan_belajar_id) ? false : null
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
            buttonsStyling: false,
            allowOutsideClick: false,
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