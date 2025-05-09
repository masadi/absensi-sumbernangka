<template>
  <div class="auth-wrapper auth-v2">
    <b-row class="auth-inner m-0">

      <!-- Brand logo-->
      <b-link class="brand-logo">
        <vuexy-logo />
        <h2 class="brand-text text-primary ml-1" v-html="appName"></h2>
      </b-link>
      <!-- /Brand logo-->

      <!-- Left Text-->
      <b-col lg="8" class="d-none d-lg-flex align-items-center p-5">
        <div class="w-100 d-lg-flex align-items-center justify-content-center px-5">
          <b-img fluid :src="imgUrl" alt="Login V2" />
        </div>
      </b-col>
      <!-- /Left Text-->

      <!-- Login-->
      <b-col lg="4" class="d-flex align-items-center auth-bg px-2 p-lg-5">
        <b-col sm="8" md="6" lg="12" class="px-xl-2 mx-auto">
          <b-card-title class="mb-1 font-weight-bold" title-tag="h2" v-html="`Welcome to ${appBrand}`"></b-card-title>
          <b-card-text class="mb-2">
            Silahkan Login dengan Akun Anda!
          </b-card-text>
          <!-- form -->
          <validation-observer ref="loginForm" #default="{invalid}">
            <b-form class="auth-login-form mt-2" @submit.prevent="login">
              <!-- email -->
              <b-form-group label="Email" label-for="login-email">
                <validation-provider #default="{ errors }" name="Email" vid="email" rules="required|email">
                  <b-form-input ref="form_email" id="login-email" v-model="userEmail" :state="errors.length > 0 ? false:null" name="login-email" placeholder="Email Valid" />
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- forgot password -->
              <b-form-group>
                <div class="d-flex justify-content-between">
                  <label for="login-password">Password</label>
                  <!--b-link :to="{name:'auth-forgot-password'}">
                    <small>Forgot Password?</small>
                  </b-link-->
                </div>
                <validation-provider #default="{ errors }" name="Password" vid="password" rules="required">
                  <b-input-group class="input-group-merge" :class="errors.length > 0 ? 'is-invalid':null">
                    <b-form-input id="login-password" v-model="password" :state="errors.length > 0 ? false:null" class="form-control-merge" :type="passwordFieldType" name="login-password" placeholder="Password" />
                    <b-input-group-append is-text>
                      <feather-icon class="cursor-pointer" :icon="passwordToggleIcon" @click="togglePasswordVisibility" />
                    </b-input-group-append>
                  </b-input-group>
                  <small class="text-danger">{{ errors[0] }}</small>
                </validation-provider>
              </b-form-group>

              <!-- checkbox -->
              <b-form-group>
                <b-form-checkbox id="remember-me" v-model="status" name="checkbox-1">
                  Remember Me
                </b-form-checkbox>
              </b-form-group>

              <b-overlay :show="loading" rounded opacity="0.6" spinner-variant="warning" spinner-small class="d-inline-block">
                <b-button type="submit" variant="primary" block :disabled="invalid">
                  Sign in
                </b-button>
              </b-overlay>
            </b-form>
          </validation-observer>
        </b-col>
      </b-col>
    <!-- /Login-->
    </b-row>
  </div>
</template>

<script>
/* eslint-disable global-require */
import { ValidationProvider, ValidationObserver } from 'vee-validate'
import VuexyLogo from '@core/layouts/components/Logo.vue'
import {
  BRow,
  BCol,
  BLink,
  BFormGroup,
  BFormInput,
  BInputGroupAppend,
  BInputGroup,
  BFormCheckbox,
  BCardText,
  BCardTitle,
  BImg,
  BForm,
  BButton,
  BAlert,
  VBTooltip,
  BOverlay,
} from 'bootstrap-vue'
import useJwt from '@/auth/jwt/useJwt'
import { required, email } from '@validations'
import { togglePasswordVisibility } from '@core/mixins/ui/forms'
import store from '@/store/index'

import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'

export default {
  directives: {
    'b-tooltip': VBTooltip,
  },
  components: {
    BRow,
    BCol,
    BLink,
    BFormGroup,
    BFormInput,
    BInputGroupAppend,
    BInputGroup,
    BFormCheckbox,
    BCardText,
    BCardTitle,
    BImg,
    BForm,
    BButton,
    BAlert,
    VuexyLogo,
    ValidationProvider,
    ValidationObserver,
    BOverlay,
  },
  mixins: [togglePasswordVisibility],
  data() {
    return {
      loading: false,
      status: '',
      password: '',
      userEmail: '',
      sideImg: '/img/pages/login-v2.svg',

      // validation rules
      required,
      email,
    }
  },
  computed: {
    passwordToggleIcon() {
      return this.passwordFieldType === 'password' ? 'EyeIcon' : 'EyeOffIcon'
    },
    imgUrl() {
      if (store.state.appConfig.layout.skin === 'dark') {
        // eslint-disable-next-line vue/no-side-effects-in-computed-properties
        this.sideImg = '/img/pages/login-v2-dark.svg'
        return this.sideImg
      }
      return this.sideImg
    },
    appName(){
      return app_name;
    },
    appBrand(){
      return app_brand;
    },
  },
  created() {
    this.setFocus();
  },
  methods: {
    setFocus(){
      this.$nextTick(() => {
        this.$refs.form_email.focus()
      })
    },
    login() {
      this.loading = true
      this.$refs.loginForm.validate().then(success => {
        if (success) {
          this.$http.post('/auth/login', {
            email: this.userEmail,
            password: this.password,
          }).then(response => {
            this.loading = false
            const { userData } = response.data
            if(userData){
              //useJwt.setToken(response.data.accessToken)
              //useJwt.setRefreshToken(response.data.refreshToken)
              localStorage.setItem('accessToken', userData.accessToken)
              localStorage.setItem('refreshToken', userData.accessToken)
              localStorage.setItem('userData', JSON.stringify(userData))
              this.$ability.update(userData.ability)

              // ? This is just for demo purpose as well.
              // ? Because we are showing eCommerce app's cart items count in navbar
              
              // ? This is just for demo purpose. Don't think CASL is role based in this case, we used role in if condition just for ease
              this.$router.replace({name: 'dashboard'}).then(() => {
                this.$toast({
                  component: ToastificationContent,
                  position: 'top-right',
                  props: {
                    title: `Welcome ${userData.name}`,
                    icon: 'CoffeeIcon',
                    variant: 'success',
                    text: `You have successfully logged in as ${userData.role}. Now you can start to explore!`,
                  },
                })
              })
            } else {
              //console.log(response.data);
              this.$refs.loginForm.setErrors(response.data.errors)
            }
          }).catch(error => {
            console.log('bawah');
            console.log(error);
            //this.$refs.loginForm.setErrors(error.response.data.error)
          })
        }
      })
    },
  },
}
</script>

<style lang="scss">
@import '~@resources/scss/vue/pages/page-auth.scss';
</style>
