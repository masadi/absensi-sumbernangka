<template>
  <b-card title="Ganti Password">
    <b-alert show variant="warning">
      <div class="alert-body">
        <h4 class="alert-heading" style="font-size: 1.3rem; padding:0px;">Pastikan persyaratan ini terpenuhi!</h4>
        <p>Minimal 8 karakter, huruf besar &amp; simbol</p>
      </div>
    </b-alert>
    <b-form @submit="onSubmit">
      <b-overlay :show="loading" rounded opacity="0.6" size="lg" spinner-variant="danger">
        <b-row class="mb-2">
          <b-col cols="12" md="6">
            <b-form-group :invalid-feedback="feedback.password" :state="state.password">
              <b-input-group class="input-group-merge" :class="state.password ? 'is-invalid':null">
                <b-form-input id="login-password" v-model="password" :state="state.password > 0 ? false:null" class="form-control-merge" :type="passwordFieldType" name="login-password" placeholder="Password Baru" />
                <b-input-group-append is-text>
                  <feather-icon class="cursor-pointer" :icon="passwordToggleIcon" @click="togglePasswordVisibility" />
                </b-input-group-append>
              </b-input-group>
            </b-form-group>
          </b-col>
          <b-col cols="12" md="6">
            <b-form-group :invalid-feedback="feedback.password_confirmation" :state="state.password_confirmation">
              <b-input-group class="input-group-merge" :class="state.password_confirmation > 0 ? 'is-invalid':null">
                <b-form-input id="login-password" v-model="password_confirmation" :state="state.password_confirmation > 0 ? false:null" class="form-control-merge" :type="confirmationPasswordFieldType" name="login-password" placeholder="Ulangi Password" />
                <b-input-group-append is-text>
                  <feather-icon class="cursor-pointer" :icon="confirmationPasswordToggleIcon" @click="toggleConfirmationPasswordVisibility" />
                </b-input-group-append>
              </b-input-group>
            </b-form-group>
          </b-col>
        </b-row>
        <b-row>
          <b-col cols="12">
            <b-button type="submit" variant="primary">Ganti Password</b-button>
          </b-col>
        </b-row>
      </b-overlay>
    </b-form>
  </b-card>
</template>

<script>
import { BCard, BAlert, BForm, BOverlay, BRow, BCol, BFormGroup, BInputGroup, BFormInput, BInputGroupAppend, BButton } from 'bootstrap-vue'
import { togglePasswordVisibility, toggleConfirmationPasswordVisibility } from '@core/mixins/ui/forms'
export default {
  components: {
    BCard,
    BAlert,
    BForm,
    BOverlay,
    BRow,
    BCol,
    BFormGroup,
    BInputGroup,
    BFormInput,
    BInputGroupAppend,
    BButton,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      feedback: {},
      state: {},
      password: '',
      password_confirmation: '',
      loading: false,
    }
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    confirmationPasswordToggleIcon() {
      return this.confirmationPasswordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
  },
  methods: {
    onSubmit(event) {
      event.preventDefault()
      this.loading = true
      this.$http.post('/auth/user/ganti-password', {
        password: this.password,
        password_confirmation: this.password_confirmation,
      }).then(response => {
        this.loading = false
        let getData = response.data
        if(getData.errors){
          this.state.password = (getData.errors.password) ? false : null
          this.state.password_confirmation = (getData.errors.password_confirmation) ? false : null
          this.feedback.password = (getData.errors.password) ? getData.errors.password.join(', ') : ''
          this.feedback.password_confirmation = (getData.errors.password_confirmation) ? getData.errors.password_confirmation.join(', ') : ''
        } else {
          this.$swal({
            icon: getData.icon,
            title: getData.title,
            text: getData.text,
            customClass: {
              confirmButton: 'btn btn-success',
            },
          }).then(result => {
            this.password = ''
            this.password_confirmation = ''
            this.passwordFieldType = 'password'
            this.confirmationPasswordFieldType = 'password'
            this.state.password = null
            this.state.password_confirmation = null
            this.feedback.password = ''
            this.feedback.password_confirmation = ''
          })
        }
      })
    },
  },
}
</script>
<style lang="scss">
@import '~@resources/scss/vue/libs/vue-sweetalert.scss';
</style>