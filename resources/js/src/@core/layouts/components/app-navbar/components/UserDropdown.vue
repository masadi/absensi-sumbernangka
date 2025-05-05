<template>
  <b-nav-item-dropdown right toggle-class="d-flex align-items-center dropdown-user-link" class="dropdown-user" v-if="userData">
    <template #button-content>
      <b-avatar size="40" :src="userData.profile_photo_path ? `/storage/${userData.profile_photo_path}` : null" variant="light-primary" badge class="badge-minimal" badge-variant="success">
        <feather-icon v-if="!userData.profile_photo_path" icon="UserIcon" size="22"/>
      </b-avatar>
    </template>
    <b-dropdown-text link-class="d-flex align-items-center">
      <b-media no-body>
        <b-media-aside vertical-align="center" class="mr-75">
          <b-avatar size="40" :src="userData.profile_photo_path ? `/storage/${userData.profile_photo_path}` : null" variant="light-primary" badge class="badge-minimal" badge-variant="success">
            <feather-icon v-if="!userData.profile_photo_path" icon="UserIcon" size="22"/>
          </b-avatar>
        </b-media-aside>
        <b-media-body class="my-auto">
          <h6 class="mb-0">
            {{ userData.name }}
          </h6>
          <small>{{ userData.role }}</small>
        </b-media-body>
      </b-media>
    </b-dropdown-text>
    <b-dropdown-divider />
    <b-dropdown-item :to="{ name: 'pengaturan' }" link-class="d-flex align-items-center" v-if="hasRole('administrator')">
      <feather-icon size="16" icon="ToolIcon" class="mr-50" />
      <span>Pengaturan</span>
    </b-dropdown-item>
    <b-dropdown-item :to="{ name: 'profile' }" link-class="d-flex align-items-center">
      <feather-icon size="16" icon="UserIcon" class="mr-50" />
      <span>Profile</span>
    </b-dropdown-item>
    <b-dropdown-item link-class="d-flex align-items-center" @click="logout">
      <feather-icon size="16" icon="LogOutIcon" class="mr-50" />
      <span>Logout</span>
    </b-dropdown-item>
  </b-nav-item-dropdown>
</template>

<script>
import {
  BNavItemDropdown, BDropdownItem, BDropdownText, BDropdownDivider, BAvatar, BMedia, BMediaAside, BMediaBody
} from 'bootstrap-vue'
import eventBus from '@core/utils/eventBus'
import { initialAbility } from '@/libs/acl/config'
import useJwt from '@/auth/jwt/useJwt'
import { avatarText } from '@core/utils/filter'

export default {
  components: {
    BNavItemDropdown,
    BDropdownItem,
    BDropdownText,
    BDropdownDivider,
    BAvatar,
    BMedia,
    BMediaAside,
    BMediaBody,
  },
  data() {
    return {
      mainProps: {width: 40, height: 40 },
      userData: JSON.parse(localStorage.getItem('userData')),
      avatarText,
    }
  },
  created() {
    eventBus.$on('foto', (val) => {
      this.userData.profile_photo_path = val
      localStorage.setItem('userData', JSON.stringify(this.userData))
    })
  },
  methods: {
    logout() {
      // Remove userData from localStorage
      // ? You just removed token from localStorage. If you like, you can also make API call to backend to blacklist used token
      localStorage.removeItem(useJwt.jwtConfig.storageTokenKeyName)
      localStorage.removeItem(useJwt.jwtConfig.storageRefreshTokenKeyName)
      localStorage.removeItem('semester_id')
      // Remove userData from localStorage
      localStorage.removeItem('userData')

      // Reset ability
      this.$ability.update(initialAbility)

      // Redirect to login page
      this.$router.push({ name: 'homepage' })
    },
  },
}
</script>
