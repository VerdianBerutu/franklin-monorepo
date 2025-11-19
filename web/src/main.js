import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './style.css'
import permissionDirective from './directives/permission'

//  PERBAIKAN: Import axios configuration dari plugins
import './plugins/axios'

//  Create app instance
const app = createApp(App)

//  Register plugins & directives
app.use(router)
app.directive('permission', permissionDirective)

//  Mount app
app.mount('#app')

console.log('🚀 App mounted successfully')