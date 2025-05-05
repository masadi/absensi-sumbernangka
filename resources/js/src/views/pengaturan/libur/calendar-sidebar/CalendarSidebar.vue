<template>
  <div class="sidebar-wrapper d-flex justify-content-between flex-column flex-grow-1">
    <div class="p-2">
      <b-button v-ripple.400="'rgba(255, 255, 255, 0.15)'" aria-controls="sidebar-add-new-event" :aria-expanded="String(isEventHandlerSidebarActive)" variant="primary" block @click="addLibur">
        Tambah Libur
      </b-button>
      <div class="mt-3">
        <h5 class="app-label section-label mb-1">
          <span class="align-middle">Filter Kategori</span>
        </h5>
        <b-form-checkbox v-model="checkAll" class="mb-1">
          Semua Kategori
        </b-form-checkbox>
        <b-form-group>
          <b-form-checkbox-group v-model="selectedCalendars" name="event-filter" stacked>
            <template v-for="item in kategori_libur">
              <b-form-checkbox name="event-filter" :value="item.id" class="mb-1" :class="`custom-control-danger`">
                {{ item.nama }} <b-link @click="edit(item)" class="text-warning"><pencil-icon size="16px" /></b-link> <b-link @click="hapus(item.id)" class="text-danger"><trash-icon size="16px" /></b-link>
              </b-form-checkbox>
            </template>
          </b-form-checkbox-group>
        </b-form-group>
        <b-form-group>
          <b-button variant="success" size="sm" @click="addKategori">Tambah Kategori</b-button>
        </b-form-group>
      </div>
    </div>
    <b-img :src="'/images/calendar-illustration.png'" />
  </div>
</template>

<script>
import {
  BButton, BFormGroup, BFormCheckboxGroup, BFormCheckbox, BImg, BLink
} from 'bootstrap-vue'
import Ripple from 'vue-ripple-directive'
import eventBus from '@core/utils/eventBus'

export default {
  directives: {
    Ripple,
  },
  components: {
    BButton,
    BImg,
    BFormCheckbox,
    BFormGroup,
    BFormCheckboxGroup,
    BLink,
  },
  props: {
    isEventHandlerSidebarActive: {
      type: Boolean,
      require: true,
    },
    kategori_libur: {
      type: Array,
      require: true,
      default: [],
    }
  },
  data(){
    return {
      calendarOptions: [],
      selectedCalendars: [],
      checkAll: [],
    }
  },
  created(){
    this.loadPostsData()
  },
  methods: {
    loadPostsData(){
      //console.log(this.kategori_libur);
    },
    addLibur(){
      eventBus.$emit('open-modal-libur', {
        aksi: 'add',
        data: null
      });
    },
    addKategori(){
      eventBus.$emit('open-modal-kategori', {
        aksi: 'add',
        data: null
      });
    },
    edit(data){
      eventBus.$emit('open-modal-kategori', {
        aksi: 'edit',
        data: data
      });
    },
    hapus(id){
      this.$swal({
        title: 'Apakah Anda yakin?',
        text: 'Tindakan ini tidak dapat dikembalikan!',
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
          this.$http.post('/libur/hapus', {
            aksi: 'kategori',
            id: id,
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
              this.$emit('reload')
            })
          });
        }
      })
    }
  },
}
</script>