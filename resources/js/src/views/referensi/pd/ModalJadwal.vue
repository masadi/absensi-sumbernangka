<template>
  <div>
    <b-modal v-model="addModalJadwal" :title="title" size="xl" @hidden="hideModal">
      <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
        <b-table-simple bordered>
          <b-thead>
            <b-tr>
              <b-th class="text-center">No</b-th>
              <b-th class="text-center">Hari</b-th>
              <b-th class="text-center">Ujian Ke</b-th>
              <b-th class="text-center">Kode Mapel</b-th>
              <b-th class="text-center">Mata Pelajaran</b-th>
              <b-th class="text-center">Aksi</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <template v-if="jadwal.length">
              <b-tr v-for="(item, index) in jadwal" :key="jadwal.id">
                <b-td class="text-center">{{index + 1}}</b-td>
                <b-td>{{ item.hari.nama }}</b-td>
                <b-td class="text-center">{{item.jam_ke}}</b-td>
                <b-td class="text-center">{{item.mata_pelajaran.kode}}</b-td>
                <b-td>{{item.mata_pelajaran.nama}}</b-td>
                <b-td class="text-center">
                  <b-button variant="danger" size="sm" @click="hapusData('jadwal', item.id)">Hapus</b-button>
                </b-td>
              </b-tr>
            </template>
            <template v-else>
              <b-tr>
                <b-td class="text-center" colspan="6">Tidak ada data untuk ditampilkan</b-td>
              </b-tr>
            </template>
          </b-tbody>
        </b-table-simple>
        <div class="d-flex justify-content-between">
          <h4>Catatan <b-button variant="warning" size="sm" @click="formCatatan" class="float-right">Tambah Catatan</b-button></h4>
        </div>
        <b-table-simple bordered>
          <b-thead>
            <b-tr>
              <b-th class="text-center">No</b-th>
              <b-th class="text-center">Isi Catatan</b-th>
              <b-th class="text-center">Aksi</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <template v-if="catatan.length">
              <b-tr v-for="(item, index) in catatan" :key="catatan.id">
                <b-td class="text-center">{{index + 1}}</b-td>
                <b-td>{{ item.catatan }}</b-td>
                <b-td class="text-center">
                  <b-dropdown id="dropdown-dropleft" dropleft text="Detil" variant="primary" size="sm">
                    <b-dropdown-item href="javascript:void(0)" @click="editData(item)"><pencil-icon /> Edit</b-dropdown-item>
                    <b-dropdown-item href="javascript:void(0)" @click="hapusData('catatan', item.id)"><trash-icon /> Hapus</b-dropdown-item>
                  </b-dropdown>
                </b-td>
              </b-tr>
            </template>
            <template v-else>
              <b-tr>
                <b-td class="text-center" colspan="3">Tidak ada data untuk ditampilkan</b-td>
              </b-tr>
            </template>
          </b-tbody>
        </b-table-simple>
      </b-overlay>
      <template #modal-footer="{ ok, cancel }">
        <b-row class="w-100">
          <b-col>
            <b-button variant="primary" @click="cetak()" v-if="jadwal.length">Cetak Jadwal</b-button>
          </b-col>
          <b-col align="right">
            <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
              <b-button @click="cancel()" class="float-right">Tutup</b-button>
            </b-overlay>
            <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="success" class="d-inline-block">
              <b-button variant="success" @click="tambahJadwal" class="float-right">Tambah Jadwal</b-button>
            </b-overlay>
          </b-col>
        </b-row>
      </template>
    </b-modal>
    <modal-add-jadwal @reload="handleReload"></modal-add-jadwal>
    <modal-catatan @reload="handleReload"></modal-catatan>
  </div>
</template>

<script>
import { BOverlay, BForm, BFormInput, BButton, BTableSimple, BThead, BTbody, BTr, BTd, BTh, BRow, BCol, BDropdown, BDropdownItem } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import ModalAddJadwal from './ModalAddJadwal.vue'
import ModalCatatan from './ModalCatatan.vue'
export default {
  components: {
    ModalAddJadwal,
    ModalCatatan,
    BOverlay, 
    BForm, 
    BFormInput, 
    BButton,
    BTableSimple, 
    BThead, 
    BTbody, 
    BTr, 
    BTd, 
    BTh,
    BRow, 
    BCol,
    BDropdown, 
    BDropdownItem,
  },
  data() {
    return {
      addModalJadwal: false,
      loading_form: false,
      title: '',
      jadwal: [],
      catatan: [],
      data: '',
      aksi: '',
      rombongan_belajar_id: '',
    }
  },
  created() {
    eventBus.$on('open-modal-jadwal-rombel', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.data = data
      this.title = `Jadwal ${data.aksi.toUpperCase()} Kelas ${data.nama}`
      this.aksi = data.aksi
      this.rombongan_belajar_id = data.rombongan_belajar_id
      this.handleReload()
      this.addModalJadwal = true
    },
    hideModal(){
      this.addModalJadwal = false
      this.resetForm()
    },
    resetForm(){
      this.jadwal = []
      this.catatan = []
    },
    
    cetak(){
      //
    },
    formCatatan(){
      eventBus.$emit('open-modal-catatan', this.data);
    },
    hapusData(aksi, id){
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
              aksi: aksi,
              id: id,
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
                this.handleReload()
              })
            });
          }
        })
    },
    tambahJadwal(){
      eventBus.$emit('open-modal-form-jadwal', this.data);
    },
    handleReload(){
      this.loading_form = true
      this.$http.post('/jadwal', {
        jenis_jadwal: this.aksi,
        rombongan_belajar_id: this.rombongan_belajar_id,
      }).then(response => {
        this.loading_form = false
        let getData = response.data
        this.jadwal = getData.jadwal_ujian
        this.catatan = getData.data_catatan
      }).catch(error => {
        console.log(error);
      })
    },
    editData(data){
      eventBus.$emit('open-modal-catatan', {
        jenis_jadwal: this.data.aksi,
        rombongan_belajar_id: this.data.rombongan_belajar_id,
        id: data.id,
        catatan: data.catatan,
      });
    }
  },
}
</script>