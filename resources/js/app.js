import Vue from 'vue'
import App from './App.vue'
import Router from 'vue-router'
import routes from './routes'
import axios from 'axios'
// import Echo from 'laravel-echo'

// Some Axios setup
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'
let token = document.head.querySelector('meta[name="csrf-token"]')
if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token')
}
Vue.prototype.$http = axios

// Some Laravel Echo setup
// window.io = require('socket.io-client');
// window.Echo = new Echo({
//     broadcaster: 'socket.io',
//     host: window.location.hostname + ':6001'
// });

// Some router setup
Vue.use(Router)
const router = new Router({
    routes,
})

// Main Vue instance
new Vue({
    router,
    render: h => h(App)
}).$mount('#app')
