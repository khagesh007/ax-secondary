require('./bootstrap')
import { createApp } from 'vue'
import Index from './pages/quiz/index.vue'
import useGlobalHelpers from './composables/helpers/global'
const quizApp = createApp({})
import moment from 'moment/moment';





if (document.getElementById('quizIndex')) {
    quizApp.component('index', Index)
    quizApp.mixin(useGlobalHelpers)
    quizApp.use(moment)
    quizApp.mount('#quizIndex')
}
