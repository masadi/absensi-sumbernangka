<template>
  <b-modal v-model="addModalCatatan" :title="title" size="lg" @show="resetModal" @hidden="hideModal" @ok="handleOk">
    <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form ref="form" @submit.stop.prevent="handleSubmit">
        <b-form-group label="Tanggal" label-for="tanggal" label-cols-md="3" :invalid-feedback="feedback.tanggal" :state="state.tanggal">
          <b-form-datepicker v-model="form.tanggal" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal" @context="onContext" placeholder="Pilih Tanggal" />
        </b-form-group>
        <b-form-group label="Ketua Pelaksana" label-for="ptk_id" label-cols-md="3" :invalid-feedback="feedback.ptk_id" :state="state.ptk_id">
          <v-select id="ptk_id" v-model="form.ptk_id" :reduce="nama => nama.ptk_id" label="nama" :options="data_ptk" placeholder="== Pilih PTK ==" :state="state.ptk_id" @input="changePtk">
            <template #no-options>
              Tidak ada data untuk ditampilkan
            </template>
          </v-select>
        </b-form-group>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
      <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
        <b-button @click="cancel()" class="float-right">Tutup</b-button>
      </b-overlay>
      <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block">
        <b-button variant="success" @click="ok()" class="float-right" v-if="lengkap">Cetak Jadwal</b-button>
      </b-overlay>    
    </template>
  </b-modal>
</template>

<script>
import { BOverlay, BForm, BFormGroup, BFormTextarea, BFormInput, BButton, BRow, BCol, BFormDatepicker } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import vSelect from 'vue-select'
export default {
  components: {
    BOverlay, BForm, BFormGroup, BFormTextarea, BFormInput, BButton, BRow, BCol, BFormDatepicker, vSelect
  },
  data() {
    return {
      addModalCatatan: false,
      loading_form: false,
      title: '',
      form: {
        tanggal: '',
        ptk_id: '',
        sekolah_id: '',
        jenis: '',
        rombongan_belajar_id: '',
      },
      feedback: {
        tanggal: '',
        ptk_id: '',
      },
      state: {
        tanggal: null,
        ptk_id: null,
      },
      data_ptk: [],
      lengkap: false,
    }
  },
  created() {
    eventBus.$on('open-modal-ttd', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      console.log(data);
      this.title = 'Titimangsa'
      this.form.jenis = data.form.aksi
      this.form.rombongan_belajar_id = data.rombel.rombongan_belajar_id
      this.form.sekolah_id = data.rombel.sekolah_id
      this.$http.post('/referensi/get-ptk', {
        sekolah_id: this.form.sekolah_id,
      }).then(response => {
        let getData = response.data
        this.addModalCatatan = true
        this.data_ptk = getData.ptk
      })
    },
    hideModal(){
      this.addModalCatatan = false
      this.data = ''
    },
    resetModal(){
      console.log('resetModal');
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      console.log(`/cetak/jadwal/${this.form.jenis}/${this.form.rombongan_belajar_id}/${this.form.tanggal}/${this.form.ptk_id}`);
      //https://absensi.smpalfalahsg.sch.id/cetak/jadwal/pts/e5b60a24-21a5-4ce1-abf7-9e86cca0fdaf/2024-01-02/fe18e865-0a26-4a69-bf56-d0d0bc7e605f
      window.open(`/cetak/jadwal/${this.form.jenis}/${this.form.rombongan_belajar_id}/${this.form.tanggal}/${this.form.ptk_id}`, '_blank')
      //Route::get('/jadwal/{jenis}/{rombongan_belajar_id}/{tanggal}/{ptk_id}', [CetakController::class, 'jadwal'])->name('jadwal');
    },
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
    changePtk(val){
      this.lengkap = false
      if(val && this.form.tanggal){
        this.lengkap = true
      }
    }
  },
}
</script>