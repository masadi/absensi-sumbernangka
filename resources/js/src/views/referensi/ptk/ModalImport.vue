<template>
  <b-modal v-model="importModalShow" title="Tambah Data PTK" @show="resetForm" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-button block variant="primary" href="/templates/Template-PTK.xlsx" target="_blank">Download Template Excel</b-button>
          </b-col>
          <b-col cols="12" class="my-2">
            <b-form-group :invalid-feedback="feedback_sekolah_id" :state="state_sekolah_id">
              <v-select id="sekolah_id" v-model="sekolah_id" :reduce="nama => nama.sekolah_id" label="nama" :options="data_sekolah" placeholder="== Pilih Unit ==" :state="state_sekolah_id">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group :invalid-feedback="feedback_file" :state="state_file">
              <b-form-file v-model="file1" :state="state_file" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
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
          <b-button variant="success" @click="ok()">Berikutnya</b-button>
        </b-overlay>
      </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormGroup, BFormFile, BRow, BCol, BButton } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BOverlay,
    BForm,
    BFormGroup,
    BFormFile,
    BRow,
    BCol,
    BButton,
    vSelect,
  },
  props: {
    data_sekolah: {
      type: Array,
      required: true
    },
  },
  data() {
    return {
      importModalShow: false,
      loading_form: false,
      sekolah_id: '',
      file1:  null,
      feedback_file: '',
      feedback_sekolah_id: '',
      state_sekolah_id: null,
      state_file: null,
    }
  },
  created() {
    eventBus.$on('open-modal-import-ptk', this.handleEvent);
  },
  methods: {
    handleEvent(){
      this.importModalShow = true
    },
    hideModal(){
      this.importModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.sekolah_id = ''
      this.file1 = null
      this.feedback_file = ''
      this.feedback_sekolah_id = ''
      this.state_sekolah_id = null
      this.state_file = null
    },
    onFileChange(e) {
      this.file1 = e.target.files[0];
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      const data = new FormData();
      data.append('sekolah_id', (this.sekolah_id) ? this.sekolah_id : '');
      data.append('file', (this.file1) ? this.file1 : '');
      this.$http.post('/referensi/upload-ptk', data).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.feedback_file = (getData.errors.file) ? getData.errors.file.join(', ') : ''
          this.state_file = (getData.errors.file) ? false : null
          this.feedback_sekolah_id = (getData.errors.sekolah_id) ? getData.errors.sekolah_id.join(', ') : ''
          this.state_sekolah_id = (getData.errors.sekolah_id) ? false : null
        } else {
          this.hideModal()
          this.$emit('next', getData)
        }
      }).catch(error => {
        console.log(error);
      })
    },
  },
}
</script>