<template>
  <b-card no-body>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <datatable :loading="loading" :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @aksi="handleAksi" />
      </div>
    </b-card-body>
    <modal-edit @reload="handleReload"></modal-edit>
    <modal-kasek @reload="handleReload"></modal-kasek>
    <modal-bp @reload="handleReload"></modal-bp>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue' //IMPORT COMPONENT DATATABLENYA
import ModalEdit from './ModalEdit.vue'
import ModalKasek from './ModalKasek.vue'
import ModalBp from './ModalBp.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    ModalEdit,
    ModalKasek,
    ModalBp,
  },
  data() {
    return {
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
          key: 'npsn',
          label: 'NPSN',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'ptk_count',
          label: 'JML PTK',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'pd_count',
          label: 'JML PD',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'id_card',
          label: 'Cetak ID Card',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        /*{
          key: 'sync',
          label: 'Sync Data',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },*/
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
    eventBus.$on('add-guru', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-add-guru');
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      this.loading = true
      let current_page = this.current_page
      this.$http.get('/referensi/unit', {
        params: {
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
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>