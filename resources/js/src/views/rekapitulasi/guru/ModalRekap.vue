<template>
  <b-modal v-model="modalRekapShow" title="Cetak Rekap Presensi" size="lg" @hidden="hideModal" @ok="handleOk" no-close-on-esc no-close-on-backdrop>
    <b-form ref="form" @submit.stop.prevent="handleSubmit">
      <b-form-group label="Format Rekap" label-for="format" label-cols-md="3">
        <b-form-select v-model="format" id="format">
          <b-form-select-option :value="null">== Pilih Format Rekap ==</b-form-select-option>
          <b-form-select-option value="bulanan">Laporan Bulanan TU</b-form-select-option>
          <b-form-select-option value="harian">Laporan Harian Individu</b-form-select-option>
        </b-form-select>
      </b-form-group>
      <b-form-group label="Output File" label-for="file" label-cols-md="3">
        <b-form-select v-model="file">
          <b-form-select-option :value="null">== Pilih Output File ==</b-form-select-option>
          <b-form-select-option value="pdf">PDF</b-form-select-option>
          <b-form-select-option value="excel">EXCEL</b-form-select-option>
        </b-form-select>
      </b-form-group>
      <b-form-group label="Filter Tanggal" label-for="mulai_tanggal" label-cols-md="3">
        <b-input-group>
          <b-form-datepicker v-model="mulai_tanggal" :min="mulai_min" :max="mulai_max" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="mulai_tanggal" @context="changeMulai" placeholder="Pilih Mulai Tanggal" />
          <b-input-group-prepend is-text><b>S/D</b></b-input-group-prepend>
          <b-form-datepicker v-model="sampai_tanggal" :min="sampai_min" :max="sampai_max" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="sampai_tanggal" @context="changeSampai" placeholder="Pilih Sampai Tanggal" />
        </b-input-group>
      </b-form-group>
    </b-form>
    <template #modal-footer="{ ok, cancel }">
      <b-button @click="cancel()">Batal</b-button>
      <b-button variant="success" @click="ok()" v-if="show">Cetak</b-button>
    </template>
  </b-modal>
</template>

<script>
import { BForm, BRow, BCol, BFormGroup, BButton, BFormSelect, BFormSelectOption, BInputGroup, BInputGroupText, BFormDatepicker, BInputGroupPrepend } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BForm,
    BRow,
    BCol,
    BFormGroup,
    BButton,
    BFormSelect,
    BFormSelectOption,
    BInputGroup,
    BInputGroupText,
    BFormDatepicker,
    BInputGroupPrepend,
  },
  props: {
    aksi: {
      type: String,
      required: true
    },
  },
  data() {
    return {
      show: false,
      modalRekapShow: false,
      ptk_id: null,
      format: 'bulanan',
      file: 'pdf',
      mulai_min: '',
      mulai_tanggal: '',
      mulai_max: '',
      sampai_tanggal: '',
      sampai_min: '',
      sampai_max: '',
    }
  },
  created() {
    eventBus.$on('open-modal-cetak-rekap-guru', this.handleEvent);
  },
  updated: function () {
    this.$nextTick(function () {
      if(this.format && this.file && this.mulai_tanggal && this.sampai_tanggal){
        this.show = true
      } else {
        this.show = false
      }
    })
  },
  methods: {
    handleEvent(data){
      if(data){
        if(data.ptk_id){
          this.ptk_id = data.ptk_id
          //this.mulai_tanggal = data.tanggal_mulai
          //this.mulai_min = data.tanggal_mulai
          //this.mulai_max = data.tanggal_selesai
          //this.sampai_tanggal = data.tanggal_selesai
          this.modalRekapShow = true
        } else {
          this.$swal({
            icon: 'error',
            text: 'Silahkan pilih PTK terlebih dahulu',
          })  
        }
      } else {
        this.$swal({
          icon: 'error',
          text: 'Silahkan pilih PTK terlebih dahulu',
        })
      }
    },
    hideModal(){
      this.modalRekapShow = false
      this.resetForm()
    },
    resetForm(){
      this.format = null
      this.file = null
      this.mulai_tanggal = null
      this.mulai_max = ''
      this.sampai_tanggal = null
      this.sampai_min = ''
      this.sampai_max = ''
      this.show = false
    },
    handleOk(bvModalEvent){
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit(){
      var link_open = `/cetak/${this.file}-rekap/${this.format}/${this.aksi}/${this.mulai_tanggal}/${this.sampai_tanggal}/${this.ptk_id}`
      window.open(link_open, '_blank')
    },
    changeMulai(ctx) {
      this.sampai_min = new Date(ctx.selectedYMD);
      this.formatted = ctx.selectedFormatted
      //this.$emit('mulai_tanggal', ctx.selectedYMD)
    },
    changeSampai(ctx) {
      this.formatted = ctx.selectedFormatted
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>