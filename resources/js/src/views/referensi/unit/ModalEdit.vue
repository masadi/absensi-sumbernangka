<template>
  <b-modal v-model="editModalShow" title="Edit Data Unit" size="xl" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Nama Unit" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama Unit"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Alamat Lengkap" label-for="alamat" label-cols-md="3" :invalid-feedback="feedback.alamat" :state="state.alamat">
              <b-form-textarea id="alamat" v-model="form.alamat" placeholder="Alamat Lengkap"></b-form-textarea>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Logo Unit" label-for="image" label-cols-md="3" :invalid-feedback="feedback.image" :state="state.image">
              <b-form-file id="image" accept=".jpg, .png" v-model="form.image" :state="state.image" placeholder="Upload Logo Unit..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
            </b-form-group>
          </b-col>
          <b-col cols="7" offset="3" v-if="preview_url">
            <b-img rounded v-bind="mainProps" :src="preview_url" alt="Logo Unit"></b-img>
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
import { BOverlay, BForm, BFormInput, BFormTextarea, BRow, BCol, BFormGroup, BFormFile, BButton, BImg } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BOverlay,
    BForm,
    BFormInput,
    BFormTextarea,
    BRow,
    BCol,
    BFormGroup,
    BFormFile,
    BButton,
    BImg,
  },
  data() {
    return {
      editModalShow: false,
      loading_form: false,
      form: {},
      feedback: {},
      state: {},
      mainProps: {width: 125, height: 125 },
      preview_url: null,
    }
  },
  created() {
    eventBus.$on('open-modal-edit-unit', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.form = data
      this.preview_url = (data.logo_sekolah) ? `/storage/images/${data.logo_sekolah}` : null
      this.editModalShow = true
    },
    hideModal(){
      this.editModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form = {}
      this.feedback = {}
      this.state = {}
      this.preview_url = null
    },
    onFileChange(e) {
      this.form.image = e.target.files[0];
      this.preview_url = URL.createObjectURL(this.form.image)
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      const data = new FormData();
      data.append('sekolah_id', this.form.sekolah_id)
      data.append('nama', this.form.nama);
      data.append('alamat', this.form.alamat);
      data.append('photo', this.form.image);
      this.$http.post('/referensi/update-unit', data).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.state.alamat = (getData.errors.alamat) ? false : null
          this.state.image = (getData.errors.photo) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.feedback.alamat = (getData.errors.alamat) ? getData.errors.alamat.join(', ') : ''
          this.feedback.image = (getData.errors.photo) ? getData.errors.photo.join(', ') : ''          
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
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>