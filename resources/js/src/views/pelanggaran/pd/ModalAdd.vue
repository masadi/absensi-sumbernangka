<template>
  <b-modal v-model="addModalShow" title="Tambah Data Pelanggaran" size="xl" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Rombongan Belajar" label-for="rombongan_belajar_id" label-cols-md="3" :invalid-feedback="feedback.rombongan_belajar_id" :state="state.rombongan_belajar_id">
              <b-overlay :show="loading_rombel" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="rombongan_belajar_id" v-model="form.rombongan_belajar_id" :reduce="nama => nama.rombongan_belajar_id" label="nama" :options="data_rombel" placeholder="== Pilih Rombongan Belajar ==" :state="state.rombongan_belajar_id" @input="changeRombel">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Peserta Didik" label-for="anggota_rombel_id" label-cols-md="3" :invalid-feedback="feedback.anggota_rombel_id" :state="state.anggota_rombel_id">
              <b-overlay :show="loading_siswa" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="anggota_rombel_id" v-model="form.anggota_rombel_id" :reduce="nama => nama.anggota_rombel.anggota_rombel_id" label="nama" :options="data_siswa" placeholder="== Pilih Peserta Didik ==" :state="state.anggota_rombel_id">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tanggal Kejadian" label-for="tanggal" label-cols-md="3" :invalid-feedback="feedback.tanggal" :state="state.tanggal">
              <b-form-datepicker v-model="form.tanggal" :state="state.tanggal" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal" @context="onContext" placeholder="Pilih Tanggal Kejadian" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Waktu Kejadian" label-for="waktu" label-cols-md="3" :invalid-feedback="feedback.waktu" :state="state.waktu">
              <b-form-input id="waktu" v-model="form.waktu" :state="state.waktu" placeholder="Waktu Kejadian"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Petugas" label-for="user_id" label-cols-md="3" :invalid-feedback="feedback.user_id" :state="state.user_id">
              <v-select id="user_id" v-model="form.user_id" :reduce="name => name.id" label="name" :options="data_petugas" placeholder="== Pilih Petugas ==" :state="state.user_id">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Masalah/Pelanggaran" label-for="masalah" label-cols-md="3" :invalid-feedback="feedback.masalah" :state="state.masalah">
              <b-form-textarea id="masalah" v-model="form.masalah" placeholder="Masalah/Pelanggaran" :state="state.masalah"></b-form-textarea>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tindak Lanjut" label-for="tindak_lanjut" label-cols-md="3" :invalid-feedback="feedback.tindak_lanjut" :state="state.tindak_lanjut">
              <b-form-textarea id="tindak_lanjut" v-model="form.tindak_lanjut" placeholder="Tindak Lanjut" :state="state.tindak_lanjut"></b-form-textarea>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Catatan" label-for="catatan" label-cols-md="3">
              <b-form-textarea id="catatan" v-model="form.catatan" placeholder="Catatan"></b-form-textarea>
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
          <b-button variant="success" @click="ok()">Perbaharui</b-button>
        </b-overlay>
      </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormInput, BFormTextarea, BRow, BCol, BFormGroup, BButton, BFormDatepicker } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BOverlay,
    BForm,
    BFormInput,
    BFormTextarea,
    BRow,
    BCol,
    BFormGroup,
    BButton,
    BFormDatepicker,
    vSelect,
  },
  data() {
    return {
      addModalShow: false,
      loading_form: false,
      loading_rombel: false,
      loading_siswa: false,
      form: {
        semester_id: '',
        periode_aktif: '',
        rombongan_belajar_id: '',
        anggota_rombel_id: '',
        tanggal: '',
        waktu: '',
        user_id: '',
        masalah: '',
        tindak_lanjut: '',
        catatan: '',
      },
      feedback: {
        rombongan_belajar_id: '',
        anggota_rombel_id: '',
        tanggal: '',
        waktu: '',
        user_id: '',
        masalah: '',
        tindak_lanjut: '',
      },
      state: {
        rombongan_belajar_id: null,
        anggota_rombel_id: null,
        tanggal: null,
        waktu: null,
        user_id: null,
        masalah: null,
        tindak_lanjut: null,
      },
      data_rombel: [],
      data_siswa: [],
      data_petugas: [],
    }
  },
  created() {
    eventBus.$on('open-modal-add-pelanggaran', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.loading_rombel = true
      this.form.semester_id = data.semester_id
      this.form.periode_aktif = data.periode_aktif
      this.$http.post('/referensi/get-rombel', {
        sekolah_id: data.sekolah_id,
        semester_id: data.semester_id,
      }).then(response => {
        this.loading_rombel = false
        let getData = response.data
        this.data_rombel = getData.rombel
        this.form.waktu = getData.waktu
        console.log(getData);
        this.addModalShow = true
      });
    },
    changeRombel(val){
      this.loading_siswa = true
      this.data_siswa = []
      if(val){
        this.$http.post('/referensi/get-siswa', {
          rombongan_belajar_id: val,
          semester_id: this.form.semester_id,
          periode_aktif: this.form.periode_aktif,
        }).then(response => {
          this.loading_siswa = false
          let getData = response.data
          this.data_siswa = getData.siswa
          this.data_petugas = getData.petugas
        });
      }
    },
    hideModal(){
      this.addModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form = {
        semester_id: '',
        rombongan_belajar_id: '',
        anggota_rombel_id: '',
        tanggal: '',
        waktu: '',
        user_id: '',
        masalah: '',
        tindak_lanjut: '',
        catatan: '',
      }
      this.feedback = {
        rombongan_belajar_id: '',
        anggota_rombel_id: '',
        tanggal: '',
        waktu: '',
        user_id: '',
        masalah: '',
        tindak_lanjut: '',
      }
      this.state = {
        rombongan_belajar_id: null,
        anggota_rombel_id: null,
        tanggal: null,
        waktu: null,
        user_id: null,
        masalah: null,
        tindak_lanjut: null,
      }
      this.data_rombel = []
      this.data_siswa = []
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/pelanggaran/store', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.rombongan_belajar_id = (getData.errors.rombongan_belajar_id) ? false : null
          this.state.anggota_rombel_id = (getData.errors.anggota_rombel_id) ? false : null
          this.state.tanggal = (getData.errors.tanggal) ? false : null
          this.state.waktu = (getData.errors.waktu) ? false : null
          this.state.user_id = (getData.errors.user_id) ? false : null
          this.state.masalah = (getData.errors.masalah) ? false : null
          this.state.tindak_lanjut = (getData.errors.tindak_lanjut) ? false : null
          this.feedback.rombongan_belajar_id = (getData.errors.rombongan_belajar_id) ? getData.errors.rombongan_belajar_id.join(', ') : ''
          this.feedback.anggota_rombel_id = (getData.errors.anggota_rombel_id) ? getData.errors.anggota_rombel_id.join(', ') : ''
          this.feedback.tanggal = (getData.errors.tanggal) ? getData.errors.tanggal.join(', ') : ''
          this.feedback.waktu = (getData.errors.waktu) ? getData.errors.waktu.join(', ') : ''
          this.feedback.user_id = (getData.errors.user_id) ? getData.errors.user_id.join(', ') : ''
          this.feedback.masalah = (getData.errors.masalah) ? getData.errors.masalah.join(', ') : ''
          this.feedback.tindak_lanjut = (getData.errors.tindak_lanjut) ? getData.errors.tindak_lanjut.join(', ') : ''
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
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>