<template>
  <b-nav-item-dropdown class="dropdown-notification mr-25" menu-class="dropdown-menu-media" right>
    <template #button-content>
      <feather-icon :badge="notifications.length" badge-classes="bg-danger" class="text-body" icon="BellIcon" size="21" />
    </template>

    <!-- Header -->
    <li class="dropdown-menu-header">
      <div class="dropdown-header d-flex">
        <h4 class="notification-title mb-0 mr-auto">
          Notifications
        </h4>
        <b-badge pill variant="light-primary" v-if="notifications.length">
          {{notifications.length}} New
        </b-badge>
      </div>
    </li>

    <!-- Notifications -->
    <vue-perfect-scrollbar :settings="perfectScrollbarSettings" class="scrollable-container media-list scroll-area" tagname="li">
      <b-link v-for="notification in notifications" :key="notification.subtitle" :to="{name: notification.route}">
        <b-media>
          <p class="media-heading">
            <span class="font-weight-bolder">
              {{ notification.title }}
            </span>
          </p>
          <small class="notification-text">{{ notification.subtitle }}</small>
        </b-media>
      </b-link>
    </vue-perfect-scrollbar>
  </b-nav-item-dropdown>
</template>

<script>
import {
  BNavItemDropdown, BBadge, BMedia, BLink, BAvatar, BButton, BFormCheckbox,
} from 'bootstrap-vue'
import VuePerfectScrollbar from 'vue-perfect-scrollbar'

export default {
  components: {
    BNavItemDropdown,
    BBadge,
    BMedia,
    BLink,
    BAvatar,
    VuePerfectScrollbar,
    BButton,
    BFormCheckbox,
  },
  data() {
    return {
      notifications: [],
      perfectScrollbarSettings: {
        maxScrollbarLength: 3,
        wheelPropagation: false,
      },
    }
  },
  created() {
    if(this.user){
      this.$http.post('/dashboard/notifikasi', {
        guru_id: this.user.guru_id,
        periode_aktif: this.user.semester.nama,
        peserta_didik_id: this.user.peserta_didik_id,
        semester_id: this.user.semester.semester_id,
        tanggal_mulai: this.user.semester.tanggal_mulai,
        tanggal_selesai: this.user.semester.tanggal_selesai,
      }).then(response => {
        let getData = response.data
        this.notifications = getData.notifications
      });
    }
  },
}
</script>