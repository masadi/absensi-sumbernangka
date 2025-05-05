<template>
  <b-card no-body>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <datatable :loading="loading" :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @selected="handleSelected" @sekolah="handleSekolah" />
      </div>
    </b-card-body>
    <modal-rekap :aksi="aksi" />
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue' //IMPORT COMPONENT DATATABLENYA
import ModalRekap from './ModalRekap.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    ModalRekap,
  },
  data() {
    return {
      loading: false,
      isBusy: true,
      fields: [
        {
          key: 'selected',
          label: '#',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'sekolah',
          label: 'Unit',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'nama',
          label: 'Nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'hari_count',
          label: 'Hari Aktif',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'absen_count',
          label: 'Hadir',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'izin_count',
          label: 'Izin',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'sakit_count',
          label: 'Sakit',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'cuti_count',
          label: 'Cuti',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'alpa_count',
          label: 'Alpa',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        }
      ],
      items: [],
      meta: {},
      current_page: 1, //DEFAULT PAGE YANG AKTIF ADA PAGE 1
      per_page: 10, //DEFAULT LOAD PERPAGE ADALAH 10
      search: '',
      sortBy: 'nama', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
      sekolah_id: null,
      aksi: 'ptk',
      data: null,
      tanggal_mulai: null,
      tanggal_selesai: null,
    }
  },
  created() {
    eventBus.$on('cetak-rekap-guru', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-cetak-rekap-guru', this.data);
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      this.loading = true
      let current_page = this.current_page
      this.$http.get('/rekapitulasi', {
        params: {
          sekolah_id: this.sekolah_id,
          aksi: this.aksi,
          semester_id: this.user.semester.semester_id,
          periode_aktif: this.user.semester.nama,
          page: current_page,
          per_page: this.per_page,
          q: this.search,
          sortby: this.sortBy,
          sortbydesc: this.sortByDesc ? 'DESC' : 'ASC'
        }
      }).then(response => {
        let getData = response.data.data
        this.isBusy = false
        this.loading = false
        this.items = getData.data
        this.meta = {
          total: getData.total,
          current_page: getData.current_page,
          per_page: getData.per_page,
          from: getData.from,
          to: getData.to,
          sekolah_id: response.data.sekolah_id,
          data_sekolah: response.data.data_sekolah
        }
        this.tanggal_mulai = response.data.tanggal_mulai
        this.tanggal_selesai = response.data.tanggal_selesai
      })
    },
    handlePerPage(val) {
      this.per_page = val
      this.loadPostsData()
    },
    handlePagination(val) {
      this.current_page = val
      this.loadPostsData()
    },
    handleSearch(val) {
      this.search = val
      this.loadPostsData()
    },
    handleSort(val) {
      if (val.sortBy) {
        this.sortBy = val.sortBy
        this.sortByDesc = val.sortDesc
        this.loadPostsData()
      }
    },
    handleSelected(val){
      this.data = {
        ptk_id: val.item,
        tanggal_mulai: this.tanggal_mulai,
        tanggal_selesai: this.tanggal_selesai,
      }
    },
    handleSekolah(val){
      this.sekolah_id = val
      this.loadPostsData()
    },
  },
}
</script>