<template>
  <b-row>
    <b-col cols="12" md="5" lg="4">
      <b-card no-body>
        <b-card-body class="text-center">
          <b-button size="sm" @click="show=true">Edit Foto</b-button>
          <div class="my-1">
            <template v-if="user.photo">
              <b-img rounded v-bind="mainProps" :src="`/storage/images/${user.photo}`"></b-img>
            </template>
            <template v-else>
              <b-avatar size="8rem" rounded :src="user.avatar" variant="light-primary">
                <feather-icon v-if="!user.name" icon="UserIcon" size="22"/>
              </b-avatar>
            </template>
          </div>
          <h5><strong>{{user.name}}</strong></h5>
          <b-badge variant="info">{{user.role}}</b-badge>
        </b-card-body>
        <b-card-footer>
          <h5 class="text-muted">BIODATA</h5>
          <BTableSimple borderless>
            <BTr>
              <BTd class="padding"><strong>Nama:</strong></BTd>
              <BTd>{{user.name}}</BTd>
            </BTr>
            <BTr>
              <BTd class="padding"><strong>Email:</strong></BTd>
              <BTd>{{user.email}}</BTd>
            </BTr>
            <BTr>
              <BTd class="padding"><strong>Hak Akses:</strong></BTd>
              <BTd>{{user.role}}</BTd>
            </BTr>
            <BTr>
              <BTd class="padding"><strong>Whatsapp:</strong></BTd>
              <BTd>{{user.whatsapp}}</BTd>
            </BTr>
          </BTableSimple>
        </b-card-footer>
      </b-card>
    </b-col>
    <b-col cols="12" md="7" lg="8">
      <b-tabs pills>
        <b-tab active>
          <template #title>
            <user-check-icon size="18" /> BIODATA
          </template>
          <b-card>
            <BiodataGuru v-if="hasRole('guru')" />
          </b-card>
        </b-tab>
        <b-tab>
          <template #title>
            <lock-icon size="18" /> KEAMANAN
          </template>
          <Security />
        </b-tab>
        <b-tab v-if="hasRole(['guru', 'pd'])">
          <template #title>
            <bell-icon size="18" /> JADWAL
          </template>
          <jadwal-guru v-if="hasRole('guru')"></jadwal-guru>
          <jadwal-siswa v-if="hasRole('pd')"></jadwal-siswa>
        </b-tab>
        <b-tab v-if="hasRole('guru')">
          <template #title>
            <calendar-event-icon size="18" /> ABSENSI
          </template>
          <rekap-absensi />
        </b-tab>
        <b-tab v-if="hasRole('pd')">
          <template #title>
            <bell-icon size="18" /> Pelanggaran
          </template>
          <b-card>
            4
            <pre>
              {{user}}
            </pre>
          </b-card>
        </b-tab>
      </b-tabs>
    </b-col>
    <b-modal v-model="show" centered hide-footer hide-header>
      <div class="my-1">
        <b-overlay :show="loading" opacity="0.6" size="md" spinner-variant="secondary">
          <b-form-file v-model="foto" accept=".jpg, .png, .jpeg" placeholder="Upload Foto..." drop-placeholder="Drop file here..." @change="onFileChange"></b-form-file>
        </b-overlay>
      </div>
    </b-modal>
  </b-row>
</template>

<script>
import { BRow, BCol, BCard, BCardBody, BCardFooter, BButton, BImg, BAvatar, BBadge, BTableSimple, BTr, BTd, BTabs, BTab, BModal, BFormFile, BOverlay, BForm } from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import BiodataGuru from './BiodataGuru.vue'
import Security from './Security.vue'
import JadwalGuru from './JadwalGuru.vue'
import JadwalSiswa from './JadwalSiswa.vue'
import RekapAbsensi from './RekapAbsensi.vue'
export default {
  components: {
    BRow,
    BCol,
    BCard,
    BCardBody,
    BCardFooter, 
    BButton,
    BImg,
    BAvatar,
    BBadge,
    BTableSimple,
    BTr,
    BTd,
    BTabs, 
    BTab,
    BFormFile,
    BOverlay,
    BForm,
    BiodataGuru,
    Security,
    JadwalGuru,
    JadwalSiswa,
    RekapAbsensi
  },
  data() {
    return {
      show: false,
      loading: false,
      mainProps: {width: 125, height: 125 },
      foto: null,
    }
  },
  
  ///
  methods: {
    onFileChange(e) {
      this.loading = true
      this.foto = e.target.files[0];
      const data = new FormData();
      data.append('foto', this.foto);
      data.append('user_id', this.user.user_id);
      this.$http.post('/auth/user/foto', data).then(response => {
        this.loading = false
        let getData = response.data
        eventBus.$emit('foto', getData.foto);
        this.user.photo = getData.foto
        this.$swal({
          icon: getData.icon,
          title: getData.title,
          text: getData.text,
          customClass: {
            confirmButton: 'btn btn-success',
          },
        }).then(result => {
          this.show = false
        })
      });
    },
  }
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
td.padding {padding:0px;}
</style>