<template>
  <p class="clearfix mb-0">
    <span class="float-md-left d-block d-md-inline-block mt-25">
      Hak Cipta &copy {{ new Date().getFullYear() }}
      <b-link class="ml-25" href="https://smpalfalahsg.sch.id/" target="_blank">SMP AL-FALAH SUMBER GAYAM</b-link>,
      <span class="d-none d-sm-inline-block"> All rights Reserved</span>
    </span>

    <span class="float-md-right d-none d-md-block">Versi 1.0.0
      <feather-icon icon="HeartIcon" size="21" class="text-danger stroke-current" />
    </span>
  </p>
</template>

<script>
import { BLink } from 'bootstrap-vue'

export default {
  components: {
    BLink,
  },
}
</script>
