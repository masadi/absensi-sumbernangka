<template>
  <b-card no-body>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <b-row>
          <b-col md="4" class="mb-2">
            <b-form-datepicker v-model="start" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal" @input="onContextStart" placeholder="Filter Tanggal" />
          </b-col>
          <b-col md="4" class="mb-2">
            <b-form-datepicker v-model="end" show-decade-nav button-variant="outline-secondary" left locale="id" aria-controls="tanggal" @input="onContextEnd" placeholder="Filter Tanggal" />
          </b-col>
          <b-col md="4" class="mb-2">
            <b-button block variant="warning" @click="unduhRekap">Download Rekap</b-button>
          </b-col>
        </b-row>
        <datatable :loading="loading" :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @aksi="handleAksi" />
      </div>
    </b-card-body>
    <modal-add @reload="handleReload"></modal-add>
    <!--modal-kasek @reload="handleReload"></modal-kasek>
    <modal-bp @reload="handleReload"></modal-bp-->
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner, BRow, BCol, BFormDatepicker, BButton } from 'bootstrap-vue'
import Datatable from './Datatable.vue' //IMPORT COMPONENT DATATABLENYA
import ModalAdd from './ModalAdd.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    BRow, 
    BCol,
    BFormDatepicker,
    BButton,
    Datatable,
    ModalAdd,
  },
  data() {
    return {
      start: null,
      end: null,
      loading: false,
      isBusy: true,
      fields: [
        {
          key: 'nama',
          label: 'Nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'kelas',
          label: 'Kelas',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'show_tanggal',
          label: 'Tanggal',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'masalah',
          label: 'Masalah/Pelanggaran',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'actions',
          label: 'Aksi',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
      ],
      items: [],
      meta: {},
      current_page: 1, //DEFAULT PAGE YANG AKTIF ADA PAGE 1
      per_page: 10, //DEFAULT LOAD PERPAGE ADALAH 10
      search: '',
      sortBy: 'created_at', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: true, //ASCEDING
    }
  },
  created() {
    eventBus.$on('add-pelanggaran', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-add-pelanggaran', {
        sekolah_id: this.user.sekolah_id,
        semester_id: this.user.semester.semester_id,
        periode_aktif: this.user.semester.nama,
      });
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      this.loading = true
      let current_page = this.current_page
      this.$http.get('/pelanggaran', {
        params: {
          semester_id: this.user.semester.semester_id,
          periode_aktif: this.user.semester.nama,
          sekolah_id: this.user.sekolah_id,
          page: current_page,
          per_page: this.per_page,
          q: this.search,
          sortby: this.sortBy,
          sortbydesc: this.sortByDesc ? 'DESC' : 'ASC',
          start: this.start,
          end: this.end,
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
        }
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
    handleAksi(val){
      console.log(val);
      eventBus.$emit(`open-modal-${val.aksi}-unit`, val.item);
    },
    onContextStart(val) {
      this.start = val
      this.end = null
    },
    onContextEnd(val) {
      this.end = val
      if(this.start && this.end){
        this.loadPostsData()
      }
    },
    unduhRekap(){
      if(this.start && this.end){
        window.open(`/unduhan/pelanggaran/${this.user.sekolah_id}/${this.user.semester.semester_id}/${this.start}/${this.end}`)
      } else {
        window.open(`/unduhan/pelanggaran/${this.user.sekolah_id}/${this.user.semester.semester_id}`)
      }
    }
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>