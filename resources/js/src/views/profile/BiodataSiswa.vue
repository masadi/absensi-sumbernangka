<template>
  <b-modal v-model="editModalShow" title="Edit Data Peserta Didik" size="xl" @show="resetForm" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Nama Lengkap" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama Lengkap dengan gelar"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="NIK" label-for="nik" label-cols-md="3" :invalid-feedback="feedback.nik" :state="state.nik">
              <b-form-input id="nik" v-model="form.nik" placeholder="NIK"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nomor Induk" label-for="no_induk" label-cols-md="3" :invalid-feedback="feedback.no_induk" :state="state.no_induk">
              <b-form-input id="no_induk" v-model="form.no_induk" placeholder="Nomor Induk"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="NISN" label-for="nisn" label-cols-md="3" :invalid-feedback="feedback.nisn" :state="state.nisn">
              <b-form-input id="nisn" v-model="form.nisn" placeholder="NISN"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Jenis Kelamin" label-for="jenis_kelamin" label-cols-md="3" :invalid-feedback="feedback.jenis_kelamin" :state="state.jenis_kelamin">
              <v-select id="jenis_kelamin" v-model="form.jenis_kelamin" :reduce="name => name.code" label="name" :options="[{name: 'Laki-laki', code: 'L'}, {name: 'Perempuan', code: 'P'}]" placeholder="== Pilih Jenis Kelamin ==" :state="state.jenis_kelamin">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tempat Lahir" label-for="tempat_lahir" label-cols-md="3" :invalid-feedback="feedback.tempat_lahir" :state="state.tempat_lahir">
              <b-form-input id="nama" v-model="form.tempat_lahir" :state="state.tempat_lahir" placeholder="Tempat Lahir"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tanggal Lahir*" label-for="tanggal_lahir" label-cols-md="3" :invalid-feedback="feedback.tanggal_lahir" :state="state.tanggal_lahir">
              <b-form-datepicker v-model="form.tanggal_lahir" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal_lahir" @context="onContext" placeholder="== Pilih Tanggal Lahir ==" :state="state.tanggal_lahir" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Agama" label-for="agama_id" label-cols-md="3" :invalid-feedback="feedback.agama_id" :state="state.agama_id">
              <v-select id="agama_id" v-model="form.agama_id" :reduce="nama => nama.agama_id" label="nama" :options="data_agama" placeholder="== Pilih Agama ==" :state="state.agama_id">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Alamat" label-for="alamat_jalan" label-cols-md="3" :invalid-feedback="feedback.alamat_jalan" :state="state.alamat_jalan">
              <b-form-input id="nama" v-model="form.alamat_jalan" :state="state.alamat_jalan" placeholder="Alamat"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="RT/RW" label-for="rt" label-cols-md="3" :invalid-feedback="feedback.rt || feedback.rw" :state="state.rt || state.rw">
              <b-input-group>
                <b-form-input placeholder="RT" v-model="form.rt" :state="state.rt"></b-form-input>
                <b-form-input placeholder="RW" v-model="form.rw" :state="state.rw"></b-form-input>
              </b-input-group>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Provinsi" label-for="provinsi_id" label-cols-md="3" :invalid-feedback="feedback.provinsi_id" :state="state.provinsi_id">
              <b-overlay :show="loading_provinsi_id" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="provinsi_id" v-model="form.provinsi_id" :reduce="name => name.code" label="name" :options="data_provinsi" placeholder="== Pilih Provinsi ==" @input="changeProvinsi" :state="state.provinsi_id">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Kabupaten/Kota" label-for="provinsi_id" label-cols-md="3" :invalid-feedback="feedback.kabupaten_id" :state="state.kabupaten_id">
              <b-overlay :show="loading_kabupaten_id" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="kabupaten_id" v-model="form.kabupaten_id" :reduce="name => name.code" label="name" :options="data_kabupaten" placeholder="== Pilih Kabupaten/Kota ==" @input="changeKabupaten" :state="state.kabupaten_id">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Kecamatan" label-for="kecamatan_id" label-cols-md="3" :invalid-feedback="feedback.kecamatan_id" :state="state.kecamatan_id">
              <b-overlay :show="loading_kecamatan_id" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="kecamatan_id" v-model="form.kecamatan_id" :reduce="name => name.code" label="name" :options="data_kecamatan" placeholder="== Pilih Kecamatan ==" @input="changeKecamatan" :state="state.kecamatan_id">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Desa/Kelurahan" label-for="desa_id" label-cols-md="3" :invalid-feedback="feedback.desa_id" :state="state.desa_id">
              <b-overlay :show="loading_desa_id" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="desa_id" v-model="form.desa_id" :reduce="name => name.code" label="name" :options="data_desa" placeholder="== Pilih Desa/Kelurahan ==" :state="state.desa_id">
                  <template #no-options>
                    Tidak ada data untuk ditampilkan
                  </template>
                </v-select>
              </b-overlay>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Cita-cita" label-for="cita" label-cols-md="3" :invalid-feedback="feedback.cita" :state="state.cita">
              <v-select id="cita" v-model="form.cita" :reduce="nama => nama.nama" label="nama" :options="data_cita" placeholder="== Pilih Cita-cita ==" :state="state.cita">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nomor HP" label-for="no_hp" label-cols-md="3" :invalid-feedback="feedback.no_hp" :state="state.no_hp">
              <b-form-input id="no_hp" v-model="form.no_hp" :state="state.no_hp" placeholder="Nomor HP"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Sekolah Asal" label-for="sekolah_asal" label-cols-md="3" :invalid-feedback="feedback.sekolah_asal" :state="state.sekolah_asal">
              <b-form-input id="sekolah_asal" v-model="form.sekolah_asal" :state="state.sekolah_asal" placeholder="Sekolah Asal"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tanggal Masuk Sekolah" label-for="diterima" label-cols-md="3" :invalid-feedback="feedback.diterima" :state="state.diterima">
              <b-form-datepicker v-model="form.diterima" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="diterima" @context="onContext" placeholder="== Pilih Tanggal Masuk Sekolah ==" :state="state.diterima" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Diterima Dikelas" label-for="diterima_kelas" label-cols-md="3" :invalid-feedback="feedback.diterima_kelas" :state="state.diterima_kelas">
              <b-form-input id="diterima_kelas" v-model="form.diterima_kelas" :state="state.diterima_kelas" placeholder="Diterima Dikelas"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Email" label-for="email" label-cols-md="3" :invalid-feedback="feedback.email" :state="state.email">
              <b-form-input id="email" v-model="form.email" :state="state.email" placeholder="Email Valid"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nama Ayah" label-for="nama_ayah" label-cols-md="3" :invalid-feedback="feedback.nama_ayah" :state="state.nama_ayah">
              <b-form-input id="nama_ayah" v-model="form.nama_ayah" :state="state.nama_ayah" placeholder="Nama Ayah"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nama Ibu" label-for="nama_ibu" label-cols-md="3" :invalid-feedback="feedback.nama_ibu" :state="state.nama_ibu">
              <b-form-input id="nama_ibu" v-model="form.nama_ibu" :state="state.nama_ibu" placeholder="Nama Ibu"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Pekerjaan Ayah" label-for="kerja_ayah" label-cols-md="3" :invalid-feedback="feedback.kerja_ayah" :state="state.kerja_ayah">
              <v-select id="kerja_ayah" v-model="form.kerja_ayah" :reduce="nama => nama.pekerjaan_id" label="nama" :options="data_pekerjaan" placeholder="== Pilih Pekerjaan Ayah ==" :state="state.kerja_ayah">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Pekerjaan Ibu" label-for="kerja_ibu" label-cols-md="3" :invalid-feedback="feedback.kerja_ibu" :state="state.kerja_ibu">
              <v-select id="kerja_ibu" v-model="form.kerja_ibu" :reduce="nama => nama.pekerjaan_id" label="nama" :options="data_pekerjaan" placeholder="== Pilih Pekerjaan Ibu ==" :state="state.kerja_ibu">
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
import { BOverlay, BForm, BFormInput, BInputGroup, BRow, BCol, BFormGroup, BButton, BFormDatepicker } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BOverlay,
    BForm,
    BFormInput,
    BInputGroup,
    BRow,
    BCol,
    BFormGroup,
    BButton,
    BFormDatepicker,
    vSelect,
  },
  data() {
    return {
      editModalShow: false,
      loading_form: false,
      form: {},
      feedback: {},
      state: {},
      loading_provinsi_id: false,
      loading_kabupaten_id: false,
      loading_kecamatan_id: false,
      loading_desa_id: false,
      data_provinsi: [],
      data_kabupaten: [],
      data_kecamatan: [],
      data_desa: [],
      data_agama: [],
      data_cita: [],
      data_pekerjaan: [],
    }
  },
  created() {
    eventBus.$on('open-modal-edit-pd', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.$http.post('/referensi/detil-data', {
        id: data.peserta_didik_id,
        data: 'pd',
        edit: true,
      }).then(response => {
        var getData = response.data
        if(getData.errors){
          this.$swal({
            icon: 'error',
            title: 'Error',
            text: getData.errors,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          })
        } else {
          this.form = getData.pd
          this.form.data = 'pd'
          this.data_provinsi = getData.provinsi
          this.data_kabupaten = getData.kabupaten
          this.data_kecamatan = getData.kecamatan
          this.data_desa = getData.desa
          this.data_agama = getData.agama
          this.data_cita = getData.cita
          this.data_pekerjaan = getData.pekerjaan
          this.editModalShow = true
        }
      })
    },
    hideModal(){
      this.editModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.feedback.semester_id = ''
      this.state.tanggal_cetak = null
    },
    changeProvinsi(val){
      this.loading_kabupaten_id = true
      this.$http.post('/referensi/wilayah', {
        provinsi_id: val,
      }).then(response => {
        this.loading_kabupaten_id = false
        this.data_kabupaten = response.data
      });
    },
    changeKabupaten(val){
      this.loading_kecamatan_id = true
      this.$http.post('/referensi/wilayah', {
        kabupaten_id: val,
      }).then(response => {
        this.loading_kecamatan_id = false
        this.data_kecamatan = response.data
      });
    },
    changeKecamatan(val){
      this.loading_desa_id = true
      this.$http.post('/referensi/wilayah', {
        kecamatan_id: val,
      }).then(response => {
        this.loading_desa_id = false
        this.data_desa = response.data
      });
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/referensi/update-data', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.state.nik = (getData.errors.nik) ? false : null
          this.state.tempat_lahir = (getData.errors.tempat_lahir) ? false : null
          this.state.tanggal_lahir = (getData.errors.tanggal_lahir) ? false : null
          this.state.agama_id = (getData.errors.agama_id) ? false : null
          this.state.alamat_jalan = (getData.errors.alamat_jalan) ? false : null
          this.state.rt = (getData.errors.rt) ? false : null
          this.state.rw = (getData.errors.rw) ? false : null
          this.state.email = (getData.errors.email) ? false : null
          this.state.jenis_kelamin = (getData.errors.jenis_kelamin) ? false : null
          this.state.provinsi_id = (getData.errors.provinsi_id) ? false : null
          this.state.kabupaten_id = (getData.errors.kabupaten_id) ? false : null
          this.state.kecamatan_id = (getData.errors.kecamatan_id) ? false : null
          this.state.desa_id = (getData.errors.desa_id) ? false : null
          this.state.no_hp = (getData.errors.no_hp) ? false : null
          this.state.sekolah_asal = (getData.errors.sekolah_asal) ? false : null
          this.state.diterima = (getData.errors.diterima) ? false : null
          this.state.diterima_kelas = (getData.errors.diterima_kelas) ? false : null
          this.state.nama_ayah = (getData.errors.nama_ayah) ? false : null
          this.state.nama_ibu = (getData.errors.nama_ibu) ? false : null
          this.state.kerja_ayah = (getData.errors.kerja_ayah) ? false : null
          this.state.kerja_ibu = (getData.errors.kerja_ibu) ? false : null
          this.state.cita = (getData.errors.cita) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.feedback.nik = (getData.errors.nik) ? getData.errors.nik.join(', ') : ''
          this.feedback.nuptk = (getData.errors.nuptk) ? getData.errors.nuptk.join(', ') : ''
          this.feedback.nip = (getData.errors.nip) ? getData.errors.nip.join(', ') : ''
          this.feedback.tempat_lahir = (getData.errors.tempat_lahir) ? getData.errors.tempat_lahir.join(', ') : ''
          this.feedback.tanggal_lahir = (getData.errors.tanggal_lahir) ? getData.errors.tanggal_lahir.join(', ') : ''
          this.feedback.agama_id = (getData.errors.agama_id) ? getData.errors.agama_id.join(', ') : ''
          this.feedback.alamat_jalan = (getData.errors.alamat_jalan) ? getData.errors.alamat_jalan.join(', ') : ''
          this.feedback.rt = (getData.errors.rt) ? getData.errors.rt.join(', ') : ''
          this.feedback.rw = (getData.errors.rw) ? getData.errors.rw.join(', ') : ''
          this.feedback.email = (getData.errors.email) ? getData.errors.email.join(', ') : ''
          this.feedback.jenis_kelamin = (getData.errors.jenis_kelamin) ? getData.errors.jenis_kelamin.join(', ') : ''
          this.feedback.provinsi_id = (getData.errors.provinsi_id) ? getData.errors.provinsi_id.join(', ') : ''
          this.feedback.kabupaten_id = (getData.errors.kabupaten_id) ? getData.errors.kabupaten_id.join(', ') : ''
          this.feedback.kecamatan_id = (getData.errors.kecamatan_id) ? getData.errors.kecamatan_id.join(', ') : ''
          this.feedback.desa_id = (getData.errors.desa_id) ? getData.errors.desa_id.join(', ') : ''
          this.feedback.no_hp = (getData.errors.no_hp) ? getData.errors.no_hp.join(', ') : ''
          this.feedback.sekolah_asal = (getData.errors.sekolah_asal) ? getData.errors.sekolah_asal.join(', ') : ''
          this.feedback.diterima = (getData.errors.diterima) ? getData.errors.diterima.join(', ') : ''
          this.feedback.diterima_kelas = (getData.errors.diterima_kelas) ? getData.errors.diterima_kelas.join(', ') : ''
          this.feedback.nama_ayah = (getData.errors.nama_ayah) ? getData.errors.nama_ayah.join(', ') : ''
          this.feedback.nama_ibu = (getData.errors.nama_ibu) ? getData.errors.nama_ibu.join(', ') : ''
          this.feedback.kerja_ayah = (getData.errors.kerja_ayah) ? getData.errors.kerja_ayah.join(', ') : ''
          this.feedback.kerja_ibu = (getData.errors.kerja_ibu) ? getData.errors.kerja_ibu.join(', ') : ''
          this.feedback.cita = (getData.errors.cita) ? getData.errors.cita.join(', ') : ''
          
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