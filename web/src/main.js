import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './style.css'
// HAPUS BARIS INI: import '@fortunezone/fortunezone-free/css/all.css'

createApp(App).use(router).mount('#app')