require('./bootstrap');

import { createApp } from 'vue';
import { createVuetify } from 'vuetify';
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

import Index from './pages/admin-dashboard/Index.vue';
import useGlobalHelpers from './composables/helpers/global';

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

const app = createApp(Index);
app.mixin(useGlobalHelpers);
app.component('VueDatePicker', VueDatePicker);
app.component('v-chart', ECharts);
app.use(vuetify);
app.mount("#admin-dashboard");
