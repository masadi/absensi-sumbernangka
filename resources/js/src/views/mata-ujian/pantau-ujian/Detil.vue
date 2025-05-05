<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>{{ title }}</b-card-title>
    </b-card-header>
    <b-card-body>
      <div v-if="isBusy" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <b-table-simple bordered responsive>
          <b-thead>
            <b-tr>
              <b-th class="text-center">No</b-th>
              <b-th class="text-center">Nama Peserta Didik</b-th>
              <b-th class="text-center">NISN</b-th>
              <b-th class="text-center">Status Ujian</b-th>
              <b-th class="text-center">Reset Ujian</b-th>
              <b-th class="text-center">Force Selesai</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <template v-if="items.length">
              <b-tr v-for="(item, index) in items" :key="index">
                <b-td class="text-center">{{ index + 1 }}</b-td>
                <b-td>{{ item.nama }}</b-td>
                <b-td class="text-center">{{ item.nisn }}</b-td>
                <b-td class="text-center">
                  <b-badge :variant="statusVariant(item.user.ujian_siswa.status).color">{{statusVariant(item.user.ujian_siswa.status).text}}</b-badge>
                </b-td>
                <b-td class="text-center text-nowrap">
                  <b-button size="sm" @click="resetUjian(item.user.id)" variant="warning" :disabled="!isDisabled(item.user.ujian_siswa.status)">Reset Ujian</b-button>
                </b-td>
                <b-td class="text-center text-nowrap">
                  <b-button size="sm" @click="forceSelesai(item.user.id)" variant="danger" :disabled="isDisabled(item.user.ujian_siswa.status)">Force Selesai</b-button>
                </b-td>
              </b-tr>
            </template>
            <template v-else>
              <b-tr>
                <b-td class="text-center" colspan="5">Tidak ada data untuk ditampilkan</b-td>
              </b-tr>
            </template>
          </b-tbody>
        </b-table-simple>
      </div>
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody, BSpinner, BTableSimple, BTbody, BThead, BTr, BTd, BTh, BButton, BBadge } from 'bootstrap-vue'

export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BSpinner,
    BTableSimple,
    BTbody,
    BThead,
    BTr,
    BTd,
    BTh,
    BButton,
    BBadge,
  },
  data() {
    return {
      title: '',
      isBusy: true,
      items: [],
    }
  },
  created() {
    //console.log(this.$route.params.id);
    this.loadPostsData()
  },
  methods: {
    handleReload(){
      this.loadPostsData()
    },
    loadPostsData() {
      this.$http.post('/pantau-ujian/list', {
        soal_ujian_id: this.$route.params.id
      }).then(response => {
        let getData = response.data
        this.isBusy = false
        this.title = `${getData.ujian.jadwal_ujian.jadwal.nama} - Mata Pelajaran ${getData.ujian.jadwal_ujian.mata_pelajaran.nama}`
        this.items = getData.pd
      })
    },
    resetUjian(user_id){
      this.showAlert('reset', this.$route.params.id, user_id, 'Tindakan ini akan memungkinan Peserta Didik untuk mengulang ujian');
    },
    forceSelesai(user_id){
      this.showAlert('selesai', this.$route.params.id, user_id, 'Tindakan ini akan menyelesaikan proses ujian Peserta Didik');
    },
    isDisabled(status){
      if(status)
        return true
      return false
    },
    statusVariant(status){
      if(status)
        return {
          color: 'secondary',
          text: 'Selesai',
        }
      return {
        color: 'success',
        text: 'Aktif',
      }
    },
    showAlert(aksi, soal_ujian_id, user_id, text){
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: text,
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
          this.$http.post('/pantau-ujian/status', {
            aksi: aksi,
            soal_ujian_id: soal_ujian_id,
            user_id: user_id,
          }).then(response => {
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
              this.loadPostsData()
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