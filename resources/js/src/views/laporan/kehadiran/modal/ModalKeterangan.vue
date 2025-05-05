<template>
  <b-modal v-model="keteranganModalShow" title="Input Keterangan" size="xl" @ok="handleOk">
    <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
      <b-table-simple bordered>
        <b-thead>
          <b-tr>
            <b-th class="text-center" colspan="2">Presensi {{ detil.nama }} tanggal {{ detil.tanggal_scan_str }}</b-th>
          </b-tr>
        </b-thead>
        <b-tbody>
          <b-tr>
            <b-td width="30%">Scan Masuk</b-td>
            <b-td width="70%">{{ detil.jam_masuk }}</b-td>
          </b-tr>
          <b-tr>
            <b-td>Scan Pulang</b-td>
            <b-td>{{ detil.jam_pulang }}</b-td>
          </b-tr>
          <b-tr>
            <b-td>Keterangan Masuk</b-td>
            <b-td>{{ detil.keterangan_masuk }}</b-td>
          </b-tr>
          <b-tr>
            <b-td>Berkas Masuk</b-td>
            <b-td>
              <b-link :href="`/storage/berkas/${detil.berkas_masuk}`" text-varian="warning" target="_blank" v-if="detil.berkas_masuk"><search-icon /></b-link>
            </b-td>
          </b-tr>
          <b-tr v-if="cek_pulang">
            <b-td>Keterangan Pulang</b-td>
            <b-td>{{ detil.keterangan_pulang }}</b-td>
          </b-tr>
          <b-tr v-if="cek_pulang">
            <b-td>Berkas Pulang</b-td>
            <b-td>
              <b-link :href="`/storage/berkas/${detil.berkas_pulang}`" text-varian="warning" target="_blank" v-if="detil.berkas_pulang"><search-icon /></b-link>
            </b-td>
          </b-tr>
        </b-tbody>
      </b-table-simple>
      <b-form @submit.prevent="handleSubmit">
        <b-row>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Keterangan Masuk" label-for="keterangan_masuk" :invalid-feedback="feedback.keterangan_masuk" :state="state.keterangan_masuk">
              <b-form-textarea id="keterangan_masuk" v-model="form.keterangan_masuk" placeholder="Keterangan Masuk..." rows="3" max-rows="6"></b-form-textarea>
            </b-form-group>
          </b-col>
          <b-col cols="12">
            <b-form-group label-cols="2" label="Berkas Keterangan Masuk" label-for="berkas_masuk" :invalid-feedback="feedback.berkas_masuk" :state="state.berkas_masuk">
              <b-form-file id="berkas_masuk" v-model="form.berkas_masuk" :state="state.berkas_masuk" placeholder="Pilih berkas......" drop-placeholder="Drop file here..."></b-form-file>
            </b-form-group>
          </b-col>
          <b-col cols="12" v-if="cek_pulang">
            <b-form-group label-cols="2" label="Keterangan Pulang" label-for="keterangan_pulang" :invalid-feedback="feedback.keterangan_pulang" :state="state.keterangan_pulang">
              <b-form-textarea id="keterangan_pulang" v-model="form.keterangan_pulang" placeholder="Keterangan Masuk..." rows="3" max-rows="6"></b-form-textarea>
            </b-form-group>
          </b-col>
          <b-col cols="12" v-if="cek_pulang">
            <b-form-group label-cols="2" label="Berkas Keterangan Pulang" label-for="berkas_pulang" :invalid-feedback="feedback.berkas_pulang" :state="state.berkas_pulang">
              <b-form-file id="berkas_pulang" v-model="form.berkas_pulang" :state="state.berkas_pulang" placeholder="Pilih berkas......" drop-placeholder="Drop file here..."></b-form-file>
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
import { BForm, BRow, BCol, BFormGroup, BFormTextarea, BOverlay, BButton, BTableSimple, BThead, BTbody, BTr, BTh, BTd, BFormFile, BLink} from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BForm, BRow, BCol, BFormGroup, BFormTextarea, BOverlay, BButton, BTableSimple, BThead, BTbody, BTr, BTh, BTd, BFormFile, BLink
  },
  data() {
    return {
      cek_pulang: false,
      keteranganModalShow: false,
      loading_modal: false,
      loading_tingkat: false,
      form: {
        id: '',
        keterangan_masuk: '',
        berkas_masuk: null,
        keterangan_pulang: '',
        berkas_pulang: null,
      },
      feedback: {
        keterangan_masuk: '',
        berkas_masuk: '',
        keterangan_pulang: '',
        berkas_pulang: '',
      },
      state: {
        keterangan_masuk: null,
        berkas_masuk: null,
        keterangan_pulang: null,
        berkas_pulang: null,
      },
      detil: {
        nama: '',
        tanggal_scan_str: '',
        jam_masuk: '-',
        jam_pulang: '-',
        keterangan_masuk: '-',
        keterangan_pulang: '-',
        berkas_masuk: null,
        berkas_pulang: null,
      }
    }
  },
  created() {
    eventBus.$on('open-modal-keterangan-laporan', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.form.id = data.id
      if(data.ptk_id){
        this.detil.nama = data.ptk.nama
      } else {
        this.detil.nama = data.pd.nama
      }
      this.detil.tanggal_scan_str = data.tanggal_scan_str
      if(data.absen_masuk){
        this.detil.jam_masuk = data.jam_masuk
        this.detil.keterangan_masuk = data.absen_masuk.keterangan
        this.form.keterangan_masuk = data.absen_masuk.keterangan
        this.detil.berkas_masuk = data.absen_masuk.dokumen
      }
      if(data.absen_pulang){
        this.cek_pulang = true
        this.detil.jam_pulang = data.jam_pulang
        this.detil.keterangan_pulang = data.absen_pulang.keterangan
        this.form.keterangan_pulang = data.absen_pulang.keterangan
        this.detil.berkas_pulang = data.absen_pulang.dokumen
      }
      this.keteranganModalShow = true
    },
    hideModal(){
      this.keteranganModalShow = false
      this.form.keterangan_masuk = ''
      this.form.berkas_masuk = null
      this.form.keterangan_pulang = ''
      this.form.berkas_pulang = null
      this.feedback.keterangan_masuk = ''
      this.feedback.berkas_masuk = ''
      this.feedback.keterangan_pulang = ''
      this.feedback.berkas_pulang = ''
      this.state.keterangan_masuk = null
      this.state.berkas_masuk = null
      this.state.keterangan_pulang = null
      this.state.berkas_pulang = null
      this.cek_pulang = false
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleSubmit() {
      this.loading_modal = true
      const data = new FormData();
      data.append('id', (this.form.id) ? this.form.id : '');
      data.append('keterangan_masuk', (this.form.keterangan_masuk) ? this.form.keterangan_masuk : '');
      data.append('berkas_masuk', (this.form.berkas_masuk) ? this.form.berkas_masuk : '');
      data.append('keterangan_pulang', (this.form.keterangan_pulang) ? this.form.keterangan_pulang : '');
      data.append('berkas_pulang', (this.form.berkas_pulang) ? this.form.berkas_pulang : '');
      this.$http.post('/laporan/update-keterangan', data).then(response => {
        let data = response.data
        this.loading_modal = false
        if(data.errors){
          this.state.keterangan_masuk = (data.errors.keterangan_masuk) ? false : null
          this.state.berkas_masuk = (data.errors.berkas_masuk) ? false : null
          this.state.keterangan_pulang = (data.errors.keterangan_pulang) ? false : null
          this.state.berkas_pulang = (data.errors.berkas_pulang) ? false : null
          this.feedback.keterangan_masuk = (data.errors.keterangan_masuk) ? data.errors.keterangan_masuk.join(', ') : ''
          this.feedback.berkas_masuk = (data.errors.berkas_masuk) ? data.errors.berkas_masuk.join(', ') : ''
          this.feedback.keterangan_pulang = (data.errors.keterangan_pulang) ? data.errors.keterangan_pulang.join(', ') : ''
          this.feedback.berkas_pulang = (data.errors.berkas_pulang) ? data.errors.berkas_pulang.join(', ') : ''
        } else {
          this.$swal({
            icon: data.icon,
            title: data.title,
            text: data.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.$emit('reload')
            this.hideModal()
          })
        }
      })
    },
  },
}
</script>