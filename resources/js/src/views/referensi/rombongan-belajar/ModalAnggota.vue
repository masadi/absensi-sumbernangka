<template>
  <b-modal v-model="anggotaModalShow" title="Anggota Rombel" size="xl" ok-only ok-variant="secondary" ok-title="Tutup" modal-class="modal-fullscreen" scrollable>
    <b-row>
      <b-col cols="6">
        <b-overlay :show="loading_anggota || loading_table" opacity="0.6" size="lg" spinner-variant="success">
          <b-table-simple bordered responsive>
            <b-thead>
              <b-tr>
                <b-th class="text-center">NO</b-th>
                <b-th class="text-center">NAMA</b-th>
                <b-th class="text-center">NISN</b-th>
                <b-th class="text-center">AKSI</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-for="(anggota, index) in data_anggota">
                <b-tr>
                  <b-td class="text-center">{{index + 1}}</b-td>
                  <b-td>{{anggota.nama}}</b-td>
                  <b-td class="text-center">{{anggota.nisn}}</b-td>
                  <b-td class="text-center">
                    <b-button size="sm" variant="danger" @click="keluarkan(anggota.peserta_didik_id)">Keluarkan</b-button>
                  </b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
        </b-overlay>
      </b-col>
      <b-col cols="6">
        <b-row>
          <b-col cols="12" class="mb-2">
            <b-form-input v-model="filter_nama" @input="cari_nama" placeholder="Cari data..."></b-form-input>
          </b-col>
        </b-row>
        <b-overlay :show="loading_non_anggota || loading_table" opacity="0.6" size="lg" spinner-variant="danger">
          <b-table-simple bordered responsive>
            <b-thead>
              <b-tr>
                <b-th class="text-center">NO</b-th>
                <b-th class="text-center">NAMA</b-th>
                <b-th class="text-center">NISN</b-th>
                <b-th class="text-center">AKSI</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-for="(non_anggota, index) in data_non_anggota">
                <b-tr>
                  <b-td class="text-center">{{index + 1}}</b-td>
                  <b-td>{{non_anggota.nama}}</b-td>
                  <b-td class="text-center">{{non_anggota.nisn}}</b-td>
                  <b-td class="text-center">
                    <b-button size="sm" variant="success" @click="masukkan(non_anggota.peserta_didik_id)">Masukkan</b-button>
                  </b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
        </b-overlay>
      </b-col>
    </b-row>
  </b-modal>
</template>

<script>
import { BRow, BCol, BTableSimple, BThead, BTh, BTbody, BTr, BTd, BButton, BOverlay, BFormInput } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BRow,
    BCol,
    BTableSimple,
    BThead,
    BTh,
    BTbody,
    BTr,
    BTd,
    BButton,
    BOverlay,
    BFormInput,
  },
  data() {
    return {
      anggotaModalShow: false,
      loading_anggota: false,
      loading_non_anggota: false,
      loading_table: false,
      data_anggota: [],
      data_non_anggota: [],
      rombongan_belajar_id: '',
      filter_nama: '',
    }
  },
  created() {
    eventBus.$on('open-modal-anggota-rombel', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.rombongan_belajar_id = data.rombongan_belajar_id
      this.getAnggota()
      this.getNonAnggota()
    },
    getAnggota(){
      this.loading_anggota = true
      this.$http.post('/referensi/anggota-rombel', {
        rombongan_belajar_id: this.rombongan_belajar_id,
      }).then(response => {
        this.loading_anggota = false
        var getData = response.data
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
    getNonAnggota(){
      console.log(this.user);
      this.loading_non_anggota = true
      this.$http.post('/referensi/non-anggota', {
        rombongan_belajar_id: this.rombongan_belajar_id,
        filter_nama: this.filter_nama,
        semester_id: this.user.semester.semester_id,
      }).then(response => {
        this.loading_non_anggota = false
        var getData = response.data
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
          this.anggotaModalShow = true
        }
      })
    },
    hideModal(){
      this.anggotaModalShow = false
      this.resetForm()
    },
    resetForm(){
      this.data_anggota = []
      this.data_non_anggota = []
    },
    keluarkan(peserta_didik_id){
      this.setAnggota('keluarkan', peserta_didik_id)
    },
    masukkan(peserta_didik_id){
      this.setAnggota('masukkan', peserta_didik_id)
    },
    setAnggota(data, peserta_didik_id){
      this.loading_table = true
      this.$http.post('/referensi/set-anggota', {
        data: data,
        peserta_didik_id: peserta_didik_id,
        rombongan_belajar_id: this.rombongan_belajar_id,
        semester_id: this.user.semester.semester_id,
      }).then(response => {
        this.loading_table = false
        this.getAnggota()
        this.getNonAnggota()
      })
    },
    cari_nama: _.debounce(function (e) {
      console.log(e);
      this.getNonAnggota()
    }, 500),
  },
}
</script>
<style lang="scss">
.modal-fullscreen .modal-dialog {
    max-width: 100%;
    margin: 0;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    height: 100vh;
    display: flex;
    position: fixed;
    z-index: 100000;
}
</style>