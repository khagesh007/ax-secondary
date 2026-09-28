<template>

<div class="wrapper position-relative">
    <div class="container-fluid">
        <div class="row py-5">
            <div class="col-md-6">
                <div class="logo_area ps-5">
                    <a href="/">
                        <img src="../../../../../../public/assets/images/logo/logo140.png" alt="image-not-found">
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <!-- Progress- -->
                <div class="count_progress clip-1">
                  <span class="progress-left">
                     <span class="progress_bar"></span>
                  </span>
                    <span class="progress-right">
                     <span class="progress_bar"></span>
                  </span>
                    <div class="progress-value countdown_timer" data-countdown="50">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <form class="multisteps_form" id="wizard" method="POST" action="../thankyou/index-1.html">
                <!------------------------- Step-1 ----------------------------->
                <div class="col-lg-7">
                    <div class="multisteps_form_panels" id="multisteps_form_panels">
                        <div class="question_title pb-4">
                            <h1 class="bg-white rounded-pill position-relative">{{question.question}}
                            </h1>
                            <!-- Step-Progress-bar area -->
                            <div class="step_progress_bar position-absolute">
                                <div class="step position-relative" style="background-color: #f8d5b3;"></div>
                                <div class="step position-relative" style="background-color: #ffe090;"></div>
                                <div class="step position-relative" style="background-color: #f2c7db;"></div>
                                <div class="step position-relative" style="background-color: #bdcaff;"></div>
                            </div>
                        </div>
                        <div class="form_items">
                            <ul class="ms-5 p-0 list-unstyled">
                                <li v-for="(option, index) in question.options" :key="index">
                                    <label :for="'opt_' + index" class="step_1 rounded-pill position-relative animate__animated animate__fadeInRight animate_50ms">
                                        <input type="radio" :id="'opt_' + index" :name="'stp_1_select_option_' + index" :value="option.value">
                                        <span class="text-white">{{ option.value }}</span>
                                        {{ option.text }}
                                        <span :class="tipBackgroundColor[index]"></span>
                                    </label>
                                </li>

                            </ul>
                        </div>
                    </div>
                </div>
                <!------------------------- Step-2 ----------------------------->

                <!---------- Form Button ---------->
                <div class="form_btn position-absolute">
                    <button type="button" class="f_btn prev_btn text-white rounded-pill text-uppercase" id="prevBtn" @click="prevQuestion(question.id)"><span><i class="fas fa-arrow-left"></i></span> Prev Question</button>
                    <button type="button" class="f_btn nextBtn text-white rounded-pill text-uppercase" id="nextBtn" @click="nextQuestion(question.id), move()">Next Question</button>
                </div>
            </form>
        </div>
    </div>
    <!-- progress-bar -->
    <div class="progress position-absolute rounded-pill">
        <div id="myBar" class="progress-bar rounded-pill" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100">25%</div>
    </div>
    <!-- Right-side-img -->
    <div class="right_bottom_img d-none d-lg-block">
        <img class="position-absolute" src="../../../../../../public/theme/quiz/theme1/assets/images/bg_1.png" alt="image-not-found">
    </div>
</div>
</template>

<script setup>
import {onMounted, ref} from 'vue';
//import jquery from 'jquery';
import '../../../../../../public/theme/quiz/theme1/assets/js/bootstrap.min.js';
import '../../../../../../public/theme/quiz/theme1/assets/js/jquery-3.6.0.min.js';
import '../../../../../../public/theme/quiz/theme1/assets/js/script.js';
import '../../../../../../public/theme/quiz/theme1/assets/js/jquery.validate.min.js';
const currentTab = ref(0);
const currentQuestion = ref(0);
const tipBackgroundColor = ref({
    0: 'pinkLady',
    1: 'salomie',
    2: 'wePeep',
    3: 'periwinkle',
    4: 'pinkLady',
    5: 'salomie',
    6: 'wePeep',
});
const questions = [
    {
        id: 1,
        question: "What is the capital of the United Arab Emirates?",
        answered: false,
        options: [
            { text: "Sharjah", value: "A" },
            { text: "Fujairah", value: "B" },
            { text: "Ras Al Khaimah", value: "C" },
            { text: "Abu Dhabi", value: "D" },
        ]
    },
    {
        id: 2,
        question: "Which year was the United Arab Emirates founded?",
        answered: false,
        options: [
            { text: "2021", value: "A" },
            { text: "1990", value: "B" },
            { text: "1980", value: "C" },
            { text: "1971", value: "D" },
        ]
    },
    {
        id: 3,
        question: "How many Emirates make up the United Arab Emirates?",
        answered: false,
        options: [
            { text: "Two", value: "A" },
            { text: "Four", value: "B" },
            { text: "Six", value: "C" },
            { text: "Seven", value: "D" },
        ]
    },
    {
        id: 4,
        question: "What is the size of Dubai?",
        answered: false,
        options: [
            { text: "1,000 km²", value: "A" },
            { text: "4,114 km²", value: "B" },
            { text: "10,000 km²", value: "C" },
            { text: "20,000 km²", value: "D" },

        ]
    },
    {
        id: 5,
        question: "Who is the ruler of Dubai?",
        answered: false,
        options: [
            { text: "Mohammed Bin Rashid Al Maktoum", value: "A" },
            { text: "Joe Biden", value: "B" },
            { text: "Mohammed Bin Zayed", value: "C" },
            { text: "Queen Elizabeth", value: "D" },
        ]
    },
    {
        id: 6,
        question: "What is the currency of the United Arab Emirates?",
        answered: false,
        options: [
            { text: "Ruble", value: "A" },
            { text: "Dirham", value: "B" },
            { text: "Dollar", value: "C" },
            { text: "Euro", value: "D" },
        ]
    },
    {
        id: 7,
        question: "What is the dialing code for the United Arab Emirates?",
        answered: false,
        options: [
            { text: "+1", value: "A" },
            { text: "+44", value: "B" },
            { text: "+971", value: "C" },
            { text: "+77", value: "D" },

        ]
    },
    {
        id: 8,
        question: "What is the official language in the United Arab Emirates?",
        answered: false,
        options: [
            { text: "French", value: "A" },
            { text: "Italian", value: "B" },
            { text: "Urdu", value: "C" },
            { text: "Arabic", value: "D" },
        ]
    },
    {
        id: 9,
        question: "Emirates Airline is the flag carrier for which Emirate in the United Arab Emirates?",
        answered: false,
        options: [
            { text: "Fujairah", value: "A" },
            { text: "Abu Dhabi", value: "B" },
            { text: "Dubai", value: "C" },
            { text: "Sharjah", value: "D" },
        ]
    }
];


const question = ref(questions[currentQuestion.value]);
const nextQuestion = (n) => {
    var totalQuestions = questions.length;
    if (currentQuestion.value >= totalQuestions) {
        return;
    }
    currentQuestion.value += 1;
    question.value = questions[currentQuestion.value];
    move();
    showTab(currentTab);
}
const prevQuestion = (n) => {
    currentQuestion.value -= 1;
    question.value = questions[currentQuestion.value];
    move();
}
const  showTab = (n) => {
    // This function will display the specified tab of the form ...
    var qSection = document.getElementById("multisteps_form_panels");
    qSection.style.display = "none";

    fixStepIndicator(n)
    qSection.style.display = "block";
}
const fixStepIndicator = (n) => {

}
const move = () => {
    var totalQuestions = questions.length;
    var currentQuestion = question.id;
    var percent = (currentQuestion / totalQuestions) * 100;

    var elem = document.getElementById("myBar");
    var width = parseInt(elem.style.width);
    var aim = percent;
    var id = setInterval(frame, 25);

    function frame() {
        if (width >= aim) {
            clearInterval(id);
        } else if (width >= 100) {
            width = 0;
            aim = percent;
            elem.style.width = width + '%';
            elem.innerHTML = width * 1 + '%';
        } else {
            width++;
            elem.style.width = width + '%';
            elem.innerHTML = width * 1 + '%';
        }
    }
}
onMounted(() => {
   // showTab(currentTab); // Display the current tab
});
</script>
<style scoped>


</style>
