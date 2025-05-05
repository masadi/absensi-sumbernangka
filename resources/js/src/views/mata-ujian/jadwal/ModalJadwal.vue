<template>
  <div>
    <b-modal v-model="addModalJadwal" :title="title" size="xl" @hidden="hideModal" ok-only ok-title="Tutup" ok-variant="secondary">
      <b-overlay :show="loading_form" rounded opacity="0.6" size="lg" spinner-variant="danger">
        <b-table-simple bordered class="mt-1">
          <b-thead>
            <b-tr>
              <b-th class="text-center">Tanggal</b-th>
              <b-th class="text-center">Ujian Ke</b-th>
              <b-th class="text-center">Kode Mapel</b-th>
              <b-th class="text-center">Mata Pelajaran</b-th>
              <b-th class="text-center">Aksi</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <template v-if="jadwal.length">
              <b-tr v-for="(item, index) in jadwal" :key="jadwal.id">
                <b-td>
                  <b-form-datepicker v-model="item.tanggal" show-decade-nav button-variant="outline-secondary" left locale="id" @context="onContext" placeholder="Pilih Tanggal"></b-form-datepicker>
                </b-td>
                <b-td class="text-center">{{item.jam_ke}}</b-td>
                <b-td class="text-center">{{item.mata_pelajaran.kode}}</b-td>
                <b-td>{{item.mata_pelajaran.nama}}</b-td>
                <b-td class="text-center">
                  <b-button variant="primary" size="sm" @click="updateData(item)">Update</b-button>
                  <b-button variant="danger" size="sm" @click="hapusData('ujian', item.id)">Hapus</b-button>
                </b-td>
              </b-tr>
            </template>
            <template v-for="(item, index) in jumlah_form">
              <b-tr>
                <b-td>
                  <b-form-datepicker v-model="form.tanggal[index]" show-decade-nav button-variant="outline-secondary" left locale="id" @context="onContext" placeholder="Pilih Tanggal Mulai"></b-form-datepicker>
                </b-td>
                <b-td class="text-center">
                  <b-form-input type="number" v-model="form.jam_ke[index]" :state="state.jam_ke[index]"></b-form-input>
                </b-td>
                <b-td class="text-center">
                  <b-form-select v-model="form.kelompok_id[index]" :options="data_kelompok" value-field="id" text-field="nama" @input="changeKelompok(index)"></b-form-select>
                </b-td>
                <b-td>
                  <b-form-select v-model="form.mata_pelajaran_id[index]" :options="data_mata_pelajaran[index]" value-field="id" text-field="nama_mapel"></b-form-select>
                </b-td>
                <b-td class="text-center"></b-td>
              </b-tr>
            </template>
          </b-tbody>
        </b-table-simple>
        <div class="d-flex justify-content-between">
          <h4>Catatan <b-button variant="warning" size="sm" @click="formCatatan" class="float-right">Tambah Catatan</b-button></h4>
          <h4>
            <b-button variant="success" size="sm" @click="simpanForm" class="float-right">Simpan</b-button>
            <b-button variant="info" size="sm" @click="addForm" class="float-right mr-1">Tambah Form</b-button>
          </h4>
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
      <!--template #modal-footer="{ ok, cancel }">
        <b-row class="w-100">
          <b-col>
            <b-button variant="primary" @click="cetak()" v-if="jadwal.length">Cetak Jadwal</b-button>
          </b-col>
          <b-col align="right">
            <b-overlay :show="loading_form" rounded opacity="0.6" spinner-small spinner-variant="secondary" class="d-inline-block">
              <b-button @click="cancel()" class="float-right">Tutup</b-button>
            </b-overlay>
          </b-col>
        </b-row>
      </template-->
    </b-modal>
    <modal-catatan @reload="handleReload"></modal-catatan>
  </div>
</template>

<script>
import { BOverlay, BForm, BFormGroup, BFormInput, BButton, BTableSimple, BThead, BTbody, BTr, BTd, BTh, BRow, BCol, BDropdown, BDropdownItem, BFormSelect, BFormDatepicker, BToast } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import ModalCatatan from './ModalCatatan.vue'
export default {
  components: {
    ModalCatatan,
    BOverlay, 
    BForm, 
    BFormGroup,
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
    BFormSelect,
    BFormDatepicker,
    BToast,
  },
  data() {
    return {
      toastCount: 0,
      addModalJadwal: false,
      loading_form: false,
      loading_mapel: {},
      title: '',
      jadwal: [],
      catatan: [],
      form: {
        id: '',
        tanggal: {},
        jam_ke: {},
        kelompok_id: {},
        mata_pelajaran_id: {},
      },
      feedback: {
        nama: '',
        tanggal: {},
        jam_ke: {},
        kelompok_id: {},
        mata_pelajaran_id: {},
      },
      state: {
        nama: null,
        tanggal: {},
        jam_ke: {},
        kelompok_id: {},
        mata_pelajaran_id: {},
      },
      data_kelompok: [],
      data_mata_pelajaran: {},
      jumlah_form: 1,
    }
  },
  created() {
    eventBus.$on('open-modal-mata-ujian', this.handleEvent); 
  },
  methods: {
    addForm(){
      //this.form.hari_id[this.jumlah_form] = ''
      this.form.tanggal[this.jumlah_form] = ''
      this.form.jam_ke[this.jumlah_form] = ''
      this.form.kelompok_id[this.jumlah_form] = ''
      this.form.mata_pelajaran_id[this.jumlah_form] = ''
      this.jumlah_form = this.jumlah_form + 1
    },
    handleEvent(data){
      console.log(data);
      this.title = `Mata Ujian ${data.data.nama}`
      this.form.id = data.data.id
      this.handleReload()
      this.addModalJadwal = true
      this.resetForm()
    },
    hideModal(){
      this.addModalJadwal = false
      this.resetForm()
    },
    resetForm(){
      this.jadwal = []
      this.catatan = []
      this.jumlah_form = 1
      for (let i = 0; i < this.jumlah_form; i++) {
        //this.form.hari_id[i] = ''
        this.form.tanggal[i] = ''
        this.form.jam_ke[i] = ''
        this.form.kelompok_id[i] = ''
        this.form.mata_pelajaran_id[i] = ''
      }
    },
    cetak(){
      eventBus.$emit('open-modal-ttd', {
        rombel: this.data,
        form: this.form,
      });
    },
    formCatatan(){
      eventBus.$emit('open-modal-catatan', {
        jadwal_id: this.form.id
      });
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
    handleReload(){
      this.loading_form = true
      this.$http.post('/jadwal/mata-ujian', {
        jadwal_id: this.form.id
      }).then(response => {
        this.loading_form = false
        let getData = response.data
        this.jadwal = getData.jadwal_ujian
        this.catatan = getData.data_catatan
        this.data_kelompok = getData.data_kelompok
      }).catch(error => {
        console.log(error);
      })
    },
    editData(data){
      console.log(data);
      eventBus.$emit('open-modal-catatan', data);
    },
    changeKelompok(item){
      this.form.mata_pelajaran_id[item] = ''
      if(this.form.kelompok_id[item]){
        this.loading_form = true
        console.log(this.loading_mapel);
        this.$http.post('/jadwal/get-mapel', {
          kelompok_id: this.form.kelompok_id[item]
        }).then(response => {
          this.loading_form = false
          console.log(this.loading_mapel);
          let getData = response.data
          this.data_mata_pelajaran[item] = getData
        }).catch(error => {
          console.log(error);
        })
      }
    },
    simpanForm(){
      this.loading_form = true
      console.log(this.form);
      this.$http.post('/jadwal/simpan', this.form).then(response => {
        this.loading_form = false
        let getData = response.data
        if(getData.errors){
          this.state.nama = (getData.errors.nama) ? false : null
          this.feedback.nama = (getData.errors.nama) ? getData.errors.nama.join(', ') : ''
          for (let i = 0; i < this.jumlah_form; i++) {
            var a = i + 1;
            /*this.feedback.hari_id = (getData.errors.hari_id) ? getData.errors.hari_id.join(', ') : ''
            this.state.hari_id = (getData.errors.hari_id) ? false : null
            this.feedback.jam_ke = (getData.errors.jam_ke) ? getData.errors.jam_ke.join(', ') : ''
            this.state.jam_ke = (getData.errors.jam_ke) ? false : null
            this.feedback.kelompok_id = (getData.errors.kelompok_id) ? getData.errors.kelompok_id.join(', ') : ''
            this.state.kelompok_id = (getData.errors.kelompok_id) ? false : null
            this.feedback.mata_pelajaran_id = (getData.errors.mata_pelajaran_id) ? getData.errors.mata_pelajaran_id.join(', ') : ''
            this.state.mata_pelajaran_id = (getData.errors.mata_pelajaran_id) ? false : null*/
            console.log(getData.errors);
          }
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            html: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
            allowOutsideClick: false,
          }).then(result => {
            this.resetForm()
            this.handleReload()
          })
        }
      }).catch(error => {
        console.log(error);
      })
    },
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
    updateData(item){
      /*
      toastShow: false,
      toastCariant: 'success',
      toastTitle: 'Berhasil',
      toastMessage: 'Jadwal Ujian berhasil diperbaharui',
      
      */
      this.$http.post('/jadwal/update', item).then(response => {
        let getData = response.data
        this.toastCount++
        this.$bvToast.toast(getData.text, {
          title: getData.title,
          autoHideDelay: 5000,
          appendToast: true,
          toaster: 'b-toaster-top-left',
          variant: getData.variant,
        })
      });
    },    
  }
}
</script>