<template>
  <div>
    <b-row>
      <b-col md="4" class="mb-2">
        <v-select v-model="meta.per_page" :options="[10, 25, 50, 100]" @input="loadPerPage" :clearable="false" :searchable="false"></v-select>
      </b-col>
      <template v-if="hasRole(['administrator'])">
        <b-col md="4" class="mb-2">
          <v-select v-model="meta.sekolah_id" :options="meta.data_sekolah" :reduce="nama => nama.sekolah_id" label="nama" @input="changeSekolah" placeholder="== Filter Unit =="></v-select>
        </b-col>
        <b-col md="4">
          <b-form-input @input="search" placeholder="Cari data..."></b-form-input>
        </b-col>
      </template>
      <template v-else>
        <b-col md="4" offset-md="4">
          <b-form-input @input="search" placeholder="Cari data..."></b-form-input>
        </b-col>
      </template>
    </b-row>
    <b-overlay :show="loading" rounded opacity="0.6" size="lg" spinner-variant="warning">
      <b-table bordered striped :items="items" :fields="fields" :sort-by.sync="sortBy" :sort-desc.sync="sortDesc" show-empty :busy="isBusy" :select-mode="selectMode" ref="selectableTable" selectable @row-selected="onRowSelected">
        <template #cell(selected)="{ rowSelected }">
          <template v-if="rowSelected">
            <span aria-hidden="true">&check;</span>
            <span class="sr-only">Selected</span>
          </template>
          <template v-else>
            <span aria-hidden="true">&nbsp;</span>
            <span class="sr-only">Not selected</span>
          </template>
        </template>
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
        <template v-slot:cell(sync)="row">
          <template v-if="row.item.bentuk_pendidikan_id">
            <b-overlay :show="loading_sync" rounded opacity="0.6" size="lg" spinner-variant="warning">
              <b-button-group size="sm">
                <b-button variant="danger" @click="sync('ptk', row.item.sekolah_id)">PTK</b-button>
                <b-button variant="warning" @click="sync('rombel', row.item.sekolah_id)">Rombel</b-button>
                <b-button variant="success" @click="sync('pd', row.item.sekolah_id)">PD</b-button>
              </b-button-group>
            </b-overlay>
          </template>
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
import { BRow, BCol, BFormInput, BTable, BSpinner, BPagination, BDropdown, BDropdownItem, BOverlay, BButtonGroup, BButton } from 'bootstrap-vue'
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
    meta: {
      required: true
    },
    isBusy: {
      type: Boolean,
      default: () => true,
    },
    isAsesor: {
      type: Boolean,
      default: () => false,
    }
  },
  data() {
    return {
      loading: false,
      loading_sync: false,
      sortBy: null,
      sortDesc: false,
      selectMode: 'multi',
      selected: null,
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
    onRowSelected(items) {
      var ptk_id = [];
      items.forEach(function(value, key) {
        ptk_id.push(value.ptk_id)
      })
      this.$emit('selected', {
        item: (ptk_id.length) ? encodeURIComponent(window.btoa(ptk_id.join())) : null,
      })
    },
    aksi(item, aksi){
      this.$emit('aksi', {
        aksi: aksi,
        item: item,
      })
    },
    sync(item, sekolah_id){
      this.loading_sync = true
      this.$http.post(`/referensi/sync-data`, {
        data: item,
        sekolah_id: sekolah_id,
        semester_id: this.user.semester.semester_id,
      }).then(response => {
        this.loading_sync = false
        let getData = response.data
        this.$swal({
          icon: getData.icon,
          title: getData.title,
          text: getData.text,
          customClass: {
            confirmButton: 'btn btn-success',
          },
          allowOutsideClick: false,
        }).then(result => {
          this.$emit('per_page', this.meta.per_page)
        })
      });
    },
    loadPerPage(val) {
      this.$emit('per_page', this.meta.per_page)
    },
    changePage(val) {
      this.$emit('pagination', val)
    },
    changeSekolah(val){
      this.$emit('sekolah', val)
    },
    search: _.debounce(function (e) {
      this.$emit('search', e)
    }, 500),
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>