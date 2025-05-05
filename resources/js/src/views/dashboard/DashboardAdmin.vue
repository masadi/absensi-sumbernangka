<template>
  <div>
    <statistik-pd data-aos="fade-down" data-aos-duration="1500"></statistik-pd>
    <statistik-ptk data-aos="fade-left" data-aos-duration="1500"></statistik-ptk>
    <b-modal v-model="modalShow" @hidden="resetModal" @ok="handleOk" no-close-on-backdrop hide-header-close title="Generate Scan" ok-title="Lanjutkan" cancel-title="Tutup">
      <b-overlay :show="loading_modal" rounded opacity="0.6" size="lg" spinner-variant="danger">
        <b-form @submit.prevent="handleSubmit">
          <b-row>
            <b-col cols="12">
              <b-form-group label="Tanggal" label-for="tanggal" :invalid-feedback="feedback.tanggal" :state="state.tanggal">
                <b-form-datepicker id="tanggal" v-model="form.tanggal" show-decade-nav button-variant="outline-secondary" left locale="id" @context="onContext" placeholder="Pilih Tanggal" :state="state.tanggal"></b-form-datepicker>
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
          <b-button variant="primary" @click="ok()">Lanjutkan</b-button>
        </b-overlay>
      </template>
    </b-modal>
    <b-modal v-model="generateResult" ok-only ok-title="Tutup" size="lg" no-close-on-backdrop hide-header-close title="Hasil Generate Scan Tanggal 19 Agustus 2023" @ok="reloadStatistik">
      <b-alert show variant="success">
        <div class="alert-body">
          <p><strong>{{ result.baru }}</strong> data PTK berhasil di generate!</p>
          <p><strong>{{ result.baru_pd }}</strong> data PD berhasil di generate!</p>
        </div>
      </b-alert>
      <b-tabs content-class="mt-3">
        <b-tab title="PTK" active>
          <b-table-simple bordered>
            <b-thead>
              <b-tr>
                <b-th class="text-center">No</b-th>
                <b-th class="text-center">Nama</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="result.nama_baru.length">
                <b-tr v-for="(ptk, index) in result.nama_baru" :key="ptk.ptk_id">
                  <b-td class="text-center">{{ index + 1 }}</b-td>
                  <b-td>{{ ptk.nama }}</b-td>
                </b-tr>
              </template>
              <template v-else>
                <b-tr>
                  <b-td class="text-center" colspan="2">Tidak ada data untuk ditampilkan</b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
          <b-alert show variant="danger" class="mt-2">
            <div class="alert-body"><strong>{{ result.ada }}</strong> data PTK telah memiliki riwayat scan!</div>
          </b-alert>
          <b-table-simple bordered>
            <b-thead>
              <b-tr>
                <b-th class="text-center">No</b-th>
                <b-th class="text-center">Nama</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="result.nama_ada.length">
                <b-tr v-for="(ada, index) in result.nama_ada" :key="ada.ptk_id">
                  <b-td class="text-center">{{ index + 1 }}</b-td>
                  <b-td>{{ ada.nama }}</b-td>
                </b-tr>
              </template>
              <template v-else>
                <b-tr>
                  <b-td class="text-center" colspan="2">Tidak ada data untuk ditampilkan</b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
        </b-tab>
        <b-tab title="Peserta Didik">
          <b-table-simple bordered>
            <b-thead>
              <b-tr>
                <b-th class="text-center">No</b-th>
                <b-th class="text-center">Nama</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="result.nama_baru_pd.length">
                <b-tr v-for="(pd, index) in result.nama_baru_pd" :key="pd.peserta_didik_id">
                  <b-td class="text-center">{{ index + 1 }}</b-td>
                  <b-td>{{ pd.nama }}</b-td>
                </b-tr>
              </template>
              <template v-else>
                <b-tr>
                  <b-td class="text-center" colspan="2">Tidak ada data untuk ditampilkan</b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
          <b-alert show variant="danger" class="mt-2">
            <div class="alert-body"><strong>{{ result.ada }}</strong> data Peserta Didik telah memiliki riwayat scan!</div>
          </b-alert>
          <b-table-simple bordered>
            <b-thead>
              <b-tr>
                <b-th class="text-center">No</b-th>
                <b-th class="text-center">Nama</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="result.nama_ada_pd.length">
                <b-tr v-for="(ada_pd, index) in result.nama_ada_pd" :key="ada_pd.peserta_didik_id">
                  <b-td class="text-center">{{ index + 1 }}</b-td>
                  <b-td>{{ ada_pd.nama }}</b-td>
                </b-tr>
              </template>
              <template v-else>
                <b-tr>
                  <b-td class="text-center" colspan="2">Tidak ada data untuk ditampilkan</b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
        </b-tab>
      </b-tabs>
    </b-modal>
  </div>
</template>

<script>
import { BForm, BRow, BCol, BFormGroup, BOverlay, BFormDatepicker, BButton, BTabs, BTab, BTableSimple, BThead, BTbody, BTr, BTh, BTd, BAlert } from 'bootstrap-vue'
import StatistikPd from '@/views/components/StatistikPd.vue'
import StatistikPtk from '@/views/components/StatistikPtk.vue'
import eventBus from '@core/utils/eventBus'
import AOS from 'aos'
import 'aos/dist/aos.css'
export default {
  components: {
    StatistikPd, 
    StatistikPtk,
    BForm, BRow, BCol, BFormGroup, BOverlay, BFormDatepicker, BButton, BTabs, BTab,
    BTableSimple, BThead, BTbody, BTr, BTh, BTd, BAlert,
  },
  mounted() {
    AOS.init()
  },
  data() {
    return {
      modalShow: false,
      loading_modal: false,
      generateResult: false,
      form: {
        tanggal: '',
        semester_id: '',
      },
      state: {
        tanggal: null,
      },
      feedback: {
        tanggal: '',
      },
      data_ptk: [],
      data_pd: [],
      result: {
        baru: 0,
        baru_pd: 0,
        nama_baru: [],
        nama_ada: [],
        nama_baru_pd: [],
        nama_ada_pd: [],
      },
    }
  },
  created() {
    eventBus.$on('generate-scan', this.handleEvent);
    this.form.semester_id = this.user.semester.semester_id
  },
  methods: {
    reloadStatistik(){
      eventBus.$emit('reload-statistik')
    },
    handleEvent(){
      this.modalShow = true
      console.log('handleEvent');
    },
    resetModal(){
      this.form.tanggal = ''
      this.state.tanggal = null
      this.feedback.tanggal = ''
      this.modalShow = false
    },
    handleOk(bvModalEvent) {
      bvModalEvent.preventDefault()
      this.handleSubmit()
    },
    handleResult(){
      this.generateResult = true
    },
    handleSubmit(){
      this.loading_modal= true
      this.$http.post('/dashboard/generate-scan', this.form).then(response => {
        this.loading_modal = false
        let getData = response.data
        console.log(getData);
        this.result.baru = getData.baru
        this.result.baru_pd = getData.baru_pd
        this.result.nama_baru = getData.nama_baru
        this.result.nama_ada = getData.nama_ada
        this.result.nama_baru_pd = getData.nama_baru_pd
        this.result.nama_ada_pd = getData.nama_ada_pd
        if(getData.errors){
          this.state.tanggal = (getData.errors.tanggal) ? false : null
          this.feedback.tanggal = (getData.errors.tanggal) ? getData.errors.tanggal.join(', ') : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
            allowOutsideClick: false,
          }).then(result => {
            this.resetModal()
            this.handleResult()
          })
        }
      });
    },
    onContext(ctx) {
      this.formatted = ctx.selectedFormatted
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>