<template>
  <div>
    <b-row>
      <b-col md="4" class="mb-2">
        <v-select v-model="meta.per_page" :options="[10, 25, 50, 100]" :searchable="false" :clearable="false" @input="loadPerPage" />
      </b-col>
      <b-col md="4" class="mb-2">
        <v-select id="role_id" v-model="meta.role_id" :reduce="display_name => display_name.name" label="display_name" :options="data_roles" placeholder="== Filter Role ==" @input="changeRole">
          <template #no-options>
            Tidak ada data untuk ditampilkan
          </template>
        </v-select>
      </b-col>
      <b-col md="4">
        <b-form-input @input="search" placeholder="Cari data..."></b-form-input>
      </b-col>
    </b-row>
    <b-overlay :show="loading" rounded opacity="0.6" size="lg" spinner-variant="warning">
      <b-table bordered striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty :busy="isBusy">
        <template #table-busy>
          <div class="text-center text-danger my-2">
            <b-spinner class="align-middle"></b-spinner>
            <strong>Loading...</strong>
          </div>
        </template>
        <template v-slot:cell(roles)="row">
          {{row.item.roles.map(getFullName).join(', ')}}
        </template>
        <template v-slot:cell(status_password)="row">
          <template v-if="row.item.status_password === 'custom'">
            <b-badge variant="danger">Custom</b-badge>
          </template>
          <template v-else>
            {{row.item.status_password}}
          </template>
        </template>
        <template v-slot:cell(actions)="row">
          <b-dropdown id="dropdown-dropleft" dropleft size="sm" text="Aksi" variant="primary">
            <b-dropdown-item href="javascript:" @click="aksi(row.item.user_id, 'detil')"><eye-icon /> Detil</b-dropdown-item>
            <b-dropdown-item href="javascript:" @click="aksi(row.item.user_id, 'reset')"><lock-open-icon /> Reset Password</b-dropdown-item>
            <b-dropdown-item href="javascript:" @click="aksi(row.item.user_id, 'hapus')"><trash-icon /> Hapus Data</b-dropdown-item>
          </b-dropdown>
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
import _ from 'lodash' //IMPORT LODASH, DIMANA AKAN DIGUNAKAN UNTUK MEMBUAT DELAY KETIKA KOLOM PENCARIAN DIISI
import { BRow, BCol, BFormInput, BFormSelect, BTable, BSpinner, BPagination, BButton, BOverlay, BBadge, BDropdown, BDropdownItem } from 'bootstrap-vue'
import vSelect from 'vue-select'
export default {
  components: {
    BRow,
    BCol,
    BFormInput, BFormSelect,
    BTable,
    BSpinner,
    BPagination,
    BButton,
    BOverlay,
    BBadge,
    BDropdown,
    BDropdownItem,
    vSelect,
  },
  props: {
    items: {
      type: Array,
      required: true
    },
    fields: {
      type: Array,
      required: true
    },
    data_roles: {
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
    loading: {
      type: Boolean,
      default: () => true,
    }
  },
  data() {
    return {
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
    getFullName(item) {
      return item.display_name
    },
    getDetil(val){
      this.$emit('detil', val)
    },
    loadPerPage(val) {
      this.$emit('per_page', this.meta.per_page)
    },
    changePage(val) {
      this.$emit('pagination', val)
    },
    changeRole(val){
      this.$emit('role', this.meta.role_id)
    },
    search: _.debounce(function (e) {
      this.$emit('search', e)
    }, 500),
    aksi(val, aksi){
      this.$emit(aksi, val)
    }
  },
}
</script>