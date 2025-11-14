import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './style.css'
import permissionDirective from './directives/permission'

// ✅ BUAT app instance dulu
const app = createApp(App)

// ✅ REGISTRASI semua plugin & directive SEBELUM mount
app.use(router)
app.directive('permission', permissionDirective)

// ✅ MOUNT terakhir
app.mount('#app')