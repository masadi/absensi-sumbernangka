<template>
  <b-form @submit="onSubmit">
    <b-overlay :show="loading" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-row>
        <b-col cols="12">
          <b-form-group label="Nama Lengkap" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
            <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama Lengkap dengan gelar"></b-form-input>
          </b-form-group>  
        </b-col>
        <b-col cols="12">
          <b-form-group label="NIK" label-for="nik" label-cols-md="3" :invalid-feedback="feedback.nik" :state="state.nik">
            <b-form-input id="nik" v-model="form.nik" placeholder="NIK" :state="state.nik"></b-form-input>
          </b-form-group>  
        </b-col>
        <b-col cols="12">
          <b-form-group label="NUPTK" label-for="nuptk" label-cols-md="3" :invalid-feedback="feedback.nuptk" :state="state.nuptk">
            <b-form-input id="nuptk" v-model="form.nuptk" placeholder="NUPTK (jika ada)" :state="state.nuptk"></b-form-input>
          </b-form-group>  
        </b-col>
        <b-col cols="12">
          <b-form-group label="NIP" label-for="nip" label-cols-md="3" :invalid-feedback="feedback.nip" :state="state.nip">
            <b-form-input id="nip" v-model="form.nip" placeholder="NIP (jika ada)" :state="state.nip"></b-form-input>
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
            <b-form-input id="tempat_lahir" v-model="form.tempat_lahir" :state="state.tempat_lahir" placeholder="Tempat Lahir"></b-form-input>
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
          <b-form-group label="Nomor HP" label-for="no_hp" label-cols-md="3" :invalid-feedback="feedback.no_hp" :state="state.no_hp">
            <b-form-input id="no_hp" v-model="form.no_hp" :state="state.no_hp" placeholder="Nomor HP"></b-form-input>
          </b-form-group>
        </b-col>
        <b-col cols="12">
          <b-form-group label="Email" label-for="email" label-cols-md="3" :invalid-feedback="feedback.email" :state="state.email">
            <b-form-input id="email" v-model="form.email" :state="state.email" placeholder="Email Valid"></b-form-input>
          </b-form-group>  
        </b-col>
      </b-row>
      <b-row>
        <b-col cols="12">
          <b-form-group label-cols-md="3">
            <b-button type="submit" variant="primary">Simpan</b-button>
          </b-form-group>
        </b-col>
      </b-row>
    </b-overlay>
  </b-form>
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
      loading: false,
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
    }
  },
  created() {
    this.loadPostsData()
    /*this.form.edit = true
    this.form.user_id = this.user.user_id
    this.form.sekolah_id = this.user.sekolah_id
    this.form.semester_id = this.user.semester.semester_id
    this.form.periode_aktif = this.user.semester.nama*/
  },
  methods: {
    loadPostsData(){
      this.loading = true
      this.$http.post('/referensi/detil-data', {
        data: 'guru',
        id: this.user.guru_id,
        edit: true,
      }).then(response => {
        this.loading = false
        let getData = response.data
        this.form = getData.guru
        this.form.data = 'guru'
        this.data_provinsi = getData.provinsi
        this.data_kabupaten = getData.kabupaten
        this.data_kecamatan = getData.kecamatan
        this.data_desa = getData.desa
        this.data_agama = getData.agama
      });
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
    resetForm(){
      this.state = {}
      this.feedback = {}
    },
    onSubmit(event) {
      event.preventDefault()
      this.loading = true
      this.$http.post('/referensi/update-data', this.form).then(response => {
        this.loading = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.state.nik = (getData.errors.nik) ? false : null
          this.state.nuptk = (getData.errors.nuptk) ? false : null
          this.state.nip = (getData.errors.nip) ? false : null
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
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.resetForm()
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