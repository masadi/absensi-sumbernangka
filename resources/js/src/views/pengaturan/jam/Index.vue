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
    <modal-add @reload="handleReload"></modal-add>
    <modal-edit @reload="handleReload"></modal-edit>
    <modal-detil></modal-detil>
    <modal-ptk @reload="handleReload"></modal-ptk>
    <modal-pd @reload="handleReload"></modal-pd>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue' //IMPORT COMPONENT DATATABLENYA
import ModalAdd from './ModalAdd.vue'
import ModalEdit from './ModalEdit.vue'
import ModalDetil from './ModalDetil.vue'
import ModalPtk from './ModalPtk.vue'
import ModalPd from './ModalPd.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    ModalAdd,
    ModalEdit,
    ModalDetil,
    ModalPtk,
    ModalPd,
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
          key: 'unit',
          label: 'UNIT',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'jam_hari_count',
          label: 'JML HARI',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'jam_ptk_count',
          label: 'JML PTK',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center'
        },
        {
          key: 'jam_pd_count',
          label: 'JML PD',
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
    eventBus.$on('add-jam', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-add-jam');
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      this.loading = true
      let current_page = this.current_page
      this.$http.get('/jam', {
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
        let getData = response.data
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
      if(val.aksi == 'hapus'){
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
            this.$http.post('/jam/hapus', {
              id: val.item.id,
            }).then(response => {
              let getData = response.data
              this.$swal({
                icon: getData.icon,
                title: getData.title,
                text: getData.text,
                customClass: {
                  confirmButton: 'btn btn-success',
                },
                buttonsStyling: false,
              }).then(result => {
                this.loadPostsData()
              })
            });
          }
        })
      } else {
        eventBus.$emit(`open-modal-${val.aksi}-jam`, val.item);
      }
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>