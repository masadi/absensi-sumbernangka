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
    <add-siswa @reload="handleReload"></add-siswa>
    <modal-siswa @reload="handleReload"></modal-siswa>
    <!--modal-jadwal @reload="handleReload"></modal-jadwal-->
  </b-card>
</template>

<script>
import { BCard, BCardBody, BSpinner } from 'bootstrap-vue'
import Datatable from './Datatable.vue'
import AddSiswa from './AddSiswa.vue'
import ModalSiswa from './ModalSiswa.vue'
import ModalJadwal from './ModalJadwal.vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BCard,
    BCardBody,
    BSpinner,
    Datatable,
    AddSiswa,
    ModalSiswa,
    //ModalJadwal
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
          key: 'nisn',
          label: 'NISN',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'wa',
          label: 'WA',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'kelas',
          label: 'Kelas',
          sortable: true,
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
      fields_unit: [
        {
          key: 'nama',
          label: 'Nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'nisn',
          label: 'NISN',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'wa',
          label: 'WA',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'kelas',
          label: 'Kelas',
          sortable: true,
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
    this.fields = this.fields_unit;
    if(this.hasRole('administrator')){
      this.fields = this.fields_admin
    }
    eventBus.$on('add-pd', this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    handleEvent(){
      eventBus.$emit('open-modal-import-pd');
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      this.loading = true
      let current_page = this.current_page
      this.$http.get('/referensi/pd', {
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
        window.open(`/cetak/id-card/pd/${val.item.peserta_didik_id}`, '_blank')
      } else if(val.aksi == 'id-pelajar'){
        window.open(`/cetak/id-pelajar/${val.item.peserta_didik_id}`, '_blank')
      } else if(val.aksi == 'id-pkl'){
        window.open(`/cetak/id-pkl/${val.item.peserta_didik_id}`, '_blank')
      } else if(val.aksi == 'delete'){
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
          this.$http.post('/referensi/delete-siswa', {
            peserta_didik_id: val.item.peserta_didik_id,
          }).then(response => {
            let getData = response.data
            this.loading_modal = false
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
      } else if(val.aksi == 'pts' || val.aksi == 'pas'){
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
          /* Read more about isConfirmed, isDenied below */
          if (result.isConfirmed) {
            //this.$swal('Saved!', '', 'success')
            eventBus.$emit(`open-modal-jadwal-pd`, {
              aksi: val.aksi,
              rombongan_belajar_id: val.item.rombongan_belajar_id,
              nama: val.item.nama,
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
        console.log(`open-modal-${val.aksi}-pd`);
        eventBus.$emit(`open-modal-${val.aksi}-pd`, val.item);
      }
      console.log(val);
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>