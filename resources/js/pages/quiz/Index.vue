<template>
    <div>

        <component :is="theme === 'theme0' ? Theme0 : null" :quiz="quiz" :attempt="attempt" ></component>
        <component :is="theme === 'theme1' ? Theme1 : null" :quiz="quiz" :attempt="attempt" ></component>
        <component :is="theme === 'theme2' ? Theme2 : null" :quiz="quiz" :attempt="attempt"></component>
        <component :is="theme === 'theme3' ? Theme3 : null" :quiz="quiz" :attempt="attempt"></component>
        <component :is="theme === 'theme4' ? Theme4 : null" :quiz="quiz" :attempt="attempt"></component>
        <component :is="theme === 'theme5' ? Theme5 : null" :quiz="quiz" :attempt="attempt"></component>
        <component :is="theme === 'theme6' ? Theme6 : null" :quiz="quiz" :attempt="attempt"></component>
        <component :is="theme === 'theme7' ? Theme7 : null" :quiz="quiz" :attempt="attempt"></component>
        <component :is="theme === 'theme8' ? Theme8 : null" :quiz="quiz" :attempt="attempt"></component>
    </div>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import Theme1 from '../../pages/quiz/theme1/Index.vue';
import Theme2 from '../../pages/quiz/theme2/Theme2.vue';
import Theme3 from "./theme3/Theme3.vue";
import Theme4 from "./theme4/Theme4.vue";
import Theme5 from "./theme5/Theme5.vue";
import Theme6 from "./theme6/Theme6.vue";
import Theme7 from "./theme7/Theme7.vue";
import Theme8 from "./theme8/Theme8.vue";
import Theme0 from "./theme0/Theme0.vue";
const theme = ref(new URLSearchParams(window.location.search).get('theme'));

const props = defineProps(
    {
        quiz: {
            type: Object,
            required: true
        },
        attempt: {
            type: Object,
            required: true
        }
    })
const loadCSS = (url) =>{
    //this custom css loader is created to prevent css leak to other themes when switching between themes scoped style wasn't working
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = url;
    document.head.appendChild(link);
}
const loadJS = (url) => {
    // Create a script element
    const script = document.createElement('script');
    // Set the src attribute to the provided URL
    script.src = url;
    // Append the script element to the document body
    document.body.appendChild(script);
};
// const loadExternalCSS = (url) =>{
//     const link = document.createElement('link');
//     link.rel = 'stylesheet';
//     link.href = url;
//     link.onload = () => {
//         // Once Font Awesome CSS is loaded, you can use its classes
//         console.log('Font Awesome CSS loaded successfully.');
//     };
//     link.onerror = (error) => {
//         console.error('Error loading Font Awesome CSS:', error);
//     };
//     document.head.appendChild(link);
// }
onMounted(() => {
   // loadExternalCSS('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css')
    loadJS("../../../../../theme/quiz/theme1/assets/js/jquery-3.6.0.min.js")
 if (theme.value === 'theme0') {
    loadCSS("../../../../../../theme/quiz/theme1/assets/css/animate.min.css");

}else if (theme.value === 'theme1') {
        loadCSS("../../../../../../theme/quiz/theme1/assets/css/bootstrap.min.css");
        loadCSS("../../../../../../theme/quiz/theme1/assets/css/animate.min.css");
        loadCSS("../../../../../../theme/quiz/theme1/assets/css/style.css");
        loadCSS("https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap");

        loadJS("../../../../../theme/quiz/theme1/assets/js/bootstrap.min.js")
        loadJS("../../../../../theme/quiz/theme1/assets/js/script.js")

    } else if (theme.value === 'theme2') {
        loadCSS("../../../../../../theme/quiz/theme2/assets/css/bootstrap.min.css");
        loadCSS("../../../../../../theme/quiz/theme2/assets/css/animate.min.css");
        loadCSS("../../../../../../theme/quiz/theme2/assets/css/style.css");
        loadCSS("https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800;900&display=swap");

        loadJS("../../../../../theme/quiz/theme2/assets/js/bootstrap.min.js")
        loadJS("../../../../../theme/quiz/theme2/assets/js/script.js")
    }else if (theme.value === 'theme3') {
        loadCSS("../../../../../../theme/quiz/theme3/assets/css/all.min.css");
        loadCSS("../../../../../../theme/quiz/theme3/assets/css/bootstrap.min.css");
        loadCSS("../../../../../../theme/quiz/theme3/assets/css/animate.min.css");
        loadCSS("../../../../../../theme/quiz/theme3/assets/css/style.css");
        loadCSS("https://fonts.googleapis.com/css2?family=Jost:wght@500;600;700;800&family=Sen:wght@400;700;800&display=swap");
        loadJS("../../../../../theme/quiz/theme3/assets/js/bootstrap.min.js")
        loadJS("../../../../../theme/quiz/theme3/assets/js/script.js")
    }else if (theme.value === 'theme4') {

        loadCSS("../../../../../../theme/quiz/theme4/assets/css/bootstrap.min.css");
        loadCSS("../../../../../../theme/quiz/theme4/assets/css/animate.min.css");
        loadCSS("../../../../../../theme/quiz/theme4/assets/css/style.css");
        loadCSS("https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700;800;900&display=swap");
        loadCSS("https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css");
        loadJS("../../../../../theme/quiz/theme4/assets/js/bootstrap.min.js")
        loadJS("../../../../../theme/quiz/theme4/assets/js/script.js")
    }else if (theme.value === 'theme5') {
        loadCSS("../../../../../../theme/quiz/theme5/assets/css/bootstrap.min.css");
        loadCSS("../../../../../../theme/quiz/theme5/assets/css/animate.min.css");
        loadCSS("../../../../../../theme/quiz/theme5/assets/css/style.css");
        loadJS("../../../../../theme/quiz/theme5/assets/js/bootstrap.min.js")
        loadJS("../../../../../theme/quiz/theme5/assets/js/script.js")
    }else if (theme.value === 'theme6') {
        loadCSS("../../../../../../theme/quiz/theme6/assets/css/bootstrap.min.css");
        loadCSS("../../../../../../theme/quiz/theme6/assets/css/animate.min.css");
        loadCSS("../../../../../../theme/quiz/theme6/assets/css/style.css");
        loadJS("../../../../../theme/quiz/theme6/assets/js/bootstrap.min.js")
        loadJS("../../../../../theme/quiz/theme6/assets/js/script.js")
    }else if (theme.value === 'theme7') {
        loadCSS("../../../../../../theme/quiz/theme7/assets/css/bootstrap.min.css");
        loadCSS("../../../../../../theme/quiz/theme7/assets/css/animate.min.css");
        loadCSS("../../../../../../theme/quiz/theme7/assets/css/style.css");
        loadJS("../../../../../theme/quiz/theme7/assets/js/bootstrap.min.js")
        loadJS("../../../../../theme/quiz/theme7/assets/js/script.js")
    }else if (theme.value === 'theme8') {
        loadCSS("../../../../../../theme/quiz/theme8/assets/css/bootstrap.min.css");
        loadCSS("../../../../../../theme/quiz/theme8/assets/css/animate.min.css");
        loadCSS("../../../../../../theme/quiz/theme8/assets/css/style.css");
        loadJS("../../../../../theme/quiz/theme8/assets/js/bootstrap.min.js")
        loadJS("../../../../../theme/quiz/theme8/assets/js/script.js")
    }
})
</script>
<style>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css');
</style>
