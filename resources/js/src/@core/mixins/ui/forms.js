// We haven't added icon's computed property because it makes this mixin coupled with UI
export const togglePasswordVisibility = {
  data() {
    return {
      passwordFieldType: 'password',
      confirmationPasswordFieldType: 'password'
    }
  },
  methods: {
    togglePasswordVisibility() {
      this.passwordFieldType = this.passwordFieldType === 'password' ? 'text' : 'password'
    },
    toggleConfirmationPasswordVisibility(){
      this.confirmationPasswordFieldType = this.confirmationPasswordFieldType === 'password' ? 'text' : 'password'
    },
  },
}

export const _ = null
