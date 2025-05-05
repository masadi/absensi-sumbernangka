<template>
  <b-card>
    <b-table-simple bordered>
      <b-thead>
        <b-tr>
          <b-th class="text-center">Hari</b-th>
          <b-th class="text-center">Jam Ke</b-th>
          <b-th class="text-center">Mata Pelajaran</b-th>
          <b-th class="text-center">Kelas</b-th>
        </b-tr>
      </b-thead>
      <b-tbody>
        <template v-for="jadwal in data_jadwal">
          <b-tr>
            <b-td>{{jadwal.hari}}</b-td>
            <b-td class="text-center">{{getJam(jadwal.jam)}}</b-td>
            <b-td>{{jadwal.pembelajaran.nama_mata_pelajaran}}</b-td>
            <b-td class="text-center">{{jadwal.pembelajaran.nama_rombel}}</b-td>
          </b-tr>
        </template>
      </b-tbody>
    </b-table-simple>
  </b-card>
</template>

<script>
import { BCard, BTableSimple, BThead, BTh, BTbody, BTr, BTd } from 'bootstrap-vue'
export default {
  components: {
    BCard,
    BTableSimple,
    BThead,
    BTh,
    BTbody,
    BTr,
    BTd,
  },
  data() {
    return {
      data_jadwal: [],
    }
  },
  created() {
    this.loadPostsData()
  },
  methods: {
    uniqueChars(arr){
      return [...new Set(arr)];
    },
    getJam(array){
      var set_jam = [];
      array.forEach(element => {
        set_jam.push(element.jam)
      });
      return this.uniqueChars(set_jam).join(', ')
    },
    loadPostsData(){
      this.$http.get('/auth/user', {
        params: {
          semester_id: this.user.semester.semester_id
        }
      }).then(response => {
        let getData = response.data
        this.data_jadwal = getData.jadwal
      });
    },
  },
}
</script>