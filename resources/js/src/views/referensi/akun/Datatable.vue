<template>
  <div>
    <b-row>
      <b-col md="4" class="mb-2">
        <v-select v-model="meta.sekolah_id" :options="data_sekolah" @input="changeSekolah" :searchable="false" placeholder="==Filter Sekolah==" :reduce="nama => nama.sekolah_id" label="nama"></v-select>
      </b-col>
      <b-col md="4" class="mb-2">
        <v-select v-model="meta.tingkat" :options="data_tingkat" @input="changeTingkat" :searchable="false" placeholder="==Filter Tingkat Kelas==" :reduce="nama => nama.tingkat_pendidikan_id" label="nama"></v-select>
      </b-col>
      <b-col md="4" class="mb-2">
        <v-select v-model="meta.rombongan_belajar_id" :options="data_rombel" @input="changeRombel" :searchable="false" placeholder="==Filter Rombongan Belajar==" :reduce="nama => nama.rombongan_belajar_id" label="nama"></v-select>
      </b-col>
    </b-row>
    <b-row>
      <b-col md="4" class="mb-2">
        <v-select v-model="meta.per_page" :options="[10, 25, 50, 100]" @input="loadPerPage" :clearable="false" :searchable="false"></v-select>
      </b-col>
      <b-col md="4" offset-md="4">
        <b-form-input @input="search" placeholder="Cari data..."></b-form-input>
      </b-col>
    </b-row>
    <b-overlay :show="loading" rounded opacity="0.6" size="lg" spinner-variant="warning">
      <b-table bordered striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty :busy="isBusy">
        <template v-slot:empty>
          <p class="text-center">Tidak ada data untuk ditampilkan</p>
        </template>
        <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>
        <template v-slot:cell(sekolah)="row">
          {{ row.item.sekolah.nama }}
        </template>
        <template v-slot:cell(kelas)="row">
          {{ row.item.pd.kelas.nama }}
        </template>
        <template v-slot:cell(password)="row">
          <template v-if="cekPass(row.item.password, '12345678')">
            12345678
          </template>
          <template v-else>
            <b-badge variant="success">Custom</b-badge>
          </template>
        </template>
        <template v-slot:cell(actions)="row">
          <b-button variant="danger" @click="hapus(row.item.id)" size="sm"> Hapus</b-button>
        </template>
      </b-table>
    </b-overlay>
    <b-row class="mt-2">
      <b-col md="6">
        <p>Menampilkan {{ (meta.from) ? meta.from : 0 }} sampai {{ meta.to }} dari {{ meta.total }} entri</p>
      </b-col>
      <b-col md="6">
        <b-pagination v-model="meta.current_page" :total-rows="meta.total" :per-page="meta.per_page" align="right" @change="changePage" aria-controls="dw-datatable"></b-pagination>
      </b-col>
    </b-row>
  </div>
</template>

<script>
import _ from 'lodash'
import { BRow, BCol, BFormInput, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BOverlay, BButtonGroup, BButton, BBadge } from 'bootstrap-vue'
import bcrypt from 'bcryptjs';
import vSelect from 'vue-select'
export default {
  components: {
    BRow,
    BCol,
    BFormInput,
    BTable,
    BSpinner,
    BPagination,
    BDropdown,
    BDropdownItem,
    BOverlay,
    BButtonGroup,
    BButton,
    BBadge,
    vSelect,
  },
  props: {
    items: {
      type: Array,
      required: true
    },
    data_sekolah: {
      type: Array,
      required: true
    },
    data_tingkat: {
      type: Array,
      required: true
    },
    data_rombel: {
      type: Array,
      required: true
    },
    fields: {
      type: Array,
      required: true
    },
    meta: {
      required: true
    },
    isBusy: {
      type: Boolean,
      default: () => true,
    },
  },
  data() {
    return {
      loading: false,
      loading_sync: false,
      sortBy: null,
      sortDesc: false,
    }
  },
  watch: {
    sortBy(val) {
      this.$emit('sort', {
        sortBy: this.sortBy,
        sortDesc: this.sortDesc
      })
    },
    sortDesc(val) {
      this.$emit('sort', {
        sortBy: this.sortBy,
        sortDesc: this.sortDesc
      })
    }
  },
  methods: {
    cekPass(password, default_password){
      return bcrypt.compareSync(default_password, password)
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
          this.$http.post('/referensi/post-admin', {
            aksi: 'hapus',
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
              this.$emit('per_page', this.meta.per_page)
            })
          });
        }
      })
    },
    loadPerPage(val) {
      this.$emit('per_page', this.meta.per_page)
    },
    changePage(val) {
      this.$emit('pagination', val)
    },
    search: _.debounce(function (e) {
      this.$emit('search', e)
    }, 500),
    changeSekolah(val) {
      this.$emit('sekolah', val)
    },
    changeTingkat(val) {
      this.$emit('tingkat', val)
    },
    changeRombel(val) {
      this.$emit('rombel', val)
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>