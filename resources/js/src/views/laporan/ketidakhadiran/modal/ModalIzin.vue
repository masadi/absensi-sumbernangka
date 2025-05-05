<template>
  <b-modal v-model="izinModalShow" :title="`Input ${title}`" size="lg" @ok="handleOk">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-form @submit.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label="Nama" label-for="nama">
              <b-form-input v-model="form.nama" placeholder="Nama" disabled></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-table-simple bordered>
              <b-thead>
                <b-tr>
                  <b-th class="text-center">No</b-th>
                  <b-th class="text-center">Tanggal {{ title }}</b-th>
                  <b-th class="text-center">Alasan</b-th>
                  <b-th class="text-center">Berkas</b-th>
                  <b-th class="text-center">Aksi</b-th>
                </b-tr>
              </b-thead>
              <b-tbody>
                <template v-if="data.length">
                  <b-tr v-for="(izin, index) in data" :key="izin.id">
                    <b-td class="text-center">{{ index + 1 }}</b-td>
                    <b-td class="text-center">{{ tanggal }}</b-td>
                    <b-td>{{ izin.alasan }}</b-td>
                    <b-td class="text-center">
                      <template v-if="izin.berkas">
                        <b-link :href="`/storage/berkas/${izin.berkas}`" target="_blank"><search-icon /></b-link>
                      </template>
                    </b-td>
                    <b-td class="text-center">
                      <b-button-group size="sm">
                        <b-button variant="warning" @click="editIzin(izin)"><pencil-icon size="16px" /></b-button>
                        <b-button variant="danger"><trash-icon size="16px" @click="hapusIzin(izin)" /></b-button>
                      </b-button-group>
                    </b-td>
                  </b-tr>
                </template>
                <template v-else>
                  <b-tr>
                    <b-td class="text-center" colspan="5">Tidak ada untuk ditampilkan</b-td>
                  </b-tr>
                </template>
              </b-tbody>
            </b-table-simple>
          </b-col>
          <b-col cols="12">
            <b-form-group :label="`Berkas ${title}`" label-for="berkas">
              <b-form-file v-model="form.file" placeholder="Choose a file or drop it here..." drop-placeholder="Drop file here..."></b-form-file>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Keterangan" label-for="keterangan">
              <b-form-input v-model="form.keterangan" placeholder="Keterangan" disabled></b-form-input>
            </b-form-group>
          </b-col>
          <b-col cols="12" v-if="form.keterangan == 'Izin'">
            <b-form-group label="Jenis Izin" label-for="jenis">
              <b-form-select v-model="form.jenis">
                <b-form-select-option :value="null"> == Pilih Jenis Izin ==</b-form-select-option>
                <b-form-select-option value="Pribadi">Pribadi</b-form-select-option>
                <b-form-select-option value="Sekolah">Sekolah (Dinas/Tugas Luar)</b-form-select-option>
              </b-form-select>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label="Nama" label-for="alasan">
              <b-form-textarea id="alasan" v-model="form.alasan" placeholder="Alasan tidak masuk (jika ada)" rows="3" max-rows="6"></b-form-textarea>
            </b-form-group>
          </b-col>
        </b-row>
      </b-form>
    </b-overlay>
    <template #modal-footer="{ ok, cancel }">
      <b-overlay :show="loading_modal" rounded opacity="0.6" size="sm" spinner-variant="secondary">
        <b-button @click="cancel()">Tutup</b-button>
      </b-overlay>
      <b-overlay :show="loading_modal" rounded opacity="0.6" size="sm" spinner-variant="primary">
        <b-button variant="primary" @click="ok()">Simpan</b-button>
      </b-overlay>
    </template>  
  </b-modal>
</template>

<script>
import { BForm, BRow, BCol, BFormGroup, BFormInput, BOverlay, BButton, BFormTextarea, BFormFile, BTableSimple, BThead, BTbody, BTr, BTh, BTd, BFormSelect, BFormSelectOption, BButtonGroup, BLink } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BForm, BRow, BCol, BFormGroup, BFormInput, BOverlay, BButton, BFormTextarea, BFormFile, BTableSimple, BThead, BTbody, BTr, BTh, BTd, BFormSelect, BFormSelectOption, BButtonGroup, BLink
  },
  data() {
    return {
      izinModalShow: false,
      loading_modal: false,
      title: '',
      form: {
        id: '',
        nama: '',
        file: null,
        keterangan: '',
        jenis: null,
        alasan: '',
      },
      tanggal: '',
      data: [],
      absen: null,
    }
  },
  created() {
    eventBus.$on('open-modal-izin', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.absen = data
      this.loading_modal = true
      this.$emit('loading', true)
      this.$http.post('/laporan/tidak-hadir', {
        id: data.item.id,
        ptk_id: data.item.ptk_id,
        peserta_didik_id: data.item.peserta_didik_id,
        keterangan: data.keterangan,
      }).then(response => {
        let getData = response.data
        this.form.id = getData.absen.id
        this.title = getData.keterangan
        if(getData.absen.ptk_id){
          this.form.nama = getData.absen.ptk.nama
          this.data = getData.absen.ptk.izin
        }
        if(getData.absen.peserta_didik_id){
          this.form.nama = getData.absen.pd.nama
          this.data = getData.absen.pd.izin
        }
        this.form.keterangan = getData.keterangan
        this.tanggal = getData.absen.tanggal_scan
        this.loading_modal = false
        this.$emit('loading', false)
        this.izinModalShow = true
      })
    },
    hideModal(){
      this.izinModalShow = false
      this.form.id = ''
      this.form.nama = ''
      this.form.file = null
      this.form.keterangan = ''
      this.form.jenis = null
      this.form.alasan = ''
      this.tanggal = ''
      this.data = []
      this.absen = null
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.loading_modal = true
      const data = new FormData();
      data.append('id', (this.form.id) ? this.form.id : '')
      data.append('berkas', (this.form.file) ? this.form.file : '')
      data.append('keterangan', (this.form.keterangan) ? this.form.keterangan : '')
      data.append('jenis', (this.form.jenis) ? this.form.jenis : '')
      data.append('alasan', (this.form.alasan) ? this.form.alasan : '')
      this.$http.post('/laporan/save-izin', data).then(response => {
        let data = response.data
        this.loading_modal = false
        if(data.errors){
          
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.handleEvent(this.absen)
          })
        }
      })
    },
    editIzin(data){
      this.form.jenis = data.jenis
      this.form.alasan = data.alasan
    },
    hapusIzin(data){
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
          this.$http.post('/laporan/hapus-izin', {
            id: data.id,
            keterangan: data.keterangan,
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
              this.handleEvent(this.absen)
              //this.$emit('reload')
              //this.resetModal()
            })
          });
        }
      })
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>