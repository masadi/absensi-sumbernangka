<template>
  <b-modal v-model="addModalShow" title="Validasi Data PTK" @hidden="hideModal" @ok="handleOk" size="xl">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-table-simple>
          <b-thead>
            <b-tr>
              <b-th class="text-center">No</b-th>
              <b-th class="text-center">ID PTK</b-th>
              <b-th class="text-center">Nama</b-th>
              <b-th class="text-center">NUPTK</b-th>
              <b-th class="text-center">L/P</b-th>
              <b-th class="text-center">Tempat Lahir</b-th>
              <b-th class="text-center">Tanggal Lahir</b-th>
              <b-th class="text-center">Email</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <b-tr v-for="(ptk, index) in data_guru" :key="index">
              <b-td class="text-center">{{ index + 1 }}</b-td>
              <b-td>
                <b-form-group :label-for="`ptk_id-${index}`" :invalid-feedback="feedback.ptk_id[index]" :state="state.ptk_id[index]">
                  <b-form-input :id="`ptk_id-${index}`" v-model="form.ptk_id[index]" :state="state.ptk_id[index]" />
                </b-form-group>
              </b-td>
              <b-td>
                <b-form-group :label-for="`nama-${index}`" :invalid-feedback="feedback.nama[index]" :state="state.nama[index]">
                  <b-form-input :id="`nama-${index}`" v-model="form.nama[index]" :state="state.nama[index]" />
                </b-form-group>
              </b-td>
              <b-td>
                <b-form-group :label-for="`nuptk-${index}`" :invalid-feedback="feedback.nuptk[index]" :state="state.nuptk[index]">
                  <b-form-input :id="`nuptk-${index}`" v-model="form.nuptk[index]" :state="state.nuptk[index]" />
                </b-form-group>
              </b-td>
              <b-td>
                <b-form-group :label-for="`jenis_kelamin-${index}`" :invalid-feedback="feedback.jenis_kelamin[index]" :state="state.jenis_kelamin[index]">
                  <b-form-select :id="`jenis_kelamin-${index}`" v-model="form.jenis_kelamin[index]" :state="state.jenis_kelamin[index]" :options="options"></b-form-select>
                </b-form-group>
              </b-td>
              <b-td>
                <b-form-group :label-for="`tempat_lahir-${index}`" :invalid-feedback="feedback.tempat_lahir[index]" :state="state.tempat_lahir[index]">
                  <b-form-input :id="`tempat_lahir-${index}`" v-model="form.tempat_lahir[index]" :state="state.tempat_lahir[index]" />
                </b-form-group>
              </b-td>
              <b-td>
                <b-form-group :label-for="`tanggal_lahir-${index}`" :invalid-feedback="feedback.tanggal_lahir[index]" :state="state.tanggal_lahir[index]">
                  <b-form-datepicker :id="`tanggal_lahir-${index}`" v-model="form.tanggal_lahir[index]" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal_lahir" @context="onContext" placeholder="Pilih Tanggal Lahir" :state="state.tanggal_lahir[index]" />
                </b-form-group>
              </b-td>
              <b-td>
                <b-form-group :label-for="`email-${index}`" :invalid-feedback="feedback.email[index]" :state="state.email[index]">
                  <b-form-input :id="`email-${index}`" v-model="form.email[index]" :state="state.email[index]" />
                </b-form-group>
              </b-td>
            </b-tr>
          </b-tbody>
        </b-table-simple>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
          <b-button @click="cancel()">Tutup</b-button>
        </b-overlay>
        <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block">
          <b-button variant="success" @click="ok()">Simpan</b-button>
        </b-overlay>
      </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormGroup, BFormInput, BFormSelect, BFormDatepicker, BButton, BTableSimple, BThead, BTbody, BTr, BTd, BTh } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BOverlay,
    BForm,
    BFormGroup,
    BFormInput,
    BFormSelect,
    BFormDatepicker,
    BButton,
    vSelect,
    BTableSimple, 
    BThead, 
    BTbody, 
    BTr, 
    BTd, 
    BTh,
  },
  data() {
    return {
      addModalShow: false,
      loading_form: false,
      form: {
        sekolah_id: '',
        ptk_id: {},
        nama: {},
        nuptk: {},
        jenis_kelamin: {},
        tempat_lahir: {},
        tanggal_lahir: {},
        email: {},
      },
      feedback: {
        ptk_id: {},
        nama: {},
        nuptk: {},
        jenis_kelamin: {},
        tempat_lahir: {},
        tanggal_lahir: {},
        email: {},
      },
      state: {
        ptk_id: {},
        nama: {},
        nuptk: {},
        jenis_kelamin: {},
        tempat_lahir: {},
        tanggal_lahir: {},
        email: {},
      },
      form_data: {
        guru: [],
      },
      data_guru: [],
      options: [
        { value: 'L', text: 'Laki-laki' },
        { value: 'P', text: 'Perempuan' },
      ],
    }
  },
  created() {
    eventBus.$on('open-modal-add-ptk', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.form.sekolah_id = data.sekolah_id
      this.data_guru = data.guru
      this.form_data = data
      this.addModalShow = true
      var _this = this
      this.data_guru.forEach(function(el, index) {
        _this.form.ptk_id[index] = el.ptk_id
        _this.form.nama[index] = el.nama
        _this.form.nuptk[index] = el.nuptk
        _this.form.jenis_kelamin[index] = el.jenis_kelamin
        _this.form.tempat_lahir[index] = el.tempat_lahir
        _this.form.tanggal_lahir[index] = el.tanggal_lahir
        _this.form.email[index] = el.email
      })
    },
    hideModal(){
      this.addModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.form.ptk_id = {}
      this.form.nama = {}
      this.form.nuptk = {}
      this.form.jenis_kelamin = {}
      this.form.tempat_lahir = {}
      this.form.tanggal_lahir = {}
      this.form.email = {}
      this.feedback.ptk_id = {}
      this.feedback.nama = {}
      this.feedback.nuptk = {}
      this.feedback.jenis_kelamin = {}
      this.feedback.tempat_lahir = {}
      this.feedback.tanggal_lahir = {}
      this.feedback.email = {}
      this.state.ptk_id = {}
      this.state.nama = {}
      this.state.nuptk = {}
      this.state.jenis_kelamin = {}
      this.state.tempat_lahir = {}
      this.state.tanggal_lahir = {}
      this.state.email = {}
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
      this.$http.post('/referensi/add-ptk', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          var _this = this
          this.data_guru.forEach(function(el, index) {
            _this.feedback.ptk_id[index] = (getData.errors['ptk_id.'+index]) ? getData.errors['ptk_id.'+index].join(', ') : ''
            _this.feedback.nama[index] = (getData.errors['nama.'+index]) ? getData.errors['nama.'+index].join(', ') : ''
            _this.feedback.nuptk[index] = (getData.errors['nuptk.'+index]) ? getData.errors['nuptk.'+index].join(', ') : ''
            _this.feedback.jenis_kelamin[index] = (getData.errors['jenis_kelamin.'+index]) ? getData.errors['jenis_kelamin.'+index].join(', ') : ''
            _this.feedback.tempat_lahir[index] = (getData.errors['ematempat_lahiril.'+index]) ? getData.errors['tempat_lahir.'+index].join(', ') : ''
            _this.feedback.tanggal_lahir[index] = (getData.errors['tanggal_lahir.'+index]) ? getData.errors['tanggal_lahir.'+index].join(', ') : ''
            _this.feedback.email[index] = (getData.errors['email.'+index]) ? getData.errors['email.'+index].join(', ') : ''
            _this.state.ptk_id[index] = (getData.errors['ptk_id.'+index]) ? false : null
            _this.state.nama[index] = (getData.errors['nama.'+index]) ? false : null
            _this.state.nuptk[index] = (getData.errors['nuptk.'+index]) ? false : null
            _this.state.jenis_kelamin[index] = (getData.errors['jenis_kelamin.'+index]) ? false : null
            _this.state.tempat_lahir[index] = (getData.errors['tempat_lahir.'+index]) ? false : null
            _this.state.tanggal_lahir[index] = (getData.errors['tanggal_lahir.'+index]) ? false : null
            _this.state.email[index] = (getData.errors['email.'+index]) ? false : null
          });
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
      this.formatted = ctx.selectedYMD
    },
  },
}
</script>