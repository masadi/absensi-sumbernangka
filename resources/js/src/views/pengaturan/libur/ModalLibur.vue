<template>
  <b-modal v-model="modalShow" @hidden="resetModal" @ok="handleOk" :title="title">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form @submit.prevent="handleSubmit">
        <b-form-group label="Nama" label-for="nama" :invalid-feedback="feedback.nama" :state="state.nama">
          <b-form-input id="nama" v-model="form.nama" :state="state.nama"></b-form-input>
        </b-form-group>
        <b-form-group label="Kategori" label-for="kategori_id" :invalid-feedback="feedback.kategori_id" :state="state.kategori_id">
          <b-form-select v-model="form.kategori_id" :options="kategori_libur" :state="state.kategori_id" text-field="nama" value-field="id"></b-form-select>
        </b-form-group>
        <b-form-group label="Tanggal Mulai" label-for="mulai_tanggal" :invalid-feedback="feedback.mulai_tanggal" :state="state.mulai_tanggal">
          <b-form-datepicker v-model="form.mulai_tanggal" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="mulai_tanggal" @context="onContext" placeholder="Pilih Tanggal Mulai" :state="state.mulai_tanggal" />
        </b-form-group>
        <b-form-group label="Tanggal Selesai" label-for="sampai_tanggal" :invalid-feedback="feedback.sampai_tanggal" :state="state.sampai_tanggal">
          <b-form-datepicker v-model="form.sampai_tanggal" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="sampai_tanggal" @context="onContext" placeholder="Pilih Tanggal Selesai" :state="state.sampai_tanggal" />
        </b-form-group>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
      <b-overlay :show="loading_modal" rounded opacity="0.6" spinner-small spinner-variant="secondary">
        <b-button @click="cancel()" size="sm">Tutup</b-button>
      </b-overlay>
      <b-overlay :show="loading_modal" rounded opacity="0.6" spinner-small spinner-variant="primary">
        <b-button variant="primary" @click="ok()" size="sm">{{ok_title}}</b-button>
      </b-overlay>
      <b-overlay :show="loading_modal" rounded opacity="0.6" spinner-small spinner-variant="secondary">
        <b-button variant="danger" @click="hapus()" size="sm">Hapus</b-button>
      </b-overlay>
    </template>  
  </b-modal>
</template>

<script>
import { BForm, BFormGroup, BFormInput, BFormSelect, BOverlay, BFormDatepicker, BButton } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BForm, BFormGroup, BFormInput, BFormSelect, BOverlay, BFormDatepicker, BButton
  },
  props: {
    kategori_libur: {
      type: Array,
      required: true
    },
  },
  data() {
    return {
      ok_title: '',
      edit: false,
      modalShow: false,
      loading_modal: false,
      form: {
        aksi: 'libur',
        id: '',
        nama: '',
        kategori_id: '',
        mulai_tanggal: '',
        sampai_tanggal: '',
      },
      feedback: {
        nama: '',
        kategori_id: '',
        mulai_tanggal: '',
        sampai_tanggal: '',
      },
      state: {
        nama: null,
        kategori_id: null,
        mulai_tanggal: null,
        sampai_tanggal: null,
      },
      title: '',
    }
  },
  created() {
    eventBus.$on('open-modal-libur', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      console.log(data);
      if(data.aksi == 'add'){
        this.title = 'Tambah Data Libur'
        this.ok_title = 'Simpan'
        this.edit = false
        if(data.data){
          this.form.mulai_tanggal = data.data.dateStr
          this.form.sampai_tanggal = data.data.dateStr
        }
      }
      if(data.aksi == 'edit'){
        this.title = 'Edit Data Libur'
        this.ok_title = 'Perbaharui'
        this.edit = true
        this.form.id = data.data.publicId
        this.form.nama = data.data.title
        this.form.kategori_id = data.data.extendedProps.kategori_id
        this.form.mulai_tanggal = data.data.extendedProps.mulai_tanggal
        this.form.sampai_tanggal = data.data.extendedProps.sampai_tanggal
      }
      this.modalShow = true
    },
    resetModal(){
      this.modalShow = false
      this.form.nama = ''
      this.form.kategori_id = ''
      this.form.mulai_tanggal = ''
      this.form.sampai_tanggal = ''
      this.feedback.nama = ''
      this.feedback.kategori_id = ''
      this.feedback.mulai_tanggal = ''
      this.feedback.sampai_tanggal = ''
      this.state.nama = null
      this.state.kategori_id = null
      this.state.mulai_tanggal = null
      this.state.sampai_tanggal = null
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
          this.state.nama = (getData.errors.nama) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          this.state.kategori_id = (getData.errors.kategori_id) ? false : null
          this.feedback.kategori_id = (getData.errors.kategori_id) ? getData.errors.kategori_id.join(', ') : ''
          this.state.mulai_tanggal = (getData.errors.mulai_tanggal) ? false : null
          this.feedback.mulai_tanggal = (getData.errors.mulai_tanggal) ? getData.errors.mulai_tanggal.join(', ') : ''
          this.state.sampai_tanggal = (getData.errors.sampai_tanggal) ? false : null
          this.feedback.sampai_tanggal = (getData.errors.sampai_tanggal) ? getData.errors.sampai_tanggal.join(', ') : ''
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
    hapus(){
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: 'Tindakan ini tidak dapat dikembalikan!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yakin!',
        customClass: {
          confirmButton: 'btn btn-primary',
          cancelButton: 'btn btn-outline-danger ml-1',
        },
        buttonsStyling: false,
        allowOutsideClick: () => false,
      }).then(result => {
        if (result.value) {
          this.loading_modal = true
          this.$http.post('/libur/hapus', {
            aksi: 'libur',
            id: this.form.id,
          }).then(response => {
            let getData = response.data
            this.loading_modal = false
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
          });
        }
      })
    },
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
  }
}
</script>