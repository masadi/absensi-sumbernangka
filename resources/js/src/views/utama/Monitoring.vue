<template>
  <b-container fluid class="mt-3">
    <b-row class="match-height">
      <b-col cols="12" md="6">
        <b-card>
          <h3 class="card-title text-center mb-0">SCAN MASUK</h3>
          <b-table-simple bordered>
            <b-thead>
              <b-tr>
                <b-th class="text-center">NO</b-th>
                <b-th class="text-center">NAMA</b-th>
                <b-th class="text-center">Kelas/Unit</b-th>
                <b-th class="text-center">Waktu Scan Masuk</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="siswa_masuk.length">
                <b-tr v-for="(masuk, index) in siswa_masuk" :key="masuk.peserta_didik_id" :variant="setVarian(index)">
                  <b-td class="text-center">{{ index + 1 }}</b-td>
                  <b-td>{{ masuk.peserta_didik.nama }}</b-td>
                  <b-td class="text-center">{{ (masuk.peserta_didik.kelas) ? masuk.peserta_didik.kelas.nama : '-' }}</b-td>
                  <b-td class="text-center">{{ masuk.jam_masuk }}</b-td>
                </b-tr>
              </template>
              <template v-else>
                <b-tr>
                  <b-td class="text-center" colspan="4">Tidak ada data untuk ditampilkan</b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
        </b-card>
      </b-col>
      <b-col cols="12" md="6">
        <b-card>
          <h3 class="card-title text-center mb-0">TERLAMBAT SCAN MASUK</h3>
          <b-table-simple bordered>
            <b-thead>
              <b-tr>
                <b-th class="text-center">NO</b-th>
                <b-th class="text-center">NAMA</b-th>
                <b-th class="text-center">Kelas/Unit</b-th>
                <b-th class="text-center">Waktu Scan Masuk</b-th>
                <b-th class="text-center">WAKTU TERLAMBAT (MENIT)</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="siswa_lambat.length">
                <b-tr v-for="(lambat, index) in siswa_lambat" :key="lambat.peserta_didik_id" :variant="setVarian(index)">
                  <b-td class="text-center">{{ index + 1 }}</b-td>
                  <b-td>{{ lambat.peserta_didik.nama }}</b-td>
                  <b-td class="text-center">{{ (lambat.peserta_didik.kelas) ? lambat.peserta_didik.kelas.nama : '-' }}</b-td>
                  <b-td class="text-center">{{ lambat.jam_masuk }}</b-td>
                  <b-td class="text-center">{{ lambat.absen_masuk.terlambat }}</b-td>
                </b-tr>
              </template>
              <template v-else>
                <b-tr>
                  <b-td class="text-center" colspan="5">Tidak ada data untuk ditampilkan</b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
        </b-card>
      </b-col>
    </b-row>
    <b-row class="match-height">
      <b-col cols="12" md="6">
        <b-card>
          <h3 class="card-title text-center mb-0">TIDAK SCAN MASUK</h3>
        </b-card>
      </b-col>
      <b-col cols="12" md="6">
        <b-card>
          <h3 class="card-title text-center mb-0">SCAN PULANG CEPAT</h3>
          <b-table-simple bordered>
            <b-thead>
              <b-tr>
                <b-th class="text-center">NO</b-th>
                <b-th class="text-center">NAMA</b-th>
                <b-th class="text-center">Kelas/Unit</b-th>
                <b-th class="text-center">Keterangan</b-th>
              </b-tr>
            </b-thead>
            <b-tbody>
              <template v-if="pulang_cepat.length">
                <b-tr v-for="(pulang, index) in pulang_cepat" :key="pulang.peserta_didik_id" :variant="setVarian(index)">
                  <b-td class="text-center">{{ index + 1 }}</b-td>
                  <b-td>{{ pulang.peserta_didik.nama }}</b-td>
                  <b-td class="text-center">{{ (pulang.peserta_didik.kelas) ? pulang.peserta_didik.kelas.nama : '-' }}</b-td>
                  <b-td class="text-center">{{ pulang.absen_pulang.keterangan }}</b-td>
                </b-tr>
              </template>
              <template v-else>
                <b-tr>
                  <b-td class="text-center" colspan="4">Tidak ada data untuk ditampilkan</b-td>
                </b-tr>
              </template>
            </b-tbody>
          </b-table-simple>
        </b-card>
      </b-col>
    </b-row>
    <b-toast v-model="visible" :variant="variant" :title="title">
      {{message}}
    </b-toast>
  </b-container>
</template>

<script>
import { BContainer, BRow, BCol, BCard, BToast, BTableSimple, BThead, BTbody, BTr, BTd, BTh } from 'bootstrap-vue'
//import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
export default {
  components: {
    BContainer,
    BRow,
    BCol,
    BCard,
    BToast,
    BTableSimple, BThead, BTbody, BTr, BTd, BTh,
  },
  data(){
    return {
      form_id: '',
      disabled: false,
      visible: false,
      variant: '',
      title: '',
      message: '',
      siswa_masuk: [],
      siswa_lambat: [],
      pulang_cepat: [],
    }
  },
  created() {
    this.loadPostsData()
  },
  mounted () {
    var _this = this
    window.Echo.channel('scan').listen('ScanEvent', (e) => {
      console.log('scan');
      console.log(e);
      let getData = e.message
      if(getData.absen){
        if(getData.absen.absen_masuk){
          if(getData.absen.absen_masuk.terlambat){
            this.siswa_lambat.push(getData.absen)
            this.siswa_lambat.unshift(this.siswa_lambat.pop());
            if(this.siswa_lambat.length === 11){
              this.siswa_lambat = this.siswa_lambat.slice(0, -1); 
            }
          } else {
            this.siswa_masuk.push(getData.absen)
            this.siswa_masuk.unshift(this.siswa_masuk.pop());
            if(this.siswa_masuk.length === 11){
              this.siswa_masuk = this.siswa_masuk.slice(0, -1); 
            }
          }
        }
        if(getData.absen.absen_pulang){
          this.pulang_cepat.push(getData.absen)
          this.pulang_cepat.unshift(this.pulang_cepat.pop());
            if(this.pulang_cepat.length === 11){
              this.pulang_cepat = this.pulang_cepat.slice(0, -1); 
            }
        }
      }
      _this.variant = getData.type
      _this.title = getData.title
      _this.message = getData.message
      _this.visible = true
      _this.sound(getData.mp3)
    });
  },
  methods: {
    sound(mp3){
      new Audio(`/mp3/${mp3}`).play();
    },
    loadPostsData(){
      this.$http.get('/display').then(response => {
        let getData = response.data
        this.siswa_masuk = getData.siswa_masuk
        this.siswa_lambat = getData.siswa_lambat
        this.pulang_cepat = getData.pulang_cepat
      })
    },
    setVarian(index){
      if(index){
        return '';
      } else {
        return 'success';
      }
    }
  },
}
</script>
<style lang="scss">
*{box-sizing:border-box;font-family:consolas;margin:0;padding:0}body{background:#111;justify-content:center;min-height:100vh}.scan{align-items:center;display:flex}.scan{flex-direction:column;position:relative}.scan .qrcode{background:url(/img/scan/QR_Code01.png?dca1d8a3939dc88eb3cd6117ac0beeea);background-size:400px;height:400px;position:relative;width:400px}.scan .qrcode:before{-webkit-animation:animate 4s ease-in-out infinite;animation:animate 4s ease-in-out infinite;background:url(/img/scan/QR_Code02.png?e6d7e7e17ef605c594045b125d891f0d);background-size:400px;content:"";height:100%;left:0;overflow:hidden;position:absolute;top:0;width:100%}@-webkit-keyframes animate{0%,to{height:20px}50%{height:calc(100% - 20px)}}@keyframes animate{0%,to{height:20px}50%{height:calc(100% - 20px)}}.scan .qrcode:after{-webkit-animation:animate_line 4s ease-in-out infinite;animation:animate_line 4s ease-in-out infinite;background:#35fd5c;content:"";filter:drop-shadow(0 0 20px #35fd5c) drop-shadow(0 0 60px #35fd5c);height:2px;inset:20px;position:absolute;width:calc(100% - 40px)}@-webkit-keyframes animate_line{0%,to{top:20px}50%{top:calc(100% - 20px)}}@keyframes animate_line{0%,to{top:20px}50%{top:calc(100% - 20px)}}.border{-webkit-animation:animate_text .5s linear infinite;animation:animate_text .5s linear infinite;background:url(/img/scan/border.png?4cc6dfdd18c8c16bfba4dd88038aa533);background-repeat:no-repeat;background-size:400px;inset:0;position:absolute}@-webkit-keyframes animate_text{0%,to{opacity:0}50%{opacity:1}}@keyframes animate_text{0%,to{opacity:0}50%{opacity:1}}.scan h3{-webkit-animation:animate_text .5s steps(1) infinite;animation:animate_text .5s steps(1) infinite;color:#fff;filter:drop-shadow(0 0 20px #fff) drop-shadow(0 0 60px #fff);font-size:2em;letter-spacing:2px;margin-top:20px;text-transform:uppercase}
.toast-body {font-size: 2rem;}
</style>