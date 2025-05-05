<template>
  <b-container fluid class="mt-3">
    <b-row>
      <b-col cols="4" offset="4">
        <h3 class="text-center">Laman SCAN pindah ke link <br/> <b-link href="https://scan.smpalfalahsg.sch.id">https://scan.smpalfalahsg.sch.id</b-link></h3>
        <!--b-form-input v-model="form_id" placeholder="ID Peserta Didik/ID PTK" ref="form_id" size="lg" @input="scan" :disabled="disabled" @blur="onClick"></b-form-input>
        <div class="scan mt-2">
          <div class="qrcode"></div>
          <h3>QR Code Scanning...</h3>
          <div class="border"></div>
        </div-->
      </b-col>
    </b-row>
    <b-toast v-model="visible" :variant="variant" :title="title" @shown="handleShow">
      {{message}}
    </b-toast>
  </b-container>
</template>

<script>
import { BContainer, BRow, BCol, BFormInput, BToast, BLink } from 'bootstrap-vue'
import ToastificationContent from '@core/components/toastification/ToastificationContent.vue'
import axios from 'axios'
import _ from 'lodash'
export default {
  components: {
    BContainer,
    BRow,
    BCol,
    BFormInput,
    BToast,
    BLink,
  },
  data(){
    return {
      form_id: '',
      disabled: false,
      visible: false,
      variant: '',
      title: '',
      message: '',
    }
  },
  mounted () {
    //this.onClick();
    //document.addEventListener('click', this.onClick);
    var _this = this
    window.Echo.channel('scan').listen('ScanEvent', (e) => {
      let getData = e.message
      _this.variant = getData.type
      _this.title = getData.title
      _this.message = getData.message
      _this.visible = true
      _this.sound(getData.mp3)
    });
  },
  created() {
    //this.onClick();
  },
  methods: {
    onClick() { 
      this.$nextTick(() => {
        this.$refs.form_id.focus()
      })
    },
    handleShow(){
      this.onClick()
    },
    sound(mp3){
      new Audio(`/mp3/${mp3}`).play();
    },
    scan: _.debounce(function (e) {
      this.disabled = true
      axios.post('/api/scan', {
        id: e,
      }).then(response => {
        this.disabled = false
        this.form_id = ''
        this.onClick()
      });
    }, 500),
  },
}
</script>
<style lang="scss">
*{box-sizing:border-box;font-family:consolas;margin:0;padding:0}body{background:#111;justify-content:center;min-height:100vh}.scan{align-items:center;display:flex}.scan{flex-direction:column;position:relative}.scan .qrcode{background:url(/img/scan/QR_Code01.png?dca1d8a3939dc88eb3cd6117ac0beeea);background-size:400px;height:400px;position:relative;width:400px}.scan .qrcode:before{-webkit-animation:animate 4s ease-in-out infinite;animation:animate 4s ease-in-out infinite;background:url(/img/scan/QR_Code02.png?e6d7e7e17ef605c594045b125d891f0d);background-size:400px;content:"";height:100%;left:0;overflow:hidden;position:absolute;top:0;width:100%}@-webkit-keyframes animate{0%,to{height:20px}50%{height:calc(100% - 20px)}}@keyframes animate{0%,to{height:20px}50%{height:calc(100% - 20px)}}.scan .qrcode:after{-webkit-animation:animate_line 4s ease-in-out infinite;animation:animate_line 4s ease-in-out infinite;background:#35fd5c;content:"";filter:drop-shadow(0 0 20px #35fd5c) drop-shadow(0 0 60px #35fd5c);height:2px;inset:20px;position:absolute;width:calc(100% - 40px)}@-webkit-keyframes animate_line{0%,to{top:20px}50%{top:calc(100% - 20px)}}@keyframes animate_line{0%,to{top:20px}50%{top:calc(100% - 20px)}}.border{-webkit-animation:animate_text .5s linear infinite;animation:animate_text .5s linear infinite;background:url(/img/scan/border.png?4cc6dfdd18c8c16bfba4dd88038aa533);background-repeat:no-repeat;background-size:400px;inset:0;position:absolute}@-webkit-keyframes animate_text{0%,to{opacity:0}50%{opacity:1}}@keyframes animate_text{0%,to{opacity:0}50%{opacity:1}}.scan h3{-webkit-animation:animate_text .5s steps(1) infinite;animation:animate_text .5s steps(1) infinite;color:#fff;filter:drop-shadow(0 0 20px #fff) drop-shadow(0 0 60px #fff);font-size:2em;letter-spacing:2px;margin-top:20px;text-transform:uppercase}
.toast-body {font-size: 2rem;}
</style>