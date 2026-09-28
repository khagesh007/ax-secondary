require('./bootstrap')
import { createApp } from 'vue'
//import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.min.js'
import Welcome from './components/Welcome'
import DealIndex from './pages/deals/Index'
import DealWizard from "./pages/deals/Wizard.vue";
import DocumentsIndex from "./pages/documents/Index.vue";
import useGlobalHelpers from './composables/helpers/global'
const app = createApp({})
const dealsApp = createApp({})
const dealsWizardApp = createApp({})
const fileManagerApp = createApp({})
import { createPinia } from 'pinia'
const pinia = createPinia()
import Multiselect from 'vue-multiselect'
//import 'bootstrap/dist/css/bootstrap.css'
import "vue-select/dist/vue-select.css";
//import "/assets/deal-wizard/assets/css/bd-wizard.css";
app.component('welcome', Welcome)

// check if #app element exists
// if (document.getElementById('app')) {
//     app.mount('#app')
// }

import moment from 'moment/moment';

import { createRouter, createWebHistory } from 'vue-router';

import routes from "./routes/routes";
import dealRoutes from "./routes/dealRoutes";
const allRoutes = [...routes, ...dealRoutes];
const router = createRouter({
    history: createWebHistory(),
    routes: allRoutes
  });



if (document.getElementById('dealWizardPage')) {
    dealsWizardApp.use(pinia)
   // dealsWizardApp.component('multiselect', Multiselect)
    dealsWizardApp.component('index', DealWizard)
    dealsWizardApp.mixin(useGlobalHelpers)
    dealsWizardApp.mount('#dealWizardPage')
}
if (document.getElementById('dealsApp')) {
    dealsApp.use(pinia)
    dealsApp.use(router)
    dealsApp.use(DatePick, {})
    dealsApp.component('deals', DealIndex)
    dealsApp.mixin(useGlobalHelpers)
    dealsApp.mount('#dealsApp')
}
if (document.getElementById('fileManager')) {
    fileManagerApp.use(pinia)
    fileManagerApp.use(router)
    fileManagerApp.use(moment)
    fileManagerApp.component('Documents', DocumentsIndex)
    fileManagerApp.mixin(useGlobalHelpers)
    fileManagerApp.mount('#fileManager')
}
