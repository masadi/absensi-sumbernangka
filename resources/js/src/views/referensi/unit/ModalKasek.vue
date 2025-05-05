<template>
  <b-modal v-model="modalKasekShow" title="Edit Data Kepala Unit" size="xl" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Kepala Unit" label-for="ptk_id" label-cols-md="3" :invalid-feedback="feedback.ptk_id" :state="state.ptk_id">
              <v-select id="ptk_id" v-model="form.ptk_id" :reduce="nama => nama.ptk_id" label="nama" :options="data_ptk" placeholder="== Pilih Kepala Unit ==" :state="state.ptk_id">
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
import { BOverlay, BForm, BRow, BCol, BFormGroup, BButton } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BOverlay,
    BForm,
    BRow,
    BCol,
    BFormGroup,
    BButton,
    vSelect,
  },
  data() {
    return {
      modalKasekShow: false,
      loading_form: false,
      form: {},
      feedback: {},
      state: {},
      data_ptk: [],
    }
  },
  created() {
    eventBus.$on('open-modal-kasek-unit', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.form = data
      console.log(this.form);
      this.$http.post('/referensi/get-ptk', {
        sekolah_id: data.sekolah_id,
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
          this.data_ptk = getData.ptk
          this.modalKasekShow = true
        }
      })
    },
    hideModal(){
      this.modalKasekShow = false
      this.resetForm()
    },
    resetForm(){
      this.form = {}
      this.feedback = {}
      this.state = {}
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/referensi/update-kasek', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.ptk_id = (getData.errors.ptk_id) ? false : null
          this.feedback.ptk_id = (getData.errors.ptk_id) ? getData.errors.ptk_id.join(', ') : ''
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