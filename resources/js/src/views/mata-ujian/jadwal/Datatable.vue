<template>
  <div>
    <b-row class="d-flex justify-content-between">
      <b-col md="4" class="mb-2">
        <v-select v-model="meta.per_page" :options="[10, 25, 50, 100]" @input="loadPerPage" :clearable="false" :searchable="false"></v-select>
      </b-col>
      <!--b-col md="4" v-if="meta.data_sekolah.length">
        <v-select v-model="meta.sekolah_id" :reduce="nama => nama.sekolah_id" label="nama" :options="meta.data_sekolah" placeholder="== Filter Unit ==" @input="changeSekolah"></v-select>
      </b-col-->
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
        <template v-slot:cell(rombongan_belajar)="row">
          {{row.item.rombongan_belajar.nama}}
        </template>
        <template v-slot:cell(ketua)="row">
          {{(row.item.ptk) ? row.item.ptk.nama : '-'}}
        </template>
        <template v-slot:cell(actions)="row">
          <b-dropdown id="dropdown-dropleft" dropleft text="Aksi" variant="primary" size="sm">
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'edit')"><pencil-icon /> Edit</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'add')"><calendar-event-icon /> Tambah Mata Ujian</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'cetak')" v-if="row.item.jadwal_ujian_count"><printer-icon /> Cetak ID</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'salin')" v-if="row.item.jadwal_ujian_count"><copy-icon /> Salin Mata Ujian</b-dropdown-item>
            <b-dropdown-item href="javascript:void(0)" @click="aksi(row.item, 'hapus')"><trash-icon /> Hapus</b-dropdown-item>
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
    sync(item, sekolah_id){
      this.loading_sync = true
      this.$http.post(`/referensi/sync-data`, {
        data: item,
        sekolah_id: sekolah_id,
        semester_id: this.user.semester.semester_id,
      }).then(response => {
        this.loading_sync = false
        let getData = response.data
        this.$swal({
          icon: getData.icon,
          title: getData.title,
          text: getData.text,
          customClass: {
            confirmButton: 'btn btn-success',
          },
          allowOutsideClick: false,
        }).then(result => {
          this.$emit('per_page', this.meta.per_page)
        })
      });
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
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>