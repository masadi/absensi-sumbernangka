<template>
  <b-modal v-model="addModalShow" title="Tambah Soal Ujian" size="lg" @ok="handleOk" @hidden="hideModal">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form @submit.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Unit" label-for="sekolah_id" :invalid-feedback="feedback.sekolah_id" :state="state.sekolah_id">
              <v-select id="sekolah_id" v-model="form.sekolah_id" :options="data_sekolah" :reduce="nama => nama.sekolah_id" label="nama" placeholder="== Pilih Unit ==" @input="changeSekolah" :disabled="isDisable">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Tingkat Kelas" label-for="tingkat" :invalid-feedback="feedback.tingkat" :state="state.tingkat">
              <b-overlay :show="loading_tingkat" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="tingkat" v-model="form.tingkat" :options="data_tingkat" :reduce="nama => nama.tingkat_pendidikan_id" label="nama" placeholder="== Pilih Tingkat Kelas ==" @input="changeTingkat">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Rombongan Belajar" label-for="rombongan_belajar_id" :invalid-feedback="feedback.rombongan_belajar_id" :state="state.rombongan_belajar_id">
              <b-overlay :show="loading_rombel" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="rombongan_belajar_id" v-model="form.rombongan_belajar_id" :options="data_rombel" :reduce="nama => nama.rombongan_belajar_id" label="nama" placeholder="== Pilih Rombongan Belajar ==" @input="changeRombel">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Jadwal Ujian" label-for="jadwal_id" :invalid-feedback="feedback.jadwal_id" :state="state.jadwal_id">
              <b-overlay :show="loading_jadwal" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="jadwal_id" v-model="form.jadwal_id" :options="data_jadwal" :reduce="nama => nama.id" label="nama" placeholder="== Pilih Jadwal Ujian ==" @input="changeJadwal">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Mata Ujian" label-for="jadwal_ujian_id" :invalid-feedback="feedback.jadwal_ujian_id" :state="state.jadwal_ujian_id">
              <b-overlay :show="loading_ujian" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="jadwal_ujian_id" v-model="form.jadwal_ujian_id" :options="data_ujian" :reduce="nama => nama.id" label="nama" placeholder="== Pilih Mata Ujian ==">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                  <template #option="{ nama, mata_pelajaran }">
                    <h3 style="margin: 0">{{ mata_pelajaran.nama }}</h3>
                    <em>{{ mata_pelajaran.nama }} {{ mata_pelajaran.kode }}</em>
                  </template>
                  <template #selected-option="{ nama, mata_pelajaran }">
                    <div style="display: flex; align-items: baseline">
                      <strong>{{ mata_pelajaran.nama }}</strong>
                      <em style="margin-left: 0.5rem">({{ mata_pelajaran.kode }})</em>
                    </div>
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Jumlah Soal" label-for="jumlah_soal" :invalid-feedback="feedback.jumlah_soal" :state="state.jumlah_soal">
              <b-form-input v-model="form.jumlah_soal" placeholder="Harus terisi angka" :state="state.jumlah_soal"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Lama Ujian (dalam menit)" label-for="waktu_ujian" :invalid-feedback="feedback.waktu_ujian" :state="state.waktu_ujian">
              <b-form-input v-model="form.waktu_ujian" placeholder="Harus terisi angka" :state="state.waktu_ujian"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Skor Jawaban Benar" label-for="skor_benar" :invalid-feedback="feedback.skor_benar" :state="state.waktu_ujian">
              <b-form-input v-model="form.skor_benar" placeholder="Harus terisi angka" :state="state.skor_benar"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Skor Jawaban Salah" label-for="skor_salah" :invalid-feedback="feedback.skor_salah" :state="state.skor_salah">
              <b-form-input v-model="form.skor_salah" placeholder="Harus terisi angka" :state="state.skor_salah"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Skala Nilai" label-for="skala_nilai" :invalid-feedback="feedback.skala_nilai" :state="state.skala_nilai">
              <b-form-input v-model="form.skala_nilai" placeholder="Harus terisi angka" :state="state.skala_nilai"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="4" label="Opsi Pilihan Ganda" label-for="jumlah_pg" :invalid-feedback="feedback.jumlah_pg" :state="state.jumlah_pg">
              <v-select id="jumlah_pg" v-model="form.jumlah_pg" :options="[{code: 3, label:'A, B, C'}, {code: 4, label:'A, B, C, D'}, {code: 5, label:'A, B, C, D, E'}]" :reduce="label => label.code" placeholder="== Pilih Opsi Pilihan Ganda =="></v-select>
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
      isDisable: false,
      addModalShow: false,
      loading_modal: false,
      loading_tingkat: false,
      loading_rombel: false,
      loading_jadwal: false,
      loading_ujian: false,
      form: {
          semester_id: '',
          sekolah_id: '',
          tingkat: '',
          rombongan_belajar_id: '',
          jadwal_id: '',
          jadwal_ujian_id: '',
          jumlah_soal: 50,
          waktu_ujian: 120,
          skor_benar: 2,
          skor_salah: 0,
          skala_nilai: 100,
          jumlah_pg: '',
      },
      feedback: {
        sekolah_id: '',
        tingkat: '',
        rombongan_belajar_id: '',
        jadwal_id: '',
        jadwal_ujian_id: '',
        jumlah_soal: '',
        waktu_ujian: '',
        skor_benar: '',
        skor_salah: '',
        skala_nilai: '',
        jumlah_pg: '',
      },
      state: {
        sekolah_id: null,
        tingkat: null,
        rombongan_belajar_id: null,
        jadwal_id: null,
        jadwal_ujian_id: null,
        jumlah_soal: null,
        waktu_ujian: null,
        skor_benar: null,
        skor_salah: null,
        skala_nilai: null,
        jumlah_pg: null,
      },
      data_sekolah: [],
      data_tingkat: [],
      data_rombel: [],
      data_jadwal: [],
      data_ujian: [],
    }
  },
  created() {
    eventBus.$on('open-modal-add-soal', this.handleEvent);
  },
  methods: {
    handleEvent(){
      //this.resetForm()
      this.$http.post('/soal-ujian/referensi').then(response => {
        let getData = response.data
        this.data_sekolah = getData.data_sekolah
        this.form.semester_id = this.user.semester.semester_id
        this.addModalShow = true
      });
    },
    hideModal(){
      this.addModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form = {
        semester_id: '',
        sekolah_id: '',
        tingkat: '',
        rombongan_belajar_id: '',
        jadwal_id: '',
        jadwal_ujian_id: '',
        jumlah_soal: 50,
        waktu_ujian: 120,
        skor_benar: 2,
        skor_salah: 0,
        skala_nilai: 100,
        jumlah_pg: '',
      }
      this.feedback = {
        sekolah_id: '',
        tingkat: '',
        rombongan_belajar_id: '',
        jadwal_id: '',
        jadwal_ujian_id: '',
        jumlah_soal: '',
        waktu_ujian: '',
        skor_benar: '',
        skor_salah: '',
        skala_nilai: '',
        jumlah_pg: '',
      }
      this.state = {
        sekolah_id: null,
        tingkat: null,
        rombongan_belajar_id: null,
        jadwal_id: null,
        jadwal_ujian_id: null,
        jumlah_soal: null,
        waktu_ujian: null,
        skor_benar: null,
        skor_salah: null,
        skala_nilai: null,
        jumlah_pg: null,
      }
      this.data_sekolah = []
      this.data_tingkat = []
      this.data_rombel = []
      this.data_jadwal = []
      this.data_ujian = []
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.loading_modal = true
      this.$http.post('/soal-ujian/store', this.form).then(response => {
        let data = response.data
        this.loading_modal = false
        if(data.errors){
          this.state.sekolah_id = (data.errors.sekolah_id) ? false : null
          this.state.tingkat = (data.errors.tingkat) ? false : null
          this.state.rombongan_belajar_id = (data.errors.rombongan_belajar_id) ? false : null
          this.state.jadwal_id
          this.state.jadwal_ujian_id = (data.errors.jadwal_ujian_id) ? false : null
          this.state.jumlah_soal = (data.errors.jumlah_soal) ? false : null
          this.state.waktu_ujian = (data.errors.waktu_ujian) ? false : null
          this.state.skor_benar = (data.errors.skor_benar) ? false : null
          this.state.skor_salah = (data.errors.skor_salah) ? false : null
          this.state.skala_nilai = (data.errors.skala_nilai) ? false : null
          this.state.jumlah_pg = (data.errors.jumlah_pg) ? false : null
          this.feedback.sekolah_id = (data.errors.sekolah_id) ? data.errors.sekolah_id.join(', ') : ''
          this.feedback.tingkat = (data.errors.tingkat) ? data.errors.tingkat.join(', ') : ''
          this.feedback.rombongan_belajar_id = (data.errors.rombongan_belajar_id) ? data.errors.rombongan_belajar_id.join(', ') : ''
          this.feedback.jadwal_id = (data.errors.jadwal_id) ? data.errors.jadwal_id.join(', ') : ''
          this.feedback.jadwal_ujian_id = (data.errors.jadwal_ujian_id) ? data.errors.jadwal_ujian_id.join(', ') : ''
          this.feedback.jumlah_soal = (data.errors.jumlah_soal) ? data.errors.jumlah_soal.join(', ') : ''
          this.feedback.waktu_ujian = (data.errors.waktu_ujian) ? data.errors.waktu_ujian.join(', ') : ''
          this.feedback.skor_benar = (data.errors.skor_benar) ? data.errors.skor_benar.join(', ') : ''
          this.feedback.skor_salah = (data.errors.skor_salah) ? data.errors.skor_salah.join(', ') : ''
          this.feedback.skala_nilai = (data.errors.skala_nilai) ? data.errors.skala_nilai.join(', ') : ''
          this.feedback.jumlah_pg = (data.errors.jumlah_pg) ? data.errors.jumlah_pg.join(', ') : ''
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
    changeSekolah(val){
      this.data_tingkat = []
      this.data_rombel = []
      this.data_ujian = []
      this.form.tingkat = ''
      this.form.rombongan_belajar_id = ''
      this.form.jadwal_ujian_id = ''
      if(val){
        this.loading_tingkat = true
        this.$http.post('/soal-ujian/referensi', this.form).then(response => {
          let getData = response.data
          this.loading_tingkat = false
          this.data_tingkat = getData.data_tingkat
        })
      }
    },
    changeTingkat(val){
      this.data_rombel = []
      this.data_ujian = []
      this.form.rombongan_belajar_id = ''
      this.form.jadwal_ujian_id = ''
      if(val){
        this.loading_rombel = true
        this.$http.post('/soal-ujian/referensi', this.form).then(response => {
          let getData = response.data
          this.loading_rombel = false
          this.data_rombel = getData.data_rombel
        })
      }
    },
    changeRombel(val){
      this.data_jadwal = []
      this.data_ujian = []
      this.form.jadwal_id = ''
      this.form.jadwal_ujian_id = ''
      if(val){
        this.loading_jadwal = true
        this.$http.post('/soal-ujian/referensi', this.form).then(response => {
          let getData = response.data
          this.loading_jadwal = false
          this.data_jadwal = getData.data_jadwal
        })
      }
    },
    changeJadwal(val){
      this.data_ujian = []
      this.form.jadwal_ujian_id = ''
      if(val){
        this.loading_ujian = true
        this.$http.post('/soal-ujian/referensi', this.form).then(response => {
          let getData = response.data
          this.loading_ujian = false
          this.data_ujian = getData.data_ujian
        })
      }
    }
  },
}
</script>