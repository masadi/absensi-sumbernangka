<template>
  <b-modal v-model="addModalCatatan" :title="title" size="lg" @show="resetModal" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group label-for="catatan" :invalid-feedback="feedback.catatan" :state="state.catatan">
          <b-form-textarea id="catatan" v-model="form.catatan" placeholder="Isi catatan..." rows="3" max-rows="6" :state="state.catatan"></b-form-textarea>
        </b-form-group>
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
import { BOverlay, BForm, BFormGroup, BFormTextarea, BFormInput, BButton, BRow, BCol } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BOverlay, BForm, BFormGroup, BFormTextarea, BFormInput, BButton, BRow, BCol,
  },
  data() {
    return {
      addModalCatatan: false,
      loading_form: false,
      title: '',
      form: {
        aksi: 'catatan',
        id: '',
        jadwal_id: '',
        catatan: ''
      },
      feedback: {
        catatan: ''
      },
      state: {
        catatan: null
      },
    }
  },
  created() {
    eventBus.$on('open-modal-catatan', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      console.log(data);
      if(data.id){
        this.form.id = data.id
        this.form.catatan = data.catatan
        this.title = `Edit Catatan`
      } else {
        this.form.id = ''
        this.form.catatan = ''
        this.title = `Tambah Catatan`
      }
      this.form.jadwal_id = data.jadwal_id
      this.addModalCatatan = true
    },
    hideModal(){
      this.addModalCatatan = false
      this.data = []
    },
    resetModal(){
      console.log('resetModal');
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
          this.feedback.catatan = (getData.errors.catatan) ? getData.errors.catatan.join(', ') : ''
          this.state.catatan = (getData.errors.catatan) ? false : null
        } else {
          this.hideModal()
          this.$emit('reload')
        }
      }).catch(error => {
        console.log(error);
      })
    },
  },
}
</script>