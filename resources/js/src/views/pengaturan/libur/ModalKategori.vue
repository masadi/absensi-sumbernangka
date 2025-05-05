<template>
  <b-modal v-model="modalShow" @hidden="resetModal" @ok="handleOk" :title="title">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form @submit.prevent="handleSubmit">
        <b-form-group label="Unit" label-for="sekolah_id" :invalid-feedback="feedback.sekolah_id" :state="state.sekolah_id">
          <b-form-select v-model="form.sekolah_id" :options="data_sekolah" :state="state.sekolah_id" text-field="nama" value-field="id" placeholder="Pilih Unit">
            <template #first>
              <b-form-select-option :value="null" disabled>-- Pilih Unit --</b-form-select-option>
            </template>
          </b-form-select>
        </b-form-group>
        <b-form-group label="Nama Kategori" label-for="nama" :invalid-feedback="feedback.nama" :state="state.nama">
          <b-form-input id="nama" v-model="form.nama" :state="state.nama"></b-form-input>
        </b-form-group>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
      <b-overlay :show="loading_modal" rounded opacity="0.6" size="sm" spinner-variant="secondary">
        <b-button @click="cancel()">Tutup</b-button>
      </b-overlay>
      <b-overlay :show="loading_modal" rounded opacity="0.6" size="sm" spinner-variant="primary">
        <b-button variant="primary" @click="ok()">Simpan</b-button>
      </b-overlay>
    </template>  
  </b-modal>
</template>

<script>
import { BForm, BFormGroup, BFormInput, BFormSelect, BOverlay, BButton, BFormSelectOption } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BForm, BFormGroup, BFormInput, BFormSelect, BOverlay, BButton, BFormSelectOption
  },
  props: {
    data_sekolah: {
      type: Array,
      required: true
    },
  },
  data() {
    return {
      modalShow: false,
      loading_modal: false,
      form: {
        aksi: 'kategori',
        id: '',
        sekolah_id: null,
        nama: '',
      },
      feedback: {
        sekolah_id: '',
        nama: '',
      },
      state: {
        sekolah_id: null,
        nama: null,
      },
      title: '',
    }
  },
  created() {
    eventBus.$on('open-modal-kategori', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      console.log(data);
      if(data.aksi == 'add'){
        this.title = 'Tambah Kategori Libur'
        if(data.data){
          //this.form.mulai_tanggal = data.data.dateStr
          //this.form.sampai_tanggal = data.data.dateStr
        }
      }
      if(data.aksi == 'edit'){
        this.title = 'Edit Kategori Libur'
        //console.log(data.data.extendedProps);
        this.form.id = data.data.id
        this.form.nama = data.data.nama
        this.form.sekolah_id = data.data.sekolah_id
        //this.form.mulai_tanggal = data.data.extendedProps.mulai_tanggal
        //this.form.sampai_tanggal = data.data.extendedProps.sampai_tanggal
      }
      this.modalShow = true
    },
    resetModal(){
      this.modalShow = false
      this.form.sekolah_id = ''
      this.form.nama = ''
      this.feedback.sekolah_id = ''
      this.feedback.nama = ''
      this.state.sekolah_id = null
      this.state.nama = null
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.loading_modal = true
      this.$http.post('/libur/simpan', this.form).then(response => {
        this.loading_modal = false
        let getData = response.data
        if(getData.errors){
          this.state.sekolah_id = (getData.errors.sekolah_id) ? false : null
          this.feedback.sekolah_id = (getData.errors.sekolah_id) ? getData.errors.sekolah_id.join(', ') : ''
          this.state.nama = (getData.errors.nama) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
            buttonsStyling: false,
          }).then(result => {
            this.$emit('reload')
            this.resetModal()
          })
        }
      });
    },
  }
}
</script>