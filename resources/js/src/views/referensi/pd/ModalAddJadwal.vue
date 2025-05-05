<template>
  <b-modal v-model="addModalFormJadwal" :title="title" size="lg" @show="resetModal" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Hari" label-for="hari_id" label-cols-md="4" :invalid-feedback="feedback.hari_id" :state="state.hari_id">
              <v-select id="hari_id" v-model="form.hari_id" :reduce="nama => nama.id" label="nama" :options="data_hari" placeholder="== Pilih Hari ==" :state="state.hari_id">
                <template slot="no-options">
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Jam Ke" label-for="jam_ke" label-cols-md="4" :invalid-feedback="feedback.jam_ke" :state="state.jam_ke">
              <b-form-input type="number" v-model="form.jam_ke" :state="state.jam_ke"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Kelompok Mata Pelajaran" label-for="kelompok_id" label-cols-md="4" :invalid-feedback="feedback.kelompok_id" :state="state.kelompok_id">
              <v-select id="kelompok_id" v-model="form.kelompok_id" :reduce="nama => nama.id" label="nama" :options="data_kelompok" placeholder="== Pilih Kelompok ==" :state="state.kelompok_id" @input="changeKelompok">
                <template slot="no-options">
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Mata Pelajaran" label-for="mata_pelajaran_id" label-cols-md="4" :invalid-feedback="feedback.mata_pelajaran_id" :state="state.mata_pelajaran_id">
              <b-overlay :show="loading_mapel" opacity="0.6" size="md" spinner-variant="secondary">
                <v-select id="mata_pelajaran_id" v-model="form.mata_pelajaran_id" :reduce="nama => nama.id" label="nama" :options="data_mata_pelajaran" placeholder="== Pilih Mata Pelajaran ==" :state="state.mata_pelajaran_id">
                  <template slot="no-options">
                    Tidak ada data untuk ditampilkan
                  </template>
                  <template slot="option" slot-scope="option">
                    <div class="d-center">
                      ({{ option.kode }}) {{ option.nama }}
                    </div>
                  </template>
                  <template slot="selected-option" slot-scope="option">
                    <div class="selected d-center">
                      ({{ option.kode }}) {{ option.nama }}
                    </div>
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
import { BOverlay, BForm, BFormGroup, BFormInput, BButton, BRow, BCol } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    vSelect, BOverlay, BForm, BFormGroup, BFormInput, BButton, BRow, BCol,
  },
  data() {
    return {
      addModalFormJadwal: false,
      loading_form: false,
      loading_mapel: false,
      title: '',
      form: {
        hari_id: '',
        jam_ke: '',
        kelompok_id: '',
        mata_pelajaran_id: '',
      },
      feedback: {
        hari_id: '',
        jam_ke: '',
        kelompok_id: '',
        mata_pelajaran_id: '',
      },
      state: {
        hari_id: null,
        jam_ke: null,
        kelompok_id: null,
        mata_pelajaran_id: null,
      },
      data_hari: [],
      data_kelompok: [],
      data_mata_pelajaran: [],
    }
  },
  created() {
    eventBus.$on('open-modal-form-jadwal', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.form.aksi = data.aksi
      this.form.rombongan_belajar_id = data.rombongan_belajar_id
      this.title = `Tambah Jadwal ${data.aksi.toUpperCase()} Kelas ${data.nama}`
      this.addModalFormJadwal = true
      this.loading_form = true
      this.$http.get('/jadwal/get-data').then(response => {
        this.loading_form = false
        let getData = response.data
        this.data_hari = getData.data_hari
        this.data_kelompok = getData.data_kelompok
      }).catch(error => {
        console.log(error);
      })
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/jadwal/simpan', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.feedback.hari_id = (getData.errors.hari_id) ? getData.errors.hari_id.join(', ') : ''
          this.state.hari_id = (getData.errors.hari_id) ? false : null
          this.feedback.jam_ke = (getData.errors.jam_ke) ? getData.errors.jam_ke.join(', ') : ''
          this.state.jam_ke = (getData.errors.jam_ke) ? false : null
          this.feedback.kelompok_id = (getData.errors.kelompok_id) ? getData.errors.kelompok_id.join(', ') : ''
          this.state.kelompok_id = (getData.errors.kelompok_id) ? false : null
          this.feedback.mata_pelajaran_id = (getData.errors.mata_pelajaran_id) ? getData.errors.mata_pelajaran_id.join(', ') : ''
          this.state.mata_pelajaran_id = (getData.errors.mata_pelajaran_id) ? false : null
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            html: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
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
    resetModal(){
      this.form.hari_id = ''
      this.form.jam_ke = ''
      this.form.kelompok_id = ''
      this.form.mata_pelajaran_id = ''
      this.feedback.hari_id = ''
      this.feedback.jam_ke = ''
      this.feedback.kelompok_id = ''
      this.feedback.mata_pelajaran_id = ''
      this.state.hari_id = null
      this.state.jam_ke = null
      this.state.kelompok_id = null
      this.state.mata_pelajaran_id = null
    },
    hideModal(){
      this.addModalFormJadwal = false
      this.resetModal()
    },
    changeKelompok(val){
      this.form.mata_pelajaran_id = ''
      if(val){
        this.loading_mapel = true
        this.$http.post('/jadwal/get-mapel', this.form).then(response => {
          this.loading_mapel = false
          let getData = response.data
          this.data_mata_pelajaran = getData
        }).catch(error => {
          console.log(error);
        })
      }
    }
  },
}
</script>