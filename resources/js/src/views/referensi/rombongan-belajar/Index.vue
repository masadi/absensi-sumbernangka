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
    <modal-anggota @reload="handleReload"></modal-anggota>
    <!--modal-jadwal @reload="handleReload"></modal-jadwal-->
    <modal-add @reload="handleReload"></modal-add>
    <modal-edit @reload="handleReload"></modal-edit>
    <!--modal-ttd></modal-ttd-->
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue'
import ModalAnggota from './ModalAnggota.vue'
//import ModalJadwal from './ModalJadwal.vue'
import ModalAdd from './ModalAdd.vue'
import ModalEdit from './ModalEdit.vue'
//import ModalTtd from './ModalTtd.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    ModalAnggota,
    //ModalJadwal,
    ModalAdd,
    ModalEdit,
    //ModalTtd,
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
          key: 'tingkat_pendidikan_id',
          label: 'Tingkat',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'walas',
          label: 'Wali Kelas',
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
          key: 'tingkat_pendidikan_id',
          label: 'Tingkat',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'walas',
          label: 'Wali Kelas',
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
    eventBus.$on('add-rombel', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-add-rombel');
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      let current_page = this.current_page
      this.$http.get('/referensi/rombel', {
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
      if(val.aksi == 'id-card'){
        window.open(`/cetak/id-anggota/${val.item.rombongan_belajar_id}`, '_blank')
      } else if(val.aksi == 'id-pelajar'){
        window.open(`/cetak/kartu-pelajar/${val.item.rombongan_belajar_id}`, '_blank')
      } else if(val.aksi == 'id-pkl'){
        window.open(`/cetak/kartu-pkl/${val.item.rombongan_belajar_id}`, '_blank')
      } else if(val.aksi == 'pts' || val.aksi == 'pas'){
      } else if(val.aksi == 'edit'){
        this.$http.post('/referensi/edit-rombel', {
          rombongan_belajar_id: val.item.rombongan_belajar_id,
          sekolah_id: val.item.sekolah_id,
          tingkat_pendidikan_id: val.item.tingkat_pendidikan_id,
        }).then(response => {
          eventBus.$emit('open-modal-edit-rombel', response.data);
        })
      } else if(val.aksi == 'ujian'){
        this.$swal({
          title: 'Pilih Opsi Dibawah Ini!',
          showDenyButton: true,
          showCancelButton: true,
          confirmButtonText: 'Tambah Baru',
          denyButtonText: `Salin`,
          cancelButtonText: 'Batal',
          customClass: {
            confirmButton: 'btn btn-success mr-1',
            denyButton: 'btn btn-primary mr-1',
          },
          allowOutsideClick: false,
        }).then((result) => {
          if (result.isConfirmed) {
            eventBus.$emit(`open-modal-jadwal-rombel`, {
              aksi: val.aksi,
              data: val.item,
              //rombongan_belajar_id: val.item.rombongan_belajar_id,
              //nama: val.item.nama,
            });
          } else if (result.isDenied) {
            this.loading = true
            this.$http.post('/jadwal/salin', {
              jenis_jadwal: val.aksi,
              rombongan_belajar_id: val.item.rombongan_belajar_id,
            }).then(response => {
              let getData = response.data
              this.loading = false
              this.$swal({
                icon: getData.icon,
                title: getData.title,
                text: getData.text,
                customClass: {
                  confirmButton: 'btn btn-success',
                },
                buttonsStyling: false,
                allowOutsideClick: false,
              })
            }).catch(error => {
              console.log(error);
            })
          }
        })
      } else {
        console.log(`open-modal-${val.aksi}-rombel`);
        eventBus.$emit(`open-modal-${val.aksi}-rombel`, val.item);
      }
      console.log(val);
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>