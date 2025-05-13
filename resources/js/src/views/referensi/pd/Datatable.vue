<template>
  <div>
    <b-row class="d-flex justify-content-between">
      <b-col md="3" class="mb-2">
        <v-select v-model="meta.per_page" :options="[10, 25, 50, 100]" @input="loadPerPage" :clearable="false" :searchable="false"></v-select>
      </b-col>
      <b-col md="3" v-if="meta.data_sekolah.length">
        <v-select v-model="meta.sekolah_id" :reduce="nama => nama.sekolah_id" label="nama" :options="meta.data_sekolah" placeholder="== Filter Unit ==" @input="changeSekolah"></v-select>
      </b-col>
      <b-col md="3" v-if="meta.data_sekolah.length">
        <v-select v-model="meta.rombongan_belajar_id" :reduce="nama => nama.rombongan_belajar_id" label="nama" :options="meta.data_rombel" placeholder="== Filter Rombel ==" @input="changeRombel"></v-select>
      </b-col>
      <b-col md="3">
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
        <template v-slot:cell(sekolah)="row">
          {{row.item.sekolah.nama}}
        </template>
        <template v-slot:cell(kelas)="row">
          {{row.item.kelas.nama}}
        </template>
        <template v-slot:cell(actions)="row">
          <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="primary" size="sm">
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'edit')"><edit-icon /> Edit</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'id-card')"><qrcode-icon /> Cetak ID Scan</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'delete')"><trash-icon /> Hapus</b-dropdown-item>
            <!--b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'id-pelajar')"><id-badge-2-icon /> Cetak ID Pelajar</b-dropdown-item>
            <template v-if="row.item.kelas">
              <template v-if="row.item.kelas.tingkat_pendidikan_id == 11 || row.item.kelas.tingkat_pendidikan_id == 12">
                <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'id-pkl')"><id-badge-icon /> Cetak ID PKL</b-dropdown-item>
              </template>
            </template>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'anggota')"><refresh-icon /> Pindah Rombel</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'password')"><shield-off-icon /> Reset Password</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'rekap-excel')"><file-spreadsheet-icon /> Rekap Presensi Excel</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'rekap-pdf')"><file-invoice-icon /> Rekap Presensi PDF</b-dropdown-item-->
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
import { BRow, BCol, BFormInput, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BOverlay, BButtonGroup, BButton } from 'bootstrap-vue'
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
    loadPerPage(val) {
      this.$emit('per_page', this.meta.per_page)
    },
    changeSekolah(val){
      this.$emit('sekolah', this.meta.sekolah_id)
    },
    changeRombel(){
      this.$emit('rombel', this.meta.rombongan_belajar_id)
    },
    changePage(val) {
      this.$emit('pagination', val)
    },
    search: _.debounce(function (e) {
      this.$emit('search', e)
    }, 500),
  },
}
</script>