<template>
  <b-modal v-model="addModalShow" :title="title" @hidden="hideModal" ok-only ok-title="Batal" ok-variant="secondary">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-row>
        <b-col cols="12" class="mb-2">
          <b-button block variant="warning" :href="link_excel" target="_blank">UNDUH TEMPLATE</b-button>
        </b-col>
        <b-col cols="12">
          <b-form-file v-model="file" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..." @change="onFileChange" :state="fileState" />
          <p v-show="feedback_file" class="text-danger">{{feedback_file}}</p>
        </b-col>
      </b-row>
    </b-overlay>
  </b-modal>
</template>

<script>
import { BOverlay, BButton, BFormFile, BRow, BCol } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BOverlay, BFormFile, BButton, BRow, BCol,
  },
  data() {
    return {
      addModalShow: false,
      loading_form: false,
      title: '',
      link_excel: '',
      file: null,
      fileState: null,
      feedback_file: '',
      soal_ujian_id: '',
    }
  },
  created() {
    eventBus.$on('open-modal-import-soal', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.soal_ujian_id = data.id
      this.link_excel = `/unduhan/template-jawaban/${this.soal_ujian_id}`
      this.addModalShow = true
    },
    hideModal(){
      this.addModalShow = false
      this.link_excel = ''
      this.file = null
      this.fileState = null
      this.feedback_file = ''
      this.soal_ujian_id = ''
    },
    onFileChange(e) {
      this.loading = true
      this.simpan = false
      this.file = e.target.files[0];
      const data = new FormData();
      data.append('file_excel', (this.file) ? this.file : '');
      data.append('soal_ujian_id', (this.soal_ujian_id) ? this.soal_ujian_id : '');
      this.$http.post('/soal-ujian/upload-jawaban', data).then(response => {
        //this.$emit('loading', false)
        this.loading = false
        let data = response.data
        this.fileState = null
        this.feedback_file = ''
        if(data.errors){
          this.fileState = (data.errors.file_excel) ? false : null
          this.feedback_file = (data.errors.file_excel) ? data.errors.file_excel.join(', ') : ''
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
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
        this.fileState = false
        this.feedback_file = 'Isian salah. Silahkan periksa kembali!!!'
      })
    },
  },
}
</script>