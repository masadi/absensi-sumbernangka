<template>
  <div>
    <b-row class="d-flex justify-content-between">
      <b-col md="6" class="mb-2">
        <b-form-datepicker v-model="meta.mulai_tanggal" :max="mulai_max" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="mulai_tanggal" @context="changeMulai" placeholder="Pilih Mulai Tanggal" />
      </b-col>
      <b-col md="6" class="mb-2">
        <b-form-datepicker v-model="meta.sampai_tanggal" :min="sampai_min" :max="sampai_max" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="sampai_tanggal" @context="changeSampai" placeholder="Pilih Sampai Tanggal" />
      </b-col>
    </b-row>
    <b-row class="d-flex justify-content-between">
      <b-col md="4" class="mb-2">
        <v-select v-model="meta.per_page" :options="[10, 25, 50, 100]" @input="loadPerPage" :clearable="false" :searchable="false"></v-select>
      </b-col>
      <b-col md="4" v-if="meta.data_sekolah.length">
        <v-select v-model="meta.sekolah_id" :reduce="nama => nama.sekolah_id" label="nama" :options="meta.data_sekolah" placeholder="== Filter Unit ==" @input="changeSekolah"></v-select>
      </b-col>
      <b-col md="4">
        <b-form-input v-model="meta.search" @input="search" placeholder="Cari data..."></b-form-input>
      </b-col>
    </b-row>
    <b-overlay :show="loading" rounded opacity="0.6" size="lg" spinner-variant="warning">
      <b-table bordered striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty :busy="isBusy">
        <template v-slot:empty>
          <p class="text-center">Tidak ada data untuk ditampilkan</p>
        </template>
        <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>
        <template v-slot:cell(nama)="row">
          <template v-if="row.item.ptk">
            {{ row.item.ptk.nama }}
          </template>
          <template v-if="row.item.pd">
            {{ row.item.pd.nama }}
          </template>
        </template>
        <template v-slot:cell(walas)="row">
          {{row.item.walas.nama}}
        </template>
        <template v-slot:cell(absen_masuk_1)="row">
          {{row.item.walas.nama}}
        </template>
        <template v-slot:cell(absen_masuk_2)="row">
          {{row.item.walas.nama}}
        </template>
        <template v-slot:cell(actions)="row">
          <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="primary" size="sm">
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'edit')" v-if="row.item.ptk_id"><pencil-icon /> Edit Jam Scan</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'keterangan')"><edit-icon /> Input Keterangan</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="download(row.item, 'pdf')"><font-awesome-icon icon="fa-regular fa-file-pdf" size="xl" /> Laporan Harian</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="download(row.item, 'excel')"><font-awesome-icon icon="fa-regular fa-file-excel" size="xl" /> Laporan Harian</b-dropdown-item>
          </b-dropdown>
        </template>
      </b-table>
    </b-overlay>
    <b-row class="mt-2">
      <b-col md="6">
        <p>Menampilkan {{ (meta.from) ? meta.from : 0 }} sampai {{ meta.to }} dari {{ meta.total }} entri</p>
      </b-col>
      <b-col md="6">
        <b-pagination v-model="meta.current_page" :total-rows="meta.total" :per-page="meta.per_page" align="right" @change="changePage" aria-controls="dw-datatable"></b-pagination>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import _ from 'lodash'
import { BRow, BCol, BFormInput, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BOverlay, BButtonGroup, BButton, BFormDatepicker } from 'bootstrap-vue'
import vSelect from 'vue-select'
export default {
  components: {
    BRow,
    BCol,
    BFormInput,
    BTable,
    BSpinner,
    BPagination,
    BDropdown,
    BDropdownItem,
    BOverlay,
    BButtonGroup,
    BButton,
    BFormDatepicker,
    vSelect,
  },
  props: {
    items: {
      type: Array,
      required: true
    },
    fields: {
      type: Array,
      required: true
    },
    meta: {
      required: true
    },
    isBusy: {
      type: Boolean,
      default: () => true,
    },
    loading: {
      type: Boolean,
      default: () => false,
    }
  },
  data() {
    return {
      sortBy: null,
      sortDesc: false,
      mulai_max: new Date(),
      sampai_min: '',
      sampai_max: new Date(),
    }
  },
  watch: {
    sortBy(val) {
      this.$emit('sort', {
        sortBy: this.sortBy,
        sortDesc: this.sortDesc
      })
    },
    sortDesc(val) {
      this.$emit('sort', {
        sortBy: this.sortBy,
        sortDesc: this.sortDesc
      })
    }
  },
  methods: {
    aksi(item, aksi){
      this.$emit('aksi', {
        aksi: aksi,
        item: item,
      })
    },
    formatDate(date) {
      var d = new Date(date),
          month = '' + (d.getMonth() + 1),
          day = '' + d.getDate(),
          year = d.getFullYear();

      if (month.length < 2) 
          month = '0' + month;
      if (day.length < 2) 
          day = '0' + day;

      return [year, month, day].join('-');
    },
    download(item, aksi){
      var date = new Date();
      var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
      var mulai_tanggal = this.meta.mulai_tanggal
      var sampai_tanggal = this.meta.sampai_tanggal
      if(!mulai_tanggal){
        mulai_tanggal = this.formatDate(firstDay)
      }
      if(!sampai_tanggal){
        sampai_tanggal = this.formatDate(date)
      }
      if(aksi == 'pdf'){
        if(item.ptk_id){
          window.open(`/cetak/pdf/ptk/${item.id}/harian/${mulai_tanggal}/${sampai_tanggal}`, '_blank')
        } else {
          window.open(`/cetak/pdf/pd/${item.id}/harian/${mulai_tanggal}/${sampai_tanggal}`, '_blank')
        }
      } else {
        if(item.ptk_id){
          window.open(`/cetak/excel/ptk/${item.id}/harian/${mulai_tanggal}/${sampai_tanggal}`, '_blank')
        } else {
          window.open(`/cetak/excel/pd/${item.id}/harian/${mulai_tanggal}/${sampai_tanggal}`, '_blank')
        }
      }
    },
    loadPerPage(val) {
      this.$emit('per_page', this.meta.per_page)
    },
    changeSekolah(val){
      this.$emit('sekolah', this.meta.sekolah_id)
    },
    changePage(val) {
      this.$emit('pagination', val)
    },
    search: _.debounce(function (e) {
      this.$emit('search', e)
    }, 500),
    changeMulai(ctx) {
      this.sampai_min = new Date(ctx.selectedYMD);
      this.meta.sampai_tanggal = ''
      this.formatted = ctx.selectedFormatted
      this.$emit('mulai_tanggal', ctx.selectedYMD)
    },
    changeSampai(ctx) {
      this.formatted = ctx.selectedFormatted
      this.$emit('sampai_tanggal', ctx.selectedYMD)
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>