<template>
  <b-card no-body>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <datatable :loading="loading" :isBusy="isBusy" :items="items" :data_sekolah="data_sekolah" :data_tingkat="data_tingkat" :data_rombel="data_rombel" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @sekolah="handleSekolah" @tingkat="handleTingkat" @rombel="handleRombel" />
      </div>
    </b-card-body>
    <modal-admin @reload="handleReload" />
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue' //IMPORT COMPONENT DATATABLENYA
import ModalAdmin from './ModalAdmin.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    ModalAdmin,
  },
  data() {
    return {
      loading: false,
      isBusy: true,
      fields: [
        {
          key: 'name',
          label: 'Nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'email',
          label: 'Email',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'sekolah',
          label: 'Unit',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'kelas',
          label: 'Rombel',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'password',
          label: 'Password',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'actions',
          label: 'Aksi',
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
      sortBy: 'name', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
      sekolah_id: null,
      tingkat: null,
      rombongan_belajar_id: null,
      data_sekolah: [],
      data_tingkat: [],
      data_rombel: [],
    }
  },
  created() {
    eventBus.$on('add-admin', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-add-admin', this.data_sekolah);
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      this.loading = true
      let current_page = this.current_page
      this.$http.get('/auth/users/list', {
        params: {
          semester_id: this.user.semester.semester_id,
          periode_aktif: this.user.semester.nama,
          page: current_page,
          per_page: this.per_page,
          q: this.search,
          sortby: this.sortBy,
          sortbydesc: this.sortByDesc ? 'DESC' : 'ASC',
          sekolah_id: this.sekolah_id,
          tingkat: this.tingkat,
          rombongan_belajar_id: this.rombongan_belajar_id,
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
          sekolah_id: this.sekolah_id,
          tingkat: this.tingkat,
          rombongan_belajar_id: this.rombongan_belajar_id,
        }
        this.data_sekolah = response.data.data_sekolah
        this.data_tingkat = response.data.data_tingkat
        this.data_rombel = response.data.data_rombel
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
    handleSekolah(val) {
      this.sekolah_id = val
      this.loadPostsData()
    },
    handleTingkat(val) {
      this.tingkat = val
      this.loadPostsData()
    },
    handleRombel(val) {
      this.rombongan_belajar_id = val
      this.loadPostsData()
    },
  },
}
</script>