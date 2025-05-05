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
    <modal-jadwal @reload="handleReload"></modal-jadwal>
    <modal-add @reload="handleReload"></modal-add>
    <modal-edit @reload="handleReload"></modal-edit>
    <modal-salin @reload="handleReload"></modal-salin>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue'
import ModalJadwal from './ModalJadwal.vue'
import ModalAdd from './ModalAdd.vue'
import ModalEdit from './ModalEdit.vue'
import ModalSalin from './ModalSalin.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    ModalJadwal,
    ModalAdd,
    ModalEdit,
    ModalSalin,
  },
  data() {
    return {
      isBusy: true,
      fields: [
        {
          key: 'nama',
          label: 'Nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'rombongan_belajar',
          label: 'Rombel',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'ketua',
          label: 'Ketua Pelaksana',
          sortable: false,
          thClass: 'text-center',
        },
        {
          key: 'tanggal_indo',
          label: 'Tanggal',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'jadwal_ujian_count',
          label: 'Jml Mata Ujian',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center',
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
    eventBus.$on('add-jadwal', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-add-jadwal');
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      let current_page = this.current_page
      this.$http.get('/jadwal', {
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
        //this.data_sekolah = response.data.data_sekolah
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
      if(val.aksi == 'cetak'){
        console.log('download');
        window.open(`/cetak/jadwal/${val.item.id}`, '_blank')
        //window.open(`/cetak/id-anggota/${val.item.id}`, '_blank')
      } else if(val.aksi == 'edit'){
        this.$http.post('/jadwal/edit', {
          sekolah_id: val.item.rombongan_belajar.sekolah_id,
          jadwal_id: val.item.id,
          semester_id: this.user.semester.semester_id,
        }).then(response => {
          eventBus.$emit('open-modal-edit-jadwal', response.data);
        })
      } else if(val.aksi == 'add'){
        eventBus.$emit(`open-modal-mata-ujian`, {
          aksi: val.aksi,
          data: val.item,
        });
      } else if(val.aksi == 'salin'){
        eventBus.$emit(`open-modal-salin-ujian`, val.item);
      } else if(val.aksi == 'hapus'){
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
            this.loading_form = true
            this.$http.post('/jadwal/hapus', {
              aksi: 'jadwal',
              id: val.item.id,
            }).then(response => {
              this.loading_form = false
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
        console.log(`open-modal-${val.aksi}-rombel`);
        eventBus.$emit(`open-modal-${val.aksi}-rombel`, val.item);
      }
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>