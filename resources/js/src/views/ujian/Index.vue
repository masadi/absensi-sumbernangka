<template>
  <b-card no-body>
    <b-card-header>
      <b-card-title>
        Daftar Mata Ujian {{ (rombongan_belajar) ? `di Kelas ${rombongan_belajar.nama}` : '' }}
      </b-card-title>
    </b-card-header>
    <b-card-body>
      <div v-if="loading" class="text-center text-danger my-2">
        <b-spinner class="align-middle"></b-spinner>
        <strong>Loading...</strong>
      </div>
      <div v-else>
        <b-table-simple bordered responsive>
          <b-thead>
            <b-tr>
              <b-th class="text-center">No</b-th>
              <b-th class="text-center">Mata Ujian</b-th>
              <b-th class="text-center">Mata Pelajaran</b-th>
              <b-th class="text-center">Tanggal</b-th>
              <b-th class="text-center">Jam Ke</b-th>
              <b-th class="text-center">Aksi</b-th>
            </b-tr>
          </b-thead>
          <b-tbody>
            <template v-if="items.length">
              <b-tr v-for="(item, index) in items" :key="item.id">
                <b-td class="text-center">{{ index + 1 }}</b-td>
                <b-td>{{ item.jadwal.nama }}</b-td>
                <b-td>{{ item.mata_pelajaran.nama }}</b-td>
                <b-td class="text-center">{{ item.tanggal_indo }}</b-td>
                <b-td class="text-center">{{ item.jam_ke }}</b-td>
                <b-td class="text-center">
                  <template v-if="item.soal_ujian">
                    <template v-if="item.soal_ujian.ujian_siswa">
                      <template v-if="item.soal_ujian.ujian_siswa.status">
                        <b-button size="sm" variant="danger">Ujian Selesai</b-button>
                      </template>
                      <template v-else>
                        <b-button variant="warning" :href="`/proses-ujian/${item.rombongan_belajar.anggota.anggota_rombel_id}/${item.soal_ujian.id}?nomor=1`" size="sm" v-if="item.soal_ujian.status">Lanjutkan Ujian</b-button>
                        <b-button size="sm" v-else>Ujian Non Aktif</b-button>
                      </template>
                    </template>
                    <template v-else>
                      <b-button variant="success" :href="`/proses-ujian/${item.rombongan_belajar.anggota.anggota_rombel_id}/${item.soal_ujian.id}?nomor=1`" size="sm" v-if="item.soal_ujian.status">Proses Ujian</b-button>
                      <b-button size="sm" v-else>Ujian Non Aktif</b-button>
                    </template>
                  </template>
                  <template v-else>
                    -
                  </template>
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
      </div>
    </b-card-body>
  </b-card>
</template>

<script>
import { BCard, BCardHeader, BCardTitle, BCardBody, BSpinner, BTableSimple, BThead, BTr, BTh, BTbody, BTd, BButton } from 'bootstrap-vue'

export default {
  components: {
    BCard,
    BCardHeader,
    BCardTitle,
    BCardBody,
    BSpinner,
    BTableSimple,
    BThead,
    BTr,
    BTh,
    BTbody,
    BTd,
    BButton,
  },
  data() {
    return {
      items: [],
      rombongan_belajar: null,
      loading: false,
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    loadPostsData() {
      this.loading = true
      this.$http.get('/ujian').then(response => {
        let getData = response.data
        this.loading = false
        this.items = getData.items
        this.rombongan_belajar = getData.rombongan_belajar
      })
    },
  },
}
</script>