require('./bootstrap')
import { createApp } from 'vue'
import 'bootstrap/dist/js/bootstrap.min.js'
import DealIndex from './pages/deals/Index'
import dealCreateForm from './pages/widgets/listings/dealCreateForm.vue'
import listingIndexWidget from './pages/widgets/listings/index.vue'
import useGlobalHelpers from './composables/helpers/global'
const vueWidgetApp = createApp({})
import Multiselect from 'vue-multiselect'
import "vue-select/dist/vue-select.css";
import vSelect from "vue-select";
import moment from 'moment/moment';





if (document.getElementById('vueWidgetApp')) {
    vueWidgetApp.component('listingWidgetIndex', listingIndexWidget)
    vueWidgetApp.mixin(useGlobalHelpers)
    vueWidgetApp.component('multiselect', Multiselect)
    vueWidgetApp.component('v-select', vSelect)
    vueWidgetApp.use(moment)
    vueWidgetApp.mount('#vueWidgetApp')
}
