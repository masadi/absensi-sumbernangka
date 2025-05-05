<template>
  <b-modal v-model="addModalShow" :title="title" size="lg" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Unit" label-for="sekolah_id" label-cols-md="4" :invalid-feedback="feedback.sekolah_id" :state="state.sekolah_id">
              <v-select id="sekolah_id" v-model="form.sekolah_id" :reduce="nama => nama.sekolah_id" label="nama" :options="data_sekolah" placeholder="== Pilih Unit ==" :state="state.sekolah_id">
                <template slot="no-options">
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Jumlah Siswa" label-for="jumlah" label-cols-md="4" :invalid-feedback="feedback.jumlah" :state="state.jumlah">
              <b-form-input type="number" v-model="form.jumlah" :state="state.jumlah" @input="inputJumlah"></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12" v-if="link_excel">
            <b-form-group label="Unduh Template" label-cols-md="4">
              <b-button size="sm" block variant="primary" :href="link_excel">Unduh Template</b-button>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="File Zip" label-for="tingkat" label-cols-md="4" :invalid-feedback="feedback.file" :state="state.file">
              <b-form-file v-model="form.file" :state="state.file" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
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
import _ from 'lodash'
import { BOverlay, BForm, BRow, BCol, BFormGroup, BFormFile, BButton, BFormInput } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    vSelect,
    BOverlay,
    BForm,
    BRow,
    BCol,
    BFormGroup,
    BFormFile,
    BFormInput,
    BButton,
  },
  data() {
    return {
      loading_form: false,
      loading_tingkat: false,
      loading_rombel: false,
      form: {
        file: null,
        sekolah_id: '',
        jumlah: 0,
      },
      feedback: {
        file: '',
        sekolah_id: '',
        jumlah: null,
      },
      state: {
        file: null,
        sekolah_id: null,
        jumlah: null,
      },
      data_sekolah: [],
      addModalShow: false,
      title: 'Import Template Peserta Didik',
      link_excel: null,
    }
  },
  created() {
    eventBus.$on('open-modal-import-pd', this.handleEvent);
  },
  methods: {
    handleEvent(){
      this.$http.get('/referensi/sekolah').then(response => {
        this.data_sekolah = response.data
        this.addModalShow = true
      });
    },
    onFileChange(e) {
      this.form.file = e.target.files[0];
    },
    hideModal(){
      this.addModalShow = false
      this.resetModal()
    },
    resetModal(){
      this.form.file = null
      this.form.sekolah_id = ''
      this.form.jumlah = 0
      this.feedback.file = ''
      this.feedback.jumlah = null
      this.state.file = null
      this.feedback.sekolah_id = ''
      this.state.sekolah_id = null
      this.state.jumlah = null
      this.link_excel = null
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      const data = new FormData();
      data.append('file_excel', (this.form.file) ? this.form.file : '');
      data.append('sekolah_id', this.form.sekolah_id);
      data.append('jumlah', this.form.jumlah)
      this.$http.post('/referensi/import-pd', data).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.feedback.sekolah_id = (getData.errors.sekolah_id) ? getData.errors.sekolah_id.join(', ') : ''
          this.feedback.jumlah = (getData.errors.jumlah) ? getData.errors.jumlah.join(', ') : ''
          this.feedback.file = (getData.errors.file_excel) ? getData.errors.file_excel.join(', ') : ''
          this.state.sekolah_id = (getData.errors.sekolah_id) ? false : null
          this.state.jumlah = (getData.errors.jumlah) ? false : null
          this.state.file = (getData.errors.file_excel) ? false : null
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
    inputJumlah: _.debounce(function (e) {
      this.link_excel = `/unduhan/template-pd/${e}`
    }, 500),
  },
}
</script>