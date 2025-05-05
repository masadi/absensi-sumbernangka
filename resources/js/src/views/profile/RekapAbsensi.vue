<template>
  <b-card>
    rekap absensi
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