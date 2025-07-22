import router from './router/index.js'
import { createApp } from 'vue'
import './main.css'
import App from './App.vue'
import { createI18n } from 'vue-i18n'
import en from './locales/en.json'
import vi from './locales/vi.json'

const i18n = createI18n({
    locale: 'vi',
    fallbackLocale: 'en',
    messages: { en, vi }
})

//Bootstrap
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

//Icon
import 'remixicon/fonts/remixicon.css';

import '@vueup/vue-quill/dist/vue-quill.snow.css';

const app = createApp(App)
app.use(router)
app.use(i18n)
app.mount('#app')
