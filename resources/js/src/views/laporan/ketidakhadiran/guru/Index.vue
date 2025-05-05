<template>
  <b-card no-body>
    <b-card-header>
      <h4 class="mb-0">{{card_title}}</h4>
      <b-card-text class="mb-0">
        <b-overlay :show="loading" opacity="0.6" size="md" spinner-variant="secondary">
          <b-button block variant="success" @click="cetak">Cetak Rekapitulasi</b-button>
        </b-overlay>
      </b-card-text>
    </b-card-header>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <datatable :loading="loading" :isBusy="isBusy" :items="items" :fields="fields" :meta="meta" @per_page="handlePerPage" @pagination="handlePagination" @search="handleSearch" @sort="handleSort" @sekolah="handleSekolah" @aksi="handleAksi" @mulai_tanggal="handleMulai" @sampai_tanggal="handleSampai" />
      </div>
    </b-card-body>
    <modal-izin @loading="handleLoading" />
    <modal-scan @reload="handleReload" />
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardText, BCardBody, BOverlay, BSpinner, BButton } from 'bootstrap-vue'
import Datatable from './Datatable.vue'
import eventBus from '@core/utils/eventBus'
import ModalIzin from './../modal/ModalIzin.vue'
import ModalScan from './../modal/ModalScan.vue'
export default {
  components: {
    BCard, BCardHeader, BCardText, BCardBody, BOverlay, BSpinner, BButton,
    ModalIzin,
    ModalScan,
    Datatable,
  },
  data() {
    return {
      card_title: 'Rekapitulasi Ketidakhadiran per Bulan Ini',
      isBusy: true,
      fields: [
        {
          key: 'nama',
          label: 'nama',
          sortable: true,
          thClass: 'text-center',
        },
        {
          key: 'tanggal_scan',
          label: 'tanggal',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'jam_masuk',
          label: 'masuk',
          sortable: true,
          thClass: 'text-center',
          tdClass: 'text-center',
        },
        {
          key: 'jam_pulang',
          label: 'pulang',
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
      aksi: 'guru',
      items: [],
      meta: {},
      current_page: 1, //DEFAULT PAGE YANG AKTIF ADA PAGE 1
      per_page: 10, //DEFAULT LOAD PERPAGE ADALAH 10
      search: '',
      sortBy: 'created_at', //DEFAULT SORTNYA ADALAH CREATED_AT
      sortByDesc: true, //ASCEDING
      sekolah_id: '',
      data_sekolah: [],
      loading: false,
      mulai_tanggal: '',
      sampai_tanggal: '',
    }
  },
  created() {
    eventBus.$on(`cetak-rekap-ketidakhadiran-${this.aksi}`, this.handleEvent);
    this.loadPostsData()
  },
  methods: {
    cetak(){
      window.open(`/cetak/ketidakhadiran/ptk/${this.mulai_tanggal}/${this.sampai_tanggal}`, '_blank')
    },
    handleEvent(){
      eventBus.$emit('open-modal-cetak-rekap-ketidakhadiran-guru');
    },
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      let current_page = this.current_page
      this.loading = true
      this.$http.get('/laporan/ketidakhadiran', {
        params: {
          aksi: this.aksi,
          sekolah_id: this.sekolah_id,
          semester_id: this.user.semester.semester_id,
          periode_aktif: this.user.semester.nama,
          page: current_page,
          per_page: this.per_page,
          q: this.search,
          sortby: this.sortBy,
          sortbydesc: this.sortByDesc ? 'DESC' : 'ASC',
          mulai_tanggal: this.mulai_tanggal,
          sampai_tanggal: this.sampai_tanggal,
        }
      }).then(response => {
        if(response.data.mulai_tanggal_str){
          this.card_title = `Rekapitulasi Ketidakhadiran per Tanggal ${response.data.mulai_tanggal_str} s/d ${response.data.sampai_tanggal_str}`
        }
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
          mulai_tanggal: response.data.mulai_tanggal,
          sampai_tanggal: response.data.sampai_tanggal
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
      if(val.aksi == 'absen'){
        console.log(val);
        eventBus.$emit(`open-modal-scan`, val.item)
      } else {
        eventBus.$emit(`open-modal-izin`, {
          item: val.item,
          keterangan: val.aksi,
        });
      }
    },
    handleMulai(val){
      this.mulai_tanggal = val
    },
    handleSampai(val){
      if(val){
        this.sampai_tanggal = val
        this.loadPostsData()
      }
    },
    handleLoading(val){
      this.loading = val
    }
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>