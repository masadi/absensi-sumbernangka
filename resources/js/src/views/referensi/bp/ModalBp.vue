<template>
  <b-modal v-model="modalBp" title="Cetak Rekap Presensi" size="lg" @hidden="hideModal" @ok="handleOk" no-close-on-esc no-close-on-backdrop>
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group label="Unit" label-for="sekolah_id" label-cols-md="3" :invalid-feedback="feedback.sekolah_id" :state="state.sekolah_id">
          <v-select id="ptk_id" v-model="form.sekolah_id" :reduce="nama => nama.sekolah_id" label="nama" :options="data_sekolah" placeholder="== Pilih Unit ==" :state="state.sekolah_id" @input="changeUnit">
            <template #no-options>
              Tidak ada data untuk ditampilkan
            </template>
          </v-select>
        </b-form-group>
        <b-form-group label="Nama Pengguna" label-for="file" label-cols-md="3" :invalid-feedback="feedback.ptk_id" :state="state.ptk_id">
          <b-overlay :show="loading_ptk" rounded opacity="0.6" size="lg" spinner-variant="secondary">
            <v-select id="ptk_id" v-model="form.ptk_id" :reduce="nama => nama.ptk_id" label="nama" :options="data_ptk" placeholder="== Pilih Nama Pengguna ==" :state="state.ptk_id" @input="changePtk">
              <template #no-options>
                Tidak ada data untuk ditampilkan
              </template>
            </v-select>
          </b-overlay>
        </b-form-group>
        <b-form-group label="Email" label-for="email" label-cols-md="3" :invalid-feedback="feedback.email" :state="state.email">
          <b-form-input v-model="form.email" placeholder="Email Pengguna" :state="state.email"></b-form-input>
        </b-form-group>
        <b-form-group label="Password" label-for="password" label-cols-md="3" :invalid-feedback="feedback.password" :state="state.password">
          <b-form-input v-model="form.password" placeholder="Password Pengguna" :state="state.password"></b-form-input>
        </b-form-group>
      </b-form>
      <template #modal-footer="{ ok, cancel }">
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
          <b-button @click="cancel()">Batal</b-button>
        </b-overlay>
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
          <b-button variant="success" @click="ok()">Cetak</b-button>
        </b-overlay>
      </template>
    </b-overlay>
  </b-modal>
</template>

<script>
import { BForm, BOverlay, BFormGroup, BButton, BFormInput } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BForm,
    BOverlay,
    BFormGroup,
    BButton,
    BFormInput,
    vSelect,
  },
  data() {
    return {
      loading_form: false,
      loading_ptk: false,
      modalBp: false,
      form: {
        aksi: 'baru',
        sekolah_id: '',
        ptk_id: '',
        email: '',
        password: '12345678',
        periode_aktif: null,
      },
      feedback: {
        sekolah_id: '',
        ptk_id: '',
        email: '',
        password: '',
      },
      state: {
        sekolah_id: null,
        ptk_id: null,
        email: null,
        password: null,
      },
      data_sekolah: [],
      data_ptk: [],
    }
  },
  created() {
    eventBus.$on('open-modal-add-bp', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.data_sekolah = data
      this.form.periode_aktif = this.user.semester.nama
      this.modalBp = true
    },
    hideModal(){
      this.modalBp = false
      this.resetForm()
    },
    resetForm(){
      this.data_sekolah = []
      this.data_ptk = []
      this.form.sekolah_id = ''
      this.form.ptk_id = ''
      this.form.email = ''
      this.form.password = '12345678'
      this.form.periode_aktif = ''
      this.feedback.sekolah_id = ''
      this.feedback.ptk_id = ''
      this.feedback.email = ''
      this.feedback.password = ''
      this.state.sekolah_id = null
      this.state.ptk_id = null
      this.state.email = null
      this.state.password = null
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      this.loading_form = true
      this.$http.post('/referensi/post-bp', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.sekolah_id = (getData.errors.sekolah_id) ? false : null
          this.state.ptk_id = (getData.errors.ptk_id) ? false : null
          this.state.email = (getData.errors.email) ? false : null
          this.state.password = (getData.errors.password) ? false : null
          this.feedback.sekolah_id = (getData.errors.sekolah_id) ? getData.errors.sekolah_id.join(', ') : ''
          this.feedback.ptk_id = (getData.errors.ptk_id) ? getData.errors.ptk_id.join(', ') : ''
          this.feedback.email = (getData.errors.email) ? getData.errors.email.join(', ') : ''
          this.feedback.password = (getData.errors.password) ? getData.errors.password.join(', ') : ''
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
    changeUnit(val){
      this.form.ptk_id = ''
      this.form.email = ''
      if(val){
        this.loading_ptk = true
        this.$http.post('/referensi/get-ptk', this.form).then(response => {
          this.data_ptk = response.data.ptk
          this.loading_ptk = false
        });
      }
    },
    changePtk(val){
      this.form.email = ''
      if(val){
        var email = this.data_ptk.filter(function(item, index){
          return item.ptk_id == val
        })
        if(email.length){
          this.form.email = email[0].email
        }
      }
    }
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>