require('./bootstrap');

import { createApp } from 'vue';
import { createVuetify } from 'vuetify';
import { createRouter, createWebHashHistory } from 'vue-router';
import 'vuetify/styles'; // Import Vuetify styles
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'
import '@popperjs/core'; // Import Popper.js
import 'bootstrap/dist/js/bootstrap.bundle.min.js'; // Import Bootstrap JS

// Vuetify components & icons
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { aliases, mdi } from 'vuetify/iconsets/mdi';
import ECharts from "vue-echarts";
import "echarts";
import Index from './pages/pipeline/Dashboard.vue';
import Form from './pages/pipeline/Form.vue';
import useGlobalHelpers from './composables/helpers/global';

// Define routes
const routes = [
  { path: '/', name: 'dashboard', component: Index },
  { path: '/form', name: 'form', component: Form },
];
// Create router with base path
const router = createRouter({
  history: createWebHashHistory(), // Changed to hash mode
  routes,
});
// Create Vuetify instance
const vuetify = createVuetify({
    components,
    directives,
    icons: {
        defaultSet: 'mdi',
        aliases,
        sets: { mdi }
    },
    theme: {
        defaultTheme: 'light', // Ensure a default theme is set
    },
});
const App = {
  template: '<router-view></router-view>'
};
const app = createApp(App);
app.mixin(useGlobalHelpers);
app.component('VueDatePicker', VueDatePicker);
app.component('v-chart', ECharts);
app.use(vuetify);
app.use(router);
app.mount("#pipelinePage");
