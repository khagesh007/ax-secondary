<template>
 <loading v-model:active="isLoading"
                 :can-cancel="true"/>
    <div class="wrapper position-relative">
        <div class="container-fluid">
            <div class="row py-5">
                <div class="col-md-6">
                    <div class="logo_area ps-5">
                        <a href="/">
                            <img src="../../../../../public/assets/images/logo/logo140.png" alt="image-not-found">
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Progress- -->
                    <div class="count_progress count_box clip-1">

                        <div class="progress-value countdown_timer">
                            {{ countdown.minutes }}:{{ countdown.seconds }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                    <h4 class="text-center text-danger" v-if="isTimeup">Time is up. You will see next question in 5 second.</h4>
                <form class="multisteps_form" id="wizard" method="POST" :action="action_url">
                    <!------------------------- Step-1 ----------------------------->
                    <span v-if="!showQuestions" class="loader"></span>
                    <div v-if="showQuestions" class="col-lg-7">

                        <div  class="multisteps_form_panel"  v-for="(qstn, index2) in questions" :key="index2"
                              :id="'questionSec'+index2"
                        >
                            <div class="question_title pb-4">
                                <h3 class="bg-white rounded-pill position-relative">{{qstn.question}}
                                </h3>

                                <!-- Step-Progress-bar area -->
                                <div class="step_progress_bar position-absolute">
                                    <div class="step position-relative"
                                    v-for="(option, index) in qstn.options" :key="index"
                                    style="background-color: #f8d5b3;"></div>
                                    <!-- <div class="step position-relative" style="background-color: #ffe090;"></div>
                                    <div class="step position-relative" style="background-color: #f2c7db;"></div>
                                    <div class="step position-relative" style="background-color: #bdcaff;"></div> -->
                                </div>
                            </div>
                            <div class="form_items">
                                <div class="row">
                                    <div class="col-6">
                                <ul class="ms-5 p-0 list-unstyled">
                                    <li v-for="(option, index) in qstn.options" :key="index">
                                        <label :for="'opt_'+qstn.id+''+option.id" @click="theme1OptionSelected('step_' + index+index2),selectOption(qstn, option, 'opt_'+qstn.id+''+option.id)"
                                               :id="'opt_'+qstn.id+''+option.id+'label'"
                                               :class="'step_' + index+''+index2+' them1Options  rounded-pill position-relative animate__animated animate__fadeInRight animate_50ms'">
                                            <input v-model="qstn.answer" type="radio" :id="'opt_'+qstn.id+''+option.id" :name="'stp_1_select_option_' + index" :value="option.id">
                                            <span class="text-white">{{ option.value }}</span>
                                            {{ option.text }}
                                            <span :class="tipBackgroundColor[index]"></span>
                                        </label>
                                    </li>

                                </ul>

                                <div class="d-flex justify-content-between d-none">
                                    <img src="https://fnst.axflare.com/assets/public/img/sell/buy-sell_buy.webp" />
                                </div>
                                </div>
                                    <div class="col-6">

                                        <div class="question_image"
                                        style="position: absolute;
    right: 40px;
    top: 30%;"
                                        >
                                            <a :href="qstn.image_url" target="_blank">
                                                <img v-if="qstn.image_url" :src="qstn.image_url" alt="image-not-found"
                                                     style="width: 200px;"
                                                >
                                            </a>
                                            <video v-if="qstn.video_url" controls
                                                   style="width: 350px;"
                                            >
                                                <source :src="qstn.video_url" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                                <div class="video-container">

                                                </div>
                                                    <iframe class="responsive-iframe" v-if="qstn.youtube_url" width="560" height="315" :src="`quiz/youtube/iframe/?video_url=`+qstn.youtube_url" title="YouTube video player" frameborder="0"></iframe>
                                        </div>
                                    </div>

                            </div>
                            </div>

                        </div>
                    </div>
                    <!------------------------- Step-2 ----------------------------->

                    <!---------- Form Button ---------->
                    <div v-if="showQuestions && showButtons" class="form_btn position-absolute">

                        <button type="button" class="f_btn nextBtn text-white rounded-pill text-uppercase" id="prevBtn" @click="submitAnswer(true)">Skip</button>
                        <button type="button"
                        v-if="!isTimeup"
                        class="f_btn nextBtn text-white rounded-pill text-uppercase" id="nextBtn" @click="submitAnswer()">Submit</button>

                    </div>
                    <div  v-if="showContinueBtn" class="form_btn position-absolute">
                    <button type="button"

                        class="f_btn nextBtn text-white rounded-pill text-uppercase"  @click="continueToNext()">Continue</button>
                    </div>

                </form>
            </div>
        </div>
        <!-- progress-bar -->
        <div class=" progress position-absolute rounded-pill">
            <div id="myBar" class="progress-bar rounded-pill" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">{{round(progressBarValue)}}%</div>
        </div>
        <!-- Right-side-img -->
        <!-- <div class="right_bottom_img d-none d-lg-block">
            <img class="position-absolute" src="../../../../../public/theme/quiz/theme1/assets/images/bg_1.png" alt="image-not-found">
        </div> -->
    </div>
</template>

<script setup>
import {onMounted, ref, watch, onBeforeMount} from 'vue';
//import jquery from 'jquery';
import VueYouTubeEmbed from 'vue-youtube-embed'
// import '../../../../../../public/theme/quiz/theme1/assets/js/bootstrap.min.js';
import '../../../../../public/theme/quiz/theme1/assets/js/jquery-3.6.0.min.js';
// import '../../../../../../public/theme/quiz/theme1/assets/js/script.js';
import '../../../../../public/theme/quiz/theme1/assets/js/jquery.validate.min.js';
import axios from "axios";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import { set } from 'lodash';
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
    const isTimeup = ref(false);
const action_url = 'quiz/feedback?attempt='+props.attempt.id;
const currentTab = ref(0);
const currentQuestion = ref({});
const currentQuestionNumber = ref(0);
const selectedAnswer = ref('');
const progressBarValue = ref(0);
const isLoading = ref(true);
const showContinueBtn = ref(false);
const timerSkipped = ref(false);
var timer = null;
const tipBackgroundColor = ref({
    0: 'pinkLady',
    1: 'salomie',
    2: 'wePeep',
    3: 'periwinkle',
    4: 'pinkLady',
    5: 'salomie',
    6: 'wePeep',
});
const showQuestions = ref(false);
const showButtons = ref(true);
const questions = ref([]);
const countdown = ref({
    hours: '00',
    minutes: '00',
    seconds: '00'
});
const round = (value) => {
    return Math.round(value);
}
const progressbarUpdate = (newValue) =>{

    const progressBar = document.getElementById('myBar');
    const newWidth = newValue + '%';
    progressBarValue.value = newValue;
    // Update aria-valuenow attribute
    progressBar.setAttribute('aria-valuenow', newValue);
    // Update text inside the progress bar
    progressBar.innerText = newValue + '%';
    // Update style width
    progressBar.style.width = newWidth;
}

const question = ref(questions[currentQuestion.value]);
const startCountdown = (durationInSeconds,onCountdownEnd,reset = false) => {
    const finalDate = new Date(new Date().getTime() + (durationInSeconds * 1000)); // Calculate final date

    timer = setInterval(() => {
        const currentDate = new Date();
        const diff = finalDate - currentDate;
        const hours = Math.floor(diff / 1000 / 60 / 60);
        const minutes = Math.floor(diff / 1000 / 60) % 60;
        const seconds = Math.floor(diff / 1000) % 60;

        countdown.value = {
            hours: hours < 10 ? '0' + hours : hours,
            minutes: minutes < 10 ? '0' + minutes : minutes,
            seconds: seconds < 10 ? '0' + seconds : seconds
        }

        if (diff < 0) {
            clearInterval(timer);
            countdown.value = {
                hours: 0,
                minutes: 0,
                seconds:0
            }

            if (onCountdownEnd && typeof onCountdownEnd === 'function') {
                onCountdownEnd();
            }
        }
    }, 1000);
    if(reset === true){
        clearInterval(timer);
        countdown.value = {
            hours: 0,
            minutes: 0,
            seconds:0
        }
        startCountdown(durationInSeconds,onCountdownEnd,false);
    }else{
        return timer; // Return the timer ID
    }


}
function nextPrev(n) {
    showContinueBtn.value = false;
    showButtons.value = true;
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("multisteps_form_panel");
    if(x.length == 0){
        return;
    }

    // Exit the function if any field in the current tab is invalid:
    // if (n == 1 && !validateForm()) return false;
    // Hide the current tab:
    x[currentQuestionNumber.value].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentQuestionNumber.value = currentQuestionNumber.value + n;
    // if you have reached the end of the form... :
    if (currentQuestionNumber.value == x.length) {
        showButtons.value = false;

        var totalQuestions = questions.value.length;
    var currentQn = currentQuestionNumber.value;
    var percent = (currentQn / totalQuestions) * 100;
    progressbarUpdate(percent)

        setTimeout(() => {

        window.location.replace(action_url);
        }, 3000);
        // document.getElementById("wizard").submit();
        return false;
    }
    // if (currentTab >= x.length) {
    //     //...the form gets submitted:
    //     document.getElementById("wizard").submit();
    //     return false;
    // }

    currentQuestion.value = questions.value[currentQuestionNumber.value];
    clearInterval(timer);
    showTab(currentQuestionNumber.value);
    startCountdown(currentQuestion.value.time,onCountdownEnd,false);


}
const prevQuestion = (n) => {
    currentQuestion.value -= 1;
    question.value = questions[currentQuestion.value];
    move();
}
function showTab(n) {

    // This function will display the specified tab of the form ...
    var x = document.getElementsByClassName("multisteps_form_panel");
    var questionSec = document.getElementById('questionSec'+n);
    questionSec.style.display = "block";
    if(x.length == 0){
        return;
    }
    x[n].style.display = "block";
    // ... and fix the Previous/Next buttons:

    //     document.getElementById("prevBtn").style.display = "inline";

    // if (n == (x.length - 1)) {
    //     document.getElementById("nextBtn").innerHTML = "Submit";
    // } else {

    //     document.getElementById("nextBtn").innerHTML = "Next Question" + ' <span><i class="fas fa-arrow-right"></i></span>';
    // }
    // ... and run a function that displays the correct step indicator:
    var totalQuestions = questions.value.length;
    var currentQn = currentQuestionNumber.value;
    var percent = (currentQn / totalQuestions) * 100;
    progressbarUpdate(percent)

}

const move = () => {
    var totalQuestions = questions.length;
    var percent = (currentQuestionNumber.value / totalQuestions) * 100;
    progressbarUpdate(percent)
}
const onCountdownEnd = async () => {
    isTimeup.value = true;
   // setTimeout( async () => {
        await submitAnswer(true);
    //}, 5000);

}
const continueToNext = () => {
    timerSkipped.value = true;
    nextPrev(1,onCountdownEnd);
    showContinueBtn.value = false;
}


const submitAnswer = async (skipped = false) => {

    isTimeup.value = false;
    showButtons.value = false;
    try {
        const response = await axios.post('/quiz/answer/save', {
            quiz_id: props.quiz.id,
            attempt_id: props.attempt.id,
            answer: currentQuestion.value.answered ? currentQuestion.value.answer : null,
            question_id: currentQuestion.value.id,
            skipped: skipped
        });
        showContinueBtn.value = true;
        let data = response.data;
        let timeout = 1000;
        let correct_answer = data.correct_answer;
        if(skipped === true){
            document.getElementById('opt_'+currentQuestion.value.id+correct_answer.id+'label').classList.add('correct');
            // nextPrev(1,onCountdownEnd);
            // return;
            timeout = 5000;
        }
        if(data.status == 2){

            let selectedAnsId = selectedAnswer.value.id;

            document.getElementById('opt_'+currentQuestion.value.id+selectedAnsId+'label').classList.add('wrong');
            document.getElementById('opt_'+currentQuestion.value.id+selectedAnswer.value.id).classList.remove('active');

            timeout = 7000;
        }
        document.getElementById('opt_'+currentQuestion.value.id+correct_answer.id+'label').classList.add('correct');

        setTimeout(() => {
            if(timerSkipped.value === false){
            nextPrev(1,onCountdownEnd);
        }
          timerSkipped.value = false;

           // move();
        }, timeout);

    } catch (error) {
        console.error(error);
    }
}
const theme1OptionSelected = (step) =>{

    const options = document.querySelectorAll(".them1Options");
    // Loop through each option
    options.forEach(function(option) {
        // Remove "active" class from all options
        option.classList.remove("active");
    });
    // Add "active" class to the clicked option

    document.querySelector('.'+step).classList.add("active");
}
const selectOption = (question, option, elementId) => {

    question.answered = true;
    question.answer = option.id;
    selectedAnswer.value = option;
    currentQuestion.value = question;
    var optionsElements = document.querySelectorAll('.option');
    optionsElements.forEach(function(element) {
        element.classList.remove('active');
    });
    document.getElementById(elementId).classList.add('active');

}
const them1handleClick = () => {
    document.addEventListener('click', function(event) {
        const clickedOption = event.target.closest('.them1Options');
        if (!clickedOption) return; // If clicked element doesn't have class "step_1", do nothing

        const options = document.querySelectorAll(".them1Options");

        // Loop through each option
        options.forEach(function(option) {
            // Remove "active" class from all options
            option.classList.remove("active");
        });

        // Add "active" class to the clicked option
        clickedOption.classList.add("active");

    });
}
const questionFromDb = ref([]);

const atoz = [
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'
];
const getQuestions = async () => {

    try {
        const response = await axios.get('/quiz/question/all?quiz_id='+props.quiz.id+'&attempt_id='+props.attempt.id);
        let questionsRes = [];
        questionsRes = response.data.data.questions;
        let alreadyAttempted = response.data.data.attempted_question_count;
        questionsRes.forEach((question, index) => {
            questions.value.push({
                id: question.id,
                question: question.question,
                options: question.answers.map((answer, index2) => {

                    return {
                        text: answer.answer,
                        value: atoz[index2],
                        id : answer.id
                    }
                }),
                time: question.time,
                image_url : question.image_url,
                video_url : question.video_url,
                youtube_url : question.youtube_url,
                answered: false,
                answer : ''
            });
        });
        showQuestions.value = true;
        if(questions.value.length <= alreadyAttempted){
            window.location.replace(action_url);
        }
        setTimeout(() => {
            if(alreadyAttempted > 0){
            currentQuestionNumber.value = alreadyAttempted;
           }
            showTab(currentQuestionNumber.value); // Display the current tab
            currentQuestion.value = questions.value[0];
            startCountdown(currentQuestion.value.time,onCountdownEnd);
           // nextPrev(1)
        }, 1000);
    } catch (error) {
        console.error(error);
    }
}
watch( currentQuestion.value, (newVal, oldVal) => {
    // console.log('newVal', newVal);
    // console.log('oldVal', oldVal);
});
onBeforeMount(() => {
    setTimeout(() => {
                   isLoading.value = false;
                }, 3000)
    getQuestions();
})
onMounted(() => {

});
</script>
<style scoped>

.question_title h3 {
    color: #000a38;
    font-size: 1.2rem;
    padding: 1.4rem 6.5rem;
}
.question_title .step_progress_bar {
    top: 60px;
    left: 5.5%;
    -webkit-transform: translateX(-50%);
    transform: translateX(-50%);
}
.form_items label {
    font-size: 1.2rem;
}

/* Loader style */

.loader {
    width: 48px;
    height: 48px;
    display: block;
    margin: 15px auto;
    position: relative;
    color: #ccc;
    box-sizing: border-box;
    animation: rotation 1s linear infinite;
}
.loader::after,
.loader::before {
    content: '';
    box-sizing: border-box;
    position: absolute;
    width: 24px;
    height: 24px;
    top: 50%;
    left: 50%;
    transform: scale(0.5) translate(0, 0);
    background-color: #ccc;
    border-radius: 50%;
    animation: animloader 1s infinite ease-in-out;
}
.loader::before {
    background-color: #000;
    transform: scale(0.5) translate(-48px, -48px);
}

@keyframes rotation {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
@keyframes animloader {
    50% {
        transform: scale(1) translate(-50%, -50%);
    }
}
.wrong{
    background-color: #ff5a5af0 !important;
}
.correct{
    background-color: #9bff9b !important;
}
/* END Loader style */
.count_progress:after{
    width: 0;
}
.count_progress{
    position: fixed;
    opacity: 0.6;
    z-index: 10;
}
count_box {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 999;
   opacity: 0.6;

}
.video-container {
  position: relative;
  width: 100%;
  overflow: hidden;
  padding-top: 0%; /* 16:9 Aspect Ratio */
}

.responsive-iframe {
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  /* height: 100%; */
  border: none;
}
</style>
