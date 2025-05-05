<template>
  <div>
    <template v-if="hasRole(['wakasiswa', 'wakahumas'])">
      <dashboard-waka></dashboard-waka>
    </template>
    <template v-else-if="hasRole('administrator')">
      <dashboard-admin></dashboard-admin>
    </template>
    <template v-else-if="hasRole(['ptk'])">
      <dashboard-guru></dashboard-guru>
    </template>
    <template v-else-if="hasRole('pd')">
      <dashboard-siswa></dashboard-siswa>
    </template>
    <template v-else>
      <dashboard-user></dashboard-user>
    </template>
  </div>
</template>

<script>
import DashboardWaka from './DashboardWaka.vue'
import DashboardAdmin from './DashboardAdmin.vue'
import DashboardGuru from './DashboardGuru.vue'
//import DashboardSiswa from './DashboardSiswa.vue'
import DashboardSiswa from './../ujian/Index.vue'
import DashboardUser from './DashboardUser.vue'

export default {
  components: {
    DashboardWaka,
    DashboardAdmin, 
    DashboardGuru,
    DashboardSiswa,
    DashboardUser,
  },
}
</script>