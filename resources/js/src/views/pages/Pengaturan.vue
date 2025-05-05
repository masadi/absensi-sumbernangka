<template>
  <b-card no-body>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <b-overlay :show="loading" rounded opacity="0.6" size="lg" spinner-variant="danger">
          <b-form @submit.prevent="handleSubmit">
            <b-row>
              <b-col cols="12">
                <b-form-group label-cols="4" label-cols-lg="2" label-size="sm" label="Semester Aktif" label-for="semester_id" :invalid-feedback="feedback.semester_id" :state="state.semester_id">
                  <v-select id="semester_id" v-model="form.semester_id" :options="semester" :reduce="nama => nama.semester_id" label="nama" placeholder="== Pilih Periode Aktif ==" :clearable="false">
                    <template #no-options>
                      Tidak ada data untuk ditampilkan
                    </template>
                  </v-select>
                </b-form-group>
                <b-form-group label-cols="4" label-cols-lg="2" label-size="sm" label="Tanggal Mulai" label-for="tanggal_mulai">
                  <b-form-datepicker v-model="form.tanggal_mulai" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal_mulai" @context="onContext" placeholder="Pilih Tanggal Mulai" />
                </b-form-group>
                <b-form-group label-cols="4" label-cols-lg="2" label-size="sm" label="Tanggal Selesai" label-for="tanggal_selesai" :invalid-feedback="feedback.tanggal_selesai" :state="state.tanggal_selesai">
                  <b-form-datepicker v-model="form.tanggal_selesai" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal_selesai" @context="onContext" placeholder="Pilih Tanggal Selesai" />
                </b-form-group>
              </b-col>
            </b-row>
            <b-row>
              <b-col cols="12">
                <b-button type="submit" variant="primary">Simpan</b-button>
              </b-col>
            </b-row>
          </b-form>
        </b-overlay>
      </div>
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner, BOverlay, BForm, BRow, BCol, BFormGroup, BInputGroup, BFormInput, BFormDatepicker, BButton } from 'bootstrap-vue'
import vSelect from 'vue-select'
export default {
  components: {
    BCard, BCardBody, BSpinner, BOverlay, BForm, BRow, BCol, BFormGroup, BInputGroup, BFormInput, BFormDatepicker, BButton, vSelect
  },
  data() {
    return {
      loading: false,
      isBusy: true,
      semester: [],
      form: {
        semester_id: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
      },
      feedback: {
        semester_id: '',
        tanggal_mulai: '',
        tanggal_selesai: '',
      },
      state: {
        semester_id: null,
        tanggal_mulai: null,
        tanggal_selesai: null,
      },
    }
  },
  created() {
    this.loadPostData()
  },
  methods: {
    loadPostData(){
      this.isBusy = true
      this.$http.get('/pengaturan/umum').then(response => {
        this.isBusy = false
        let getData = response.data
        this.form.semester_id = getData.semester_id
        this.form.tanggal_mulai = getData.tanggal_mulai
        this.form.tanggal_selesai = getData.tanggal_selesai
        this.semester = getData.semester
      })
    },
    handleSubmit(){
      this.loading = true
      this.$http.post('/pengaturan/umum', this.form).then(response => {
        this.loading = false
        let getData = response.data
        this.state.app_name = null
        this.state.app_version = null
        this.state.semester_id = null
        this.state.tanggal_mulai = null
        this.state.tanggal_selesai = null
        if(getData.errors){
          this.state.app_name = (getData.errors.app_name) ? false : null
          this.state.app_version = (getData.errors.app_version) ? false : null
          this.state.semester_id = (getData.errors.semester_id) ? false : null
          this.state.tanggal_mulai = (getData.errors.tanggal_mulai) ? false : null
          this.state.tanggal_selesai = (getData.errors.tanggal_selesai) ? false : null
          this.feedback.app_name = (getData.errors.app_name) ? getData.errors.app_name.join(', ') : ''
          this.feedback.app_version = (getData.errors.app_version) ? getData.errors.app_version.join(', ') : ''
          this.feedback.semester_id = (getData.errors.semester_id) ? getData.errors.semester_id.join(', ') : ''
          this.feedback.tanggal_mulai = (getData.errors.tanggal_mulai) ? getData.errors.tanggal_mulai.join(', ') : ''
          this.feedback.tanggal_selesai = (getData.errors.tanggal_selesai) ? getData.errors.tanggal_selesai.join(', ') : ''
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
            this.loadPostData()
          })
        }
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