import Vue from 'vue'

// axios
import axios from 'axios'

const axiosIns = axios.create({
  baseURL: app_url+'/api/',
  // You can add your headers here
  // ================================
  // baseURL: 'https://some-domain.com/api/',
  // timeout: 1000,
  // headers: {'X-Custom-Header': 'foobar'}
})
axiosIns.interceptors.request.use(function (config) {
  return config;
}, function (error) {
  return Promise.reject(error);
});
axiosIns.interceptors.response.use(
  response => response,
  error => {
    if (error.response && 419 === error.response.status) {
      window.location.reload()
    } else if (error.response && 401 === error.response.status) {
      localStorage.removeItem('userData')
      localStorage.removeItem('accessToken')
      localStorage.removeItem('userAbilities')
      this.$router.push('/login')
    } else {
      //location.reload()
      return error.response
      //return Promise.reject(error)
    }
});
Vue.prototype.$http = axiosIns

export default axiosIns
