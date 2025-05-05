<template>
  <b-modal v-model="pdModalShow" title="Masukkan Peserta Didik" size="xl" ok-only ok-variant="secondary" ok-title="Tutup" scrollable>
    <b-row>
      <b-col cols="6">
        <b-row>
          <b-col cols="8" class="mb-2">
            <b-form-input v-model="filter_terdaftar" @input="cari_terdaftar" placeholder="Cari data..."></b-form-input>
          </b-col>
          <b-col cols="4">
            <b-overlay :show="isBusyTerdaftar" rounded opacity="0.6" spinner-small spinner-variant="danger" class="d-inline-block">
              <b-button :disabled="disabledTerdaftar" block variant="danger" @click="allOut">Keluarkan Semua</b-button>
            </b-overlay>
          </b-col>
        </b-row>
        <b-table bordered striped :items="terdaftar" :fields="fields" show-empty :busy="isBusyTerdaftar">
          <template v-slot:empty>
            <p class="text-center">Tidak ada data untuk ditampilkan</p>
          </template>
          <template #table-busy>
            <div class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
          </template>
          <template v-slot:cell(actions)="row">
            <b-button variant="danger" size="sm" @click="keluarkan(row.item.peserta_didik_id)">Keluarkan</b-button>
          </template>
        </b-table>
        <b-row class="mt-2">
          <b-col md="6">
            <p>Menampilkan {{ (from_terdaftar) ? from_terdaftar : 0 }} sampai {{ to_terdaftar }} dari {{ total_terdaftar }} entri</p>
          </b-col>
          <b-col md="6">
            <b-pagination v-model="current_terdaftar" :total-rows="total_terdaftar" :per-page="25" align="right" @change="changeTerdaftar" aria-controls="dw-datatable"></b-pagination>
          </b-col>
        </b-row>
      </b-col>
      <b-col cols="6">
        <b-row>
          <b-col cols="8" class="mb-2">
            <b-form-input v-model="filter_kosong" @input="cari_kosong" placeholder="Cari data..."></b-form-input>
          </b-col>
          <b-col cols="4">
            <b-overlay :show="isBusyKosong" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block">
              <b-button :disabled="disabledKosong" block variant="success" @click="allIn">Masukkan Semua</b-button>
            </b-overlay>
          </b-col>
        </b-row>
        <b-table bordered striped :items="kosong" :fields="fields" show-empty :busy="isBusyKosong">
          <template v-slot:empty>
            <p class="text-center">Tidak ada data untuk ditampilkan</p>
          </template>
          <template #table-busy>
            <div class="text-center text-danger my-2">
              <b-spinner class="align-middle"></b-spinner>
              <strong>Loading...</strong>
            </div>
          </template>
          <template v-slot:cell(actions)="row">
            <b-button variant="success" size="sm" @click="masukkan(row.item.peserta_didik_id)">Masukkan</b-button>
          </template>
        </b-table>
        <b-row class="mt-2">
          <b-col md="6">
            <p>Menampilkan {{ (from_kosong) ? from_kosong : 0 }} sampai {{ to_kosong }} dari {{ total_kosong }} entri</p>
          </b-col>
          <b-col md="6">
            <b-pagination v-model="current_kosong" :total-rows="total_kosong" :per-page="25" align="right" @change="changeKosong" aria-controls="dw-datatable"></b-pagination>
          </b-col>
        </b-row>
      </b-col>
    </b-row>
  </b-modal>
</template>

<script>
import { BRow, BCol, BTable, BFormInput, BButton, BSpinner, BPagination, BOverlay } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BRow, BCol, BTable, BFormInput, BButton, BSpinner, BPagination, BOverlay
  },
  data() {
    return {
      pdModalShow: false,
      jam_id: '',
      filter_terdaftar: '',
      filter_kosong: '',
      isBusyTerdaftar: true,
      isBusyKosong: true,
      terdaftar: [],
      kosong: [],
      fields: [
        {
          key: 'nama',
          label: 'Nama',
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
      from_terdaftar: '',
      to_terdaftar: '',
      total_terdaftar: '',
      current_terdaftar: 1,
      total_kosong: '',
      current_kosong: 1,
      from_kosong: '',
      to_kosong: '',
      disabledTerdaftar: false,
      disabledKosong: false,
    }
  },
  created() {
    eventBus.$on('open-modal-pd-jam', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.jam_id = data.id
      this.getTerdaftar()
      this.getKosong()
    },
    getTerdaftar(){
      this.isBusyTerdaftar = true
      this.$http.get('/jam/terdaftar', {
        params: {
          data: 'pd',
          jam_id: this.jam_id,
          filter_nama: this.filter_terdaftar,
          page: this.current_terdaftar,
        }
      }).then(response => {
        this.isBusyTerdaftar = false
        var getData = response.data
        this.terdaftar = getData.data
        this.disabledTerdaftar = this.terdaftar.length == 0
        this.total_terdaftar = getData.total
        this.current_terdaftar = getData.current_page
        this.from_terdaftar = getData.from
        this.to_terdaftar = getData.to
        if(getData.errors){
          this.$swal({
            icon: 'error',
            title: 'Error',
            text: getData.errors,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          })
        } else {
          this.data_anggota = getData
        }
      })
    },
    getKosong(){
      this.isBusyKosong = true
      this.$http.get('/jam/kosong', {
        params: {
          data: 'pd',
          jam_id: this.jam_id,
          filter_nama: this.filter_kosong,
          page: this.current_kosong,
        }
      }).then(response => {
        this.isBusyKosong = false
        var getData = response.data
        this.kosong = getData.data
        this.disabledKosong = this.kosong.length == 0
        this.total_kosong = getData.total
        this.current_kosong = getData.current_page
        this.from_kosong = getData.from
        this.to_kosong = getData.to
        if(getData.errors){
          this.$swal({
            icon: 'error',
            title: 'Error',
            text: getData.errors,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          })
        } else {
          this.data_non_anggota = getData
          this.pdModalShow = true
        }
      })
    },
    hideModal(){
      this.pdModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.terdaftar = []
      this.kosong = []
    },
    keluarkan(id){
      this.setAnggota('out', id)
    },
    masukkan(id){
      this.setAnggota('in', id)
    },
    setAnggota(data, id){
      this.isBusyKosong = true
      this.isBusyTerdaftar = true
      this.$http.post('/jam/set-anggota', {
        aksi: data,
        data: 'pd',
        id: id,
        jam_id: this.jam_id,
      }).then(response => {
        this.filter_kosong = ''
        this.filter_terdaftar = ''
        this.getTerdaftar()
        this.getKosong()
      })
    },
    cari_terdaftar: _.debounce(function (e) {
      this.getTerdaftar()
    }, 500),
    cari_kosong: _.debounce(function (e) {
      console.log(e);
      this.getKosong()
    }, 500),
    changeTerdaftar(val){
      this.current_terdaftar = val
      this.getTerdaftar()
    },
    changeKosong(val){
      this.current_kosong = val
      this.getKosong()
    },
    allIn(){
      this.setAnggota('all-in', null);
    },
    allOut(){
      this.setAnggota('all-out', null);
    }
  },
}
</script>