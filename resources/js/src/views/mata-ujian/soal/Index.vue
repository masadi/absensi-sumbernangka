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
    <modal-add @reload="handleReload"></modal-add>
    <modal-soal @reload="handleReload"></modal-soal>
    <modal-edit @reload="handleReload"></modal-edit>
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue'
import ModalAdd from './ModalAdd.vue'
import ModalSoal from './ModalSoal.vue'
import ModalEdit from './ModalEdit.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    ModalAdd,
    ModalSoal,
    ModalEdit,
  },
  data() {
    return {
      isBusy: true,
      fields: [
      {
          key: 'mata_ujian',
          label: 'Mata Ujian',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'mata_pelajaran',
          label: 'Mata Pelajaran',
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
          key: 'jumlah_soal',
          label: 'Jml Soal',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'jumlah_kunci',
          label: 'Jml Kunci Jawaban',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'jumlah_pg',
          label: 'Opsi PG',
          sortable: false,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'status',
          label: 'Status',
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
      sortBy: 'created_at', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: false, //ASCEDING
      sekolah_id: '',
      data_sekolah: [],
      loading: false,
    }
  },
  created() {
    this.sekolah_id = this.user.sekolah_id;
    eventBus.$on('add-soal', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-add-soal');
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      let current_page = this.current_page
      this.$http.get('/soal-ujian', {
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
      if(val.aksi == 'edit'){
        eventBus.$emit('open-modal-edit-soal', val.item);
      } else if(val.aksi == 'add'){
        eventBus.$emit(`open-modal-mata-ujian`, {
          aksi: val.aksi,
          data: val.item,
        });
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
            this.$http.delete(`/soal-ujian/destroy/${val.item.id}`).then(response => {
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
      } else if(val.aksi == 'aktif'){
        //dan menonaktifkan Mata Ujian yang lain
        var text = `'Tindakan ini akan mengaktifkan Mata Ujian Mata Pelajaran ${val.item.jadwal_ujian.mata_pelajaran.nama}!`
        if(val.item.status){
          text = `'Tindakan ini akan menonaktifkan Mata Ujian Mata Pelajaran ${val.item.jadwal_ujian.mata_pelajaran.nama}!`
        }
        this.$swal({
          title: 'Apakah Anda yakin?',
          text: text,
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
            this.$http.post('/soal-ujian/status', {
              soal_ujian_id: val.item.id,
              status: val.item.status,
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
        console.log(`open-modal-${val.aksi}-soal`);
        eventBus.$emit(`open-modal-${val.aksi}-soal`, val.item);
      }
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>