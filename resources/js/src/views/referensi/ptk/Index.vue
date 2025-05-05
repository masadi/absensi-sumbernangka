<template>
  <b-card no-body>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <datatable :loading="loading" :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @sekolah="handleSekolah" @aksi="handleAksi" />
      </div>
    </b-card-body>
    <modal-import @next="handleNext" :data_sekolah="data_sekolah"></modal-import>
    <modal-add @reload="handleReload"></modal-add>
    <modal-edit @reload="handleReload"></modal-edit>
    <modal-bp @reload="handleReload"></modal-bp>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue' //IMPORT COMPONENT DATATABLENYA
import ModalImport from './ModalImport.vue'
import ModalAdd from './ModalAdd.vue'
import ModalEdit from './ModalEdit.vue'
import ModalBp from './ModalBp.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    ModalImport,
    ModalAdd,
    ModalEdit,
    ModalBp,
  },
  data() {
    return {
      isBusy: true,
      fields: [],
      fields_admin: [
        {
          key: 'sekolah',
          label: 'Sekolah',
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
          key: 'email',
          label: 'Email',
          sortable: true,
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
      fields_unit: [
        {
          key: 'nama',
          label: 'Nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'email',
          label: 'Email',
          sortable: true,
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
      sortBy: 'nama', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
      sekolah_id: '',
      data_sekolah: [],
      loading: false,
    }
  },
  created() {
    this.sekolah_id = this.user.sekolah_id;
    this.fields = this.fields_unit;
    if(this.hasRole('administrator')){
      this.fields = this.fields_admin
    }
    eventBus.$on('add-ptk', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-import-ptk');
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      this.loading = true
      let current_page = this.current_page
      this.$http.get('/referensi/ptk', {
        params: {
          sekolah_id: this.sekolah_id,
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
        this.loading = false
        this.isBusy = false
        this.items = getData.data
        this.data_sekolah = response.data.data_sekolah
        this.meta = {
          total: getData.total,
          current_page: getData.current_page,
          per_page: getData.per_page,
          from: getData.from,
          to: getData.to,
          search: this.search,
          sekolah_id: this.sekolah_id,
          data_sekolah: this.data_sekolah,
        }
      })
    },
    handlePerPage(val) {
      this.loading = true
      this.per_page = val
      this.loadPostsData()
    },
    handlePagination(val) {
      this.loading = true
      this.current_page = val
      this.loadPostsData()
    },
    handleSearch(val) {
      this.loading = true
      this.search = val
      this.loadPostsData()
    },
    handleSort(val) {
      if (val.sortBy) {
        this.loading = true
        this.sortBy = val.sortBy
        this.sortByDesc = val.sortDesc
        this.loadPostsData()
      }
    },
    handleSekolah(val){
      this.loading = true
      this.sekolah_id = val
      this.loadPostsData()
    },
    handleAksi(val){
      if(val.aksi == 'reset'){
        console.log('sweet');
      } else if(val.aksi == 'id-card'){
        console.log('download');
        window.open(`/cetak/id-card/ptk/${val.item.ptk_id}`, '_blank')
      } else {
        console.log(`open-modal-${val.aksi}-ptk`);
        eventBus.$emit(`open-modal-${val.aksi}-ptk`, {
          item: val.item,
          data_sekolah: val.data_sekolah,
        });
      }
      console.log(val);
    },
    handleNext(data){
      eventBus.$emit(`open-modal-add-ptk`, data);
    }
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>