<template>
  <b-modal v-model="modalDetilShow" title="Detil Data Jam" size="lg" @hidden="hideModal" ok-only ok-title="Tutup" ok-variant="secondary">
    <b-table-simple bordered v-if="detil_data">
      <b-tr>
        <b-td>Nama</b-td>
        <b-td colspan="2">{{ detil_data.nama }}</b-td>
      </b-tr>
      <b-tr>
        <b-td rowspan="2">Masuk (Jam 1)</b-td>
        <b-td>Scan Awal</b-td>
        <b-td>{{ detil_data.scan_masuk_start }}</b-td>
      </b-tr>
      <b-tr>
        <b-td>Scan Akhir</b-td>
        <b-td>{{ detil_data.scan_masuk_end }}</b-td>
      </b-tr>
      <b-tr>
        <b-td colspan="2">Waktu Akhir Masuk (Jam 1)</b-td>
        <b-td>{{ detil_data.waktu_akhir_masuk }}</b-td>
      </b-tr>
      <b-tr>
        <b-td rowspan="2">Masuk (Jam 2)</b-td>
        <b-td>Scan Awal</b-td>
        <b-td>{{ detil_data.scan_istirahat_start }}</b-td>
      </b-tr>
      <b-tr>
        <b-td>Scan Akhir</b-td>
        <b-td>{{ detil_data.scan_istirahat_end }}</b-td>
      </b-tr>
      <b-tr>
        <b-td colspan="2">Waktu Akhir Masuk (Jam 2)</b-td>
        <b-td>{{ detil_data.waktu_akhir_istirahat }}</b-td>
      </b-tr>
      <b-tr>
        <b-td rowspan="2">Pulang</b-td>
        <b-td>Scan Awal</b-td>
        <b-td>{{ detil_data.scan_pulang_start }}</b-td>
      </b-tr>
      <b-tr>
        <b-td>Scan Akhir</b-td>
        <b-td>{{ detil_data.scan_pulang_end }}</b-td>
      </b-tr>
      <b-tr v-if="detil_data.data_ptk.length">
        <b-td>PTK</b-td>
        <b-td colspan="2">
          <ul class="pl-1">
            <li v-for="ptk in detil_data.data_ptk">{{ ptk.nama }}</li>
          </ul>
        </b-td>
      </b-tr>
      <b-tr v-if="detil_data.data_pd.length">
        <b-td>Peserta Didik</b-td>
        <b-td colspan="2">
          <ul class="pl-1">
            <li v-for="pd in detil_data.data_pd">{{ pd.nama }}</li>
          </ul>
        </b-td>
      </b-tr>
      <b-tr>
        <b-td>Hari</b-td>
        <b-td colspan="2">
          <ul class="pl-1">
            <li v-for="hari in detil_data.hari">{{ hari.nama }}</li>
          </ul>
        </b-td>
      </b-tr>
    </b-table-simple>
  </b-modal>
</template>

<script>
import { BTableSimple, BTr, BTd } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
export default {
  components: {
    BTableSimple,
    BTr,
    BTd,
  },
  data() {
    return {
      modalDetilShow: false,
      detil_data: null,
    }
  },
  created() {
    eventBus.$on('open-modal-detil-jam', this.handleEvent);
  },
  methods: {
    handleEvent(data){
      this.$http.post('/jam/detil', {
        id: data.id
      }).then(response => {
        this.modalDetilShow = true
        this.detil_data = response.data
      })
    },
    hideModal(){
      this.modalDetilShow = false
      this.resetForm()
    },
    resetForm(){
      this.detil_data = null
    },
  },
}
</script>