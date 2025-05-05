<template>
  <b-modal v-model="editModalShow" title="Edit Data Unit" size="xl" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Unit" label-for="sekolah_id" label-cols-md="3" :invalid-feedback="feedback.sekolah_id" :state="state.sekolah_id">
              <v-select id="tingkat" v-model="form.sekolah_id" :reduce="nama => nama.sekolah_id" label="nama" :options="data_sekolah" placeholder="== Pilih Unit ==" :searchable="false" :state="state.sekolah_id">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nama" label-for="nama" label-cols-md="3" :invalid-feedback="feedback.nama" :state="state.nama">
              <b-form-input id="nama" v-model="form.nama" :state="state.nama" placeholder="Nama"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="NUPTK" label-for="nuptk" label-cols-md="3" :invalid-feedback="feedback.nuptk" :state="state.nuptk">
              <b-form-input id="nuptk" v-model="form.nuptk" :state="state.nuptk" placeholder="NUPTK"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Jenis Kelamin" label-for="jenis_kelamin" label-cols-md="3" :invalid-feedback="feedback.jenis_kelamin" :state="state.jenis_kelamin">
              <v-select id="tingkat" v-model="form.jenis_kelamin" :reduce="nama => nama.id" label="nama" :options="data_kelamin" placeholder="== Pilih Jenis Kelamin ==" :searchable="false" :state="state.jenis_kelamin">
                <template #no-options>
                  Tidak ada data untuk ditampilkan
                </template>
              </v-select>
            </b-form-group> 
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tempat Lahir" label-for="tempat_lahir" label-cols-md="3" :invalid-feedback="feedback.tempat_lahir" :state="state.tempat_lahir">
              <b-form-input id="tempat_lahir" v-model="form.tempat_lahir" :state="state.tempat_lahir" placeholder="NUPTK"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Tanggal Lahir" label-for="tanggal_lahir" label-cols-md="3" :invalid-feedback="feedback.tanggal_lahir" :state="state.tanggal_lahir">
              <b-form-datepicker v-model="form.tanggal_lahir" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal_lahir" @context="onContext" placeholder="Pilih Tanggal Lahir" />
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Email" label-for="email" label-cols-md="3" :invalid-feedback="feedback.email" :state="state.email">
              <b-form-input id="email" v-model="form.email" :state="state.email" placeholder="Email"></b-form-input>
            </b-form-group>  
          </b-col>
          <b-col cols="12">
            <b-form-group label="Foto" label-for="image" label-cols-md="3" :invalid-feedback="feedback.image" :state="state.image">
              <b-form-file id="image" accept=".jpg, .png" v-model="form.image" :state="state.image" placeholder="Upload Foto..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
            </b-form-group>
          </b-col>
          <b-col cols="7" offset="3" v-if="preview_url">
            <b-img rounded v-bind="mainProps" :src="preview_url" alt="Foto"></b-img>
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
import { BOverlay, BForm, BFormInput, BFormTextarea, BRow, BCol, BFormGroup, BFormFile, BButton, BImg, BFormDatepicker } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
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
    BFormDatepicker,
    vSelect,
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
      data_sekolah: [],
      data_kelamin: [
        {
          id: 'L',
          nama: 'Laki-laki',
        },
        {
          id: 'P',
          nama: 'Perempuan',
        }
      ],
    }
  },
  created() {
    eventBus.$on('open-modal-edit-ptk', this.handleEvent);
  },
  methods: {
    handleEvent(item){
      let data = item.item
      this.data_sekolah = item.data_sekolah
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
      data.append('semester_id', this.user.semester.semester_id)
      data.append('ptk_id', this.form.ptk_id);
      data.append('nama', this.form.nama);
      data.append('nuptk', this.form.nuptk);
      data.append('jenis_kelamin', this.form.jenis_kelamin);
      data.append('tempat_lahir', this.form.tempat_lahir);
      data.append('tanggal_lahir', this.form.tanggal_lahir);
      data.append('email', this.form.email);
      data.append('photo', (this.form.image) ? this.form.image : '');
      this.$http.post('/referensi/update-ptk', data).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.sekolah_id = (getData.errors.sekolah_id) ? false : null
          this.state.nama = (getData.errors.nama) ? false : null
          this.state.nuptk = (getData.errors.nuptk) ? false : null
          this.state.jenis_kelamin = (getData.errors.jenis_kelamin) ? false : null
          this.state.tempat_lahir = (getData.errors.tempat_lahir) ? false : null
          this.state.tanggal_lahir = (getData.errors.tanggal_lahir) ? false : null
          this.state.email = (getData.errors.email) ? false : null
          this.state.image = (getData.errors.photo) ? false : null
          this.feedback.sekolah_id = (getData.errors.sekolah_id) ? getData.errors.sekolah_id.join(', ') : ''
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.feedback.nuptk = (getData.errors.nuptk) ? getData.errors.nuptk.join(', ') : ''
          this.feedback.jenis_kelamin = (getData.errors.jenis_kelamin) ? getData.errors.jenis_kelamin.join(', ') : ''
          this.feedback.tempat_lahir = (getData.errors.tempat_lahir) ? getData.errors.tempat_lahir.join(', ') : ''
          this.feedback.tanggal_lahir = (getData.errors.tanggal_lahir) ? getData.errors.tanggal_lahir.join(', ') : ''
          this.feedback.email = (getData.errors.email) ? getData.errors.email.join(', ') : ''
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
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>