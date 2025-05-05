<template>
  <div>
    <b-card>
      <div class="d-flex justify-content-between">
        <div v-html="judul"></div>
        <div>
          <countdown :time="sisa_waktu * 1000" @end="handleEnd" :transform="transformSlotProps">
            <template slot-scope="props">
              <h2>Sisa Waktu: {{ props.hours }}:{{ props.minutes }}:{{ props.seconds }}</h2>
            </template>
          </countdown>
        </div>
      </div>
    </b-card>
    <b-row>
      <b-col cols="12" md="4">
        <b-container fluid>
          <b-card header-tag="header" footer-tag="footer">
            <template #header>
              <h4 class="mb-0">Pilih Jawaban Soal Nomor {{ nomor }}</h4>
            </template>
            <b-card-text>
              <template v-if="loading">
                <div class="text-center text-danger my-2">
                  <b-spinner class="align-middle"></b-spinner>
                  <strong>Loading...</strong>
                </div>
              </template>
              <template v-else>
                <b-form-group v-slot="{ ariaDescribedby }" class="mt-2">
                  <template v-for="(jawaban, index) in opsi_pg[jumlah_pg]">
                    <b-form-radio v-model="selected" :aria-describedby="ariaDescribedby" name="some-radios" :value="jawaban.opsi" class="mb-1"><h4>{{ jawaban.opsi }}</h4></b-form-radio>
                  </template>
                </b-form-group>
              </template>
            </b-card-text>
            <template #footer>
              <div class="d-flex justify-content-between">
                <b-button @click="lanjut(soal_ujian_id, nomor, 'prev')" variant="primary" :disabled="disabledStart">&laquo;</b-button>
                <b-form-checkbox v-model="ragu" name="check-button" button :button-variant="button_ragu" @change="getRagu">
                  RAGU
                </b-form-checkbox>
                <b-button v-if="nomor == jumlah_soal" variant="danger" @click="selesai(soal_ujian_id, nomor)">Selesai</b-button>
                <b-button v-else @click="lanjut(soal_ujian_id, nomor, 'next')" variant="primary">&raquo;</b-button>  
              </div>
            </template>
          </b-card>
        </b-container>
      </b-col>
      <b-col cols="12" md="8">
        <b-container fluid>
          <b-card no-body>
            <b-card-header class="text-center">
              <h4 class="mb-0 mx-auto w-100">Navigasi Soal</h4>
            </b-card-header>
            <b-card-text>
              <template v-for="chunk in chunked()">
                <b-row class="mb-1">
                  <template v-for="(item, index) in chunk">
                    <template v-if="!index">
                      <b-col cols="2" offset="1">
                        <b-button class="text-nowrap" block :variant="nav_variant[item.nomor_jawaban]" @click="lanjutNav(item.nomor_jawaban)">{{item.nomor_jawaban}}</b-button>
                      </b-col>
                    </template>
                    <template v-else>
                      <b-col cols="2">
                        <b-button class="text-nowrap" block :variant="nav_variant[item.nomor_jawaban]" @click="lanjutNav(item.nomor_jawaban)">{{item.nomor_jawaban}}</b-button>
                      </b-col>
                    </template>
                  </template>
                </b-row>
              </template>
            </b-card-text>
            <template #footer>
              <h2>Keterangan:</h2>
              <b-row>
                <b-col>
                  <p><b-button variant="success">x</b-button> Soal Aktif</p>
                  <p><b-button variant="secondary">x</b-button> Belum Terjawab</p>
                  <p><b-button variant="primary">x</b-button> Terjawab</p>
                  <p><b-button variant="warning">x</b-button> Terjawab Ragu</p>
                </b-col>
              </b-row>
            </template>
          </b-card>
        </b-container>
      </b-col>
    </b-row>
  </div>
</template>

<script>
//import Countdown from './Countdown.vue'
import VueCountdown from '@chenfengyuan/vue-countdown';
import { BRow, BCol, BCard, BCardHeader, BCardBody, BCardText, BContainer, BNavbar, BButton, BFormGroup, BFormRadio, BFormCheckbox, BSpinner } from 'bootstrap-vue'
import _ from 'lodash'
export default {
  components: {
    BRow,
    BCol,
    BCard,
    BCardHeader, 
    BCardBody,
    BCardText,
    BContainer,
    BNavbar,
    BButton, 
    BFormGroup, 
    BFormRadio, 
    BFormCheckbox,
    BSpinner,
    'countdown': VueCountdown,
  },
  data() {
    return {
      judul: '',
      loading: true,
      selected: '',
      ragu: false,
      jumlah_pg: 0,
      jumlah_soal: 0,
      anggota_rombel_id: '',
      soal_ujian_id: '',
      nomor: 0,
      old_nomor: 0,
      jawaban_soal_id: '',
      opsi_pg: {
        0: [],
        3: [
          {opsi: 'A'},
          {opsi: 'B'},
          {opsi: 'C'},
        ],
        4: [
          {opsi: 'A'},
          {opsi: 'B'},
          {opsi: 'C'},
          {opsi: 'D'},
        ],
        5: [
          {opsi: 'A'},
          {opsi: 'B'},
          {opsi: 'C'},
          {opsi: 'D'},
          {opsi: 'E'},
        ],
      },
      disabledStart: true,
      disabledEnd: false,
      button_ragu: 'secondary',
      nav_variant: {},
      all_soal: [],
      waktu: '0',
      sisa_waktu: '120',
    }
  },
  created() {
    this.anggota_rombel_id = this.$route.params.anggota_rombel_id
    this.soal_ujian_id = this.$route.params.soal_ujian_id
    this.nomor = this.$route.query.nomor
    this.getwaktu(this.soal_ujian_id)
    this.getSoal(this.soal_ujian_id, this.nomor, false)
  },
  methods: {
    getwaktu(soal_ujian_id){
      this.$http.post('/ujian/waktu', {
        soal_ujian_id: soal_ujian_id,
      }).then(response => {
        let getData = response.data
        if(getData){
          var today = new Date();
          this.waktu = this.addSecondsToDate(today, getData.waktu)
          this.sisa_waktu = getData.waktu
          //var d = this.addSecondsToDate(today, getData.waktu)
          //this.waktu = this.setDate(d)
        }
      })
    },
    addMinutes(date, minutes) {
      return new Date(date.getTime() + minutes*600);
    },
    addMinutesToDate(date, n){
      const d = new Date(date);
      d.setTime(d.getTime() + n * 60_000);
      return d;
    },
    addSecondsToDate(date, n){
      const d = new Date(date);
      d.setTime(d.getTime() + n * 1000);
      return d;
    },
    setDate(d){
      let h = d.getHours();
      let i = d.getMinutes();
      let s = d.getSeconds();
      var  month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

        if (month.length < 2) 
            month = '0' + month;
        if (day.length < 2) 
            day = '0' + day;

        return [year, month, day].join('-')+' '+h+':'+i+':'+s;
    },
    getSoal(soal_ujian_id, nomor, replace){
      this.nomor = nomor
      this.disabledStart = true
      this.disabledEnd = false
      if(this.nomor > 1){
        this.disabledStart = false
      }
      this.selected = ''
      this.ragu = false
      this.button_ragu = 'secondary'
      this.loading = true
      this.$http.post('/ujian/soal', {
        soal_ujian_id: soal_ujian_id,
        nomor_jawaban: nomor,
      }).then(response => {
        let getData = response.data.soal
        this.judul = `<strong>${getData.soal_ujian.jadwal_ujian.jadwal.nama}<br>Mata Pelajaran: ${getData.soal_ujian.jadwal_ujian.mata_pelajaran.nama}<br>Nama Peserta Ujian: ${this.user.name}</strong>`
        this.jumlah_pg = getData.soal_ujian.jumlah_pg
        this.jumlah_soal = getData.soal_ujian.jumlah_soal
        this.jawaban_soal_id = getData.id
        if(!this.sisa_waktu){
          this.sisa_waktu = getData.soal_ujian.waktu_ujian
        }
        if(getData.jawaban_pd){
          this.selected = getData.jawaban_pd.jawaban
          this.ragu = (getData.jawaban_pd.is_ragu) ? true : false
          this.button_ragu = (getData.jawaban_pd.is_ragu) ? 'warning' : 'secondary'
        }
        if(getData.soal_ujian.jumlah_soal === this.nomor){
          this.disabledEnd = true
        }
        this.all_soal = response.data.all_soal
        this.all_soal.forEach((value, index) => {
          if(value.jawaban_pd && value.jawaban_pd.jawaban){
            if(parseInt(nomor) === value.jawaban_pd.nomor_jawaban){
              this.nav_variant[value.nomor_jawaban] = 'success'
            } else {
              if(value.jawaban_pd.is_ragu){
                this.nav_variant[value.nomor_jawaban] = 'warning'
              } else {
                this.nav_variant[value.nomor_jawaban] = 'primary'
              }
            }
          } else {
            if(parseInt(nomor) === value.nomor_jawaban){
              this.nav_variant[value.nomor_jawaban] = 'success'
            } else {
              this.nav_variant[value.nomor_jawaban] = 'secondary'
            }
          }
        })
        this.loading = false
        if(replace && parseInt(this.old_nomor) != parseInt(nomor)){
          this.$router.replace({ name: "proses-ujian", params: {soal_ujian_id:this.$route.params.soal_ujian_id}, query: {nomor: nomor} })
        }
      })
    },
    lanjut(soal_ujian_id, nomor, nav){
      this.loading = true
      var nomor_ujian;
      if(nav == 'prev'){
        nomor_ujian = parseInt(nomor) - 1;
      } else {
        nomor_ujian = parseInt(nomor) + 1;
      }
      this.simpanUjian(soal_ujian_id, nomor, nomor_ujian);
    },
    lanjutNav(nomor_ujian){
      this.simpanUjian(this.soal_ujian_id, this.nomor, nomor_ujian);
    },
    simpanUjian(soal_ujian_id, old_nomor, new_nomor){
      this.old_nomor = old_nomor
      this.$http.post('/ujian/simpan', {
        jawaban_soal_id: this.jawaban_soal_id,
        anggota_rombel_id: this.anggota_rombel_id,
        soal_ujian_id: soal_ujian_id,
        nomor_jawaban: old_nomor,
        jawaban: this.selected,
        ragu: this.ragu,
        waktu: this.waktu,
      }).then(response => {
        this.getSoal(soal_ujian_id, new_nomor, true)
      })
    },
    getRagu(val){
      if(val){
        this.button_ragu = 'warning'
      } else {
        this.button_ragu = 'secondary'
      }
    },
    chunked() {
      return _.chunk(this.all_soal, 5)
    },
    selesai(soal_ujian_id, nomor){
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: 'Aksi ini akan mengakhiri sesi ujian',
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
          this.$http.post('/ujian/selesai', {
            jawaban_soal_id: this.jawaban_soal_id,
            anggota_rombel_id: this.anggota_rombel_id,
            soal_ujian_id: soal_ujian_id,
            nomor_jawaban: nomor,
            jawaban: this.selected,
            ragu: this.ragu,
            waktu: this.waktu,
          }).then(response => {
            let data = response.data
            this.$swal({
              icon: data.icon,
              title: data.title,
              text: data.text,
              customClass: {
                confirmButton: 'btn btn-success',
              },
            }).then(result => {
              this.$router.replace({ name: "dashboard"})
            })
          });
        }
      })
    },
    waktuHabis(soal_ujian_id, nomor){
      this.$swal({
        title: 'Waktu Habis',
        text: 'Anda tidak dapat melanjutkan ujian ini. Silahkan hubungi Administrator',
        icon: 'warning',
        confirmButtonText: 'Tutup',
        customClass: {
          confirmButton: 'btn btn-primary',
        },
        buttonsStyling: false,
        allowOutsideClick: () => false,
      }).then(result => {
        if (result.value) {
          this.$http.post('/ujian/selesai', {
            jawaban_soal_id: this.jawaban_soal_id,
            anggota_rombel_id: this.anggota_rombel_id,
            soal_ujian_id: soal_ujian_id,
            nomor_jawaban: nomor,
            jawaban: this.selected,
            ragu: this.ragu,
            waktu: this.waktu,
          }).then(response => {
            this.$router.replace({ name: "dashboard"})
          });
        }
      })
    },
    handleEnd(){
      this.waktuHabis(this.soal_ujian_id, this.nomor)
    },
    transformSlotProps(props) {
      const formattedProps = {};

      Object.entries(props).forEach(([key, value]) => {
        formattedProps[key] = value < 10 ? `0${value}` : String(value);
      });

      return formattedProps;
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>