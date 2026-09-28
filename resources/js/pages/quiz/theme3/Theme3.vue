<template>
    <div class="wrapper">
        <loading v-model:active="isLoading"
                 :can-cancel="true"
                />
        <!-- Top content -->
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="logo_area ps-5 pt-5">
                        <a href="/">
                            <img src="../../../../../public/assets/images/logo/logo140.png" alt="image-not-found">
                        </a>
                    </div>
                </div>
                <div class="col-6 d-none d-md-block pt-5">
                    <div class="count_box pe-3 me-5 rounded-pill d-flex align-items-center justify-content-center float-end">
                        <div class="count_clock ps-2">
                            <img  src="../../../../../public/theme/quiz/theme3/assets/images/clock.png" alt="image-not-found">
                        </div>
                        <div class="count_title">
                            <h4 class="ps-1">Next</h4>
                            <span class="px-1">Question in</span>
                        </div>
                        <div class="count_number rounded-pill px-3 d-flex justify-content-around align-items-center position-relative overflow-hidden " data-countdown="2022/10/24">

<!--                                <div  class="count_hours">-->
<!--                                    <h3>{{ countdown.hours }}</h3>-->
<!--                                    <span class="text-uppercase">hrs</span>-->
<!--                                </div>-->
                                <div v-if="countdown" class="count_min">
                                    <h3>{{ countdown.minutes }}</h3>
                                    <span class="text-uppercase">min</span>
                                </div>
                                <div v-if="countdown" class="count_sec">
                                    <h3>{{ countdown.seconds }}</h3>
                                    <span class="text-uppercase">sec</span>
                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <form class="multisteps_form overflow-hidden position-relative" id="wizard" method="get" :action="action_url">
                <!------------------------- Step-1 ----------------------------->
                <div v-if="showQuestions" class="multisteps_form_panel"

                     v-for="(qstn, index) in questions" :key="index"
                   :id="'questionSec'+index"
                >
                    <div class="question_title">

                        <h4 class="text-center py-5 animate__animated animate__fadeInRight animate_25ms">{{qstn.question}}</h4>
                    </div>
                    <div class="text-center">
                        <div class="row">
                            <div class="col-12">
                                <div class="question_image animate__animated animate__fadeInRight animate_25ms">
                                    <a :href="qstn.image_url" target="_blank">
                                    <img v-if="qstn.image_url" :src="qstn.image_url" alt="image-not-found"
                                    style="width: 200px;"
                                    >
                                    </a>
                                    <video v-if="qstn.video_url" controls
                                    style="width: 400px;"
                                    >
                                        <source :src="qstn.video_url" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                            <iframe v-if="qstn.youtube_url" width="560" height="315" :src="`quiz/youtube/iframe/?video_url=`+qstn.youtube_url" title="YouTube video player" frameborder="0"></iframe>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-center text-danger" v-if="isTimeup">Time is up. You will see next question in 5 second.</h5>
                            </div>
                        </div>
                      <div>
                    <div class="row rowLeftMargin pt-3">

                            <div  class="col-6 mt-3 option position-relative step_1 d-inline-block animate__animated animate__fadeInRight animate_50ms"
                                 v-for="(option, index2) in qstn.options" :key="index2"
                                  :id="'opt_'+qstn.id+''+option.id"
                                  @click="selectOption(qstn, option, 'opt_'+qstn.id+''+option.id)"
                            >
                                <input  v-model="qstn.answer"  type="radio" :name="'answer'+index" :value="option.id">
                                <label :for="'opt_'+qstn.id+''+option.id"> {{ option.text }}</label>
                                <span class="position-absolute">{{ option.value }}</span>
                            </div>


                    </div>
                </div>
                    </div>

                    <div class="row">

                        <!-- Progress bar -->
                        <div class="step_progress position-absolute text-center step">
                            <span class="text-capitalize">question {{currentQuestionNumber+1}} / {{questions.length}}</span>
                            <div class="progress rounded-pill d-none">
                                <div id="myBar" class="progress-bar rounded-pill" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!---------- Form Button ---------->
                <div>
                <button type="button" class="f_btn prev_btn text-uppercase position-absolute" id="prevBtn"
                        @click="submitAnswer(true)"
                ><span>
                <i class="fa-solid fa-angles-right"></i>
                </span> Skip</button>
                <button type="button"
                v-if="showContinueBtn"
                class="f_btn next_btn text-uppercase position-absolute"  @click="continueToNext()">Continue</button>
                <button type="button"
                v-if="!isTimeup && showNextBtn"
                class="f_btn next_btn text-uppercase position-absolute" id="nextBtn" @click="submitAnswer()">Next Question</button>
            </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import {ref,onMounted, onBeforeMount, watch} from "vue";
import '../../../../../public/theme/quiz/theme3/assets/js/bootstrap.min.js';
import '../../../../../public/theme/quiz/theme3/assets/js/jquery-3.6.0.min.js';
import '../../../../../public/theme/quiz/theme3/assets/js/script.js';
import axios from "axios";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
const currentQuestionNumber = ref(0);
const currentQuestion = ref({});
const showQuestions = ref(false);
const selectedAnswer = ref('');
const isLoading = ref(true);
const showContinueBtn = ref(false);
const timerSkipped = ref(false);
const questions = ref([]);
const question = ref(questions[currentQuestionNumber.value]);
var timer = null;
const finalDate = new Date(Date.now() + 1000 * 60 * 60 * 24);
const isTimeup = ref(false);
const showNextBtn = ref(true);
const submitOnTimeup = ref(true);
const countdown = ref({
    hours: 0,
    minutes: 0,
    seconds: 0
});
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
const action_url = 'quiz/feedback?attempt='+props.attempt.id;
const onCountdownEnd = async () => {
    isTimeup.value = true;
     //setTimeout( async () => {
    if(submitOnTimeup.value === true) {
        await submitAnswer(true);
    }
//}, 5000);
}
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
    // if(reset === true){
    //     clearInterval(timer);
    //     startCountdown(durationInSeconds,false);
    // }else{
    //     return timer; // Return the timer ID
    // }


}
const destroyTimer = () =>{
    timer.stop();
}
const selectOption = (question, option, elementId) => {
    question.answered = true;
    question.answer = option.id;

    selectedAnswer.value = option;
    console.log(selectedAnswer.value.id)
    var optionsElements = document.querySelectorAll('.option');
    optionsElements.forEach(function(element) {
        element.classList.remove('active');
    });
    document.getElementById(elementId).classList.add('active');

}
const  fixStepIndicator = (n) =>{
    // This function removes the "active" class of all steps...
    var i, x = document.getElementsByClassName("step");
    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");
    }
    //... and adds the "active" class to the current step:
    x[n].className += " active";
}

const showTab = (n) =>{
    showNextBtn.value = true;
    // This function will display the specified tab of the form ...
    var x = document.getElementsByClassName("multisteps_form_panel");
    var questionSec = document.getElementById('questionSec'+n);
    //check if questionSec is null
    if(questionSec != null){
        questionSec.style.display = "block";
    }
    if(x.length == 0){
        return;
    }
    x[n].style.display = "block";
    // ... and fix the Previous/Next buttons:
    if (n == 0) {
        //this will hide the previous button on the first question
        //document.getElementById("prevBtn").style.display = "none";
    } else {
        document.getElementById("prevBtn").style.display = "inline";
    }
    // if (n == (x.length - 1)) {
    //     document.getElementById("nextBtn").innerHTML = "Submit";
    // } else {
    //     document.getElementById("nextBtn").innerHTML = "Next Question" + ' <span><i class="fas fa-arrow-right"></i></span>';
    // }
    // ... and run a function that displays the correct step indicator:
    fixStepIndicator(n)
}
// watch(isTimeup, (newVal, oldVal) => {
//    if(newVal === true){
//        document.getElementById("nextBtn").disabled = true;
//    }else{
//          document.getElementById("nextBtn").disabled = false;
//    }
// })
const continueToNext = () => {
    timerSkipped.value = true;
    nextPrev(1,onCountdownEnd);
    showContinueBtn.value = false;
}
const submitAnswer = async (skipped = false) => {
//disable next button
showNextBtn.value = false;
    isTimeup.value = false;
    try {
        const response = await axios.post('/quiz/answer/save', {
            quiz_id: props.quiz.id,
            attempt_id: props.attempt.id,
            answer: currentQuestion.value.answered ? currentQuestion.value.answer : null,
            question_id: currentQuestion.value.id,
            skipped: skipped
        });

        let data = response.data;
        let correct_answer = data.correct_answer;
        let selectedAnsId = selectedAnswer.value.id;
        let timeout = 1000;
        let currentQstnId = currentQuestion.value.id;
        let selectedElement = 'opt_'+currentQstnId+''+selectedAnsId;
        if(skipped === true){ //when question is skipped
            //document.getElementById(`opt_${currentQuestion.value.id}${selectedAnswer.value.id}`).classList.remove('active');
            var element = document.getElementById(selectedElement);
            submitOnTimeup.value = false;
            if (element && element.classList.contains('active')) {
                console.log('Element found')
                element.classList.remove('active');
                element.classList.add('wrong');
            }else{
                console.log('element not found')
            }
            // nextPrev(1,onCountdownEnd);
            // return;
            timeout = 5000;
        }else{
            submitOnTimeup.value = true;
        }

       if(data.status == 2){
           console.log('wrong answer')
            //when user submitted answer is incorrect

                // document.getElementById('opt_'+currentQuestion.value.id+selectedAnsId).classList.add('wrong');
           var element1 = document.getElementById(selectedElement);
           submitOnTimeup.value = false;
           if (element1 && element1.classList.contains('active')) {

               element1.classList.remove('active');
               element1.classList.add('wrong');
           }else{
               submitOnTimeup.value = true;
           }

                document.getElementById('opt_'+currentQuestion.value.id+correct_answer.id).classList.add('active');
                timeout = 7000;
       }
        document.getElementById('opt_'+currentQuestion.value.id+correct_answer.id).classList.add('active');
        showContinueBtn.value = true;
      setTimeout(() => {
        if(timerSkipped.value === false){
            nextPrev(1,onCountdownEnd);
        }
          timerSkipped.value = false;
        }, timeout);
        selectedAnswer.value = '';
    } catch (error) {
        console.error(error);
    }
}
const nextPrev = (n, onCountdownEnd) => {
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("multisteps_form_panel");
    if(x.length == 0){
        return;
    }
    x[currentQuestionNumber.value].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentQuestionNumber.value = currentQuestionNumber.value + n;

    if (currentQuestionNumber.value == x.length) {

         window.location.replace(action_url);
        // document.getElementById("wizard").submit();
        return false;
    }

    // if you have reached the end of the form... :


    // Otherwise, display the correct tab:
    currentQuestion.value = questions.value[currentQuestionNumber.value];

   // startCountdown(currentQuestion.value.time,true);
    clearInterval(timer);
    startCountdown(currentQuestion.value.time,onCountdownEnd,false);
    submitOnTimeup.value = true;
    isTimeup.value = false;
    showTab(currentQuestionNumber.value);

}

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
           currentQuestion.value = questions.value[currentQuestionNumber.value];
            showTab(currentQuestionNumber.value);
            startCountdown(currentQuestion.value.time,onCountdownEnd,false);
        }, 1000);

       // nextPrev(0)
    } catch (error) {
        console.error(error);
    }
}
// const getAttemptData = async () => {
//     try {
//         let inputs = {
//             quiz_id: props.quiz.id,
//             attempt_id: props.attempt.id}
//         const response = await axios.post('/quiz/attempt/data', inputs);
//         let alreadyAttempted = response.data.attempted_question_count;
//         currentQuestionNumber.value = alreadyAttempted;

//             showTab(currentQuestionNumber.value);
//             currentQuestion.value = questions.value[currentQuestionNumber.value];
//             startCountdown(currentQuestion.value.time,onCountdownEnd,false);
//     } catch (error) {
//         console.error(error);
//     }
// }
onBeforeMount(() => {
    setTimeout(() => {
                   isLoading.value = false;
                }, 3000)
    getQuestions();
})
onMounted(() => {


})
</script>
<style scoped>
.count_box {
    max-width: 25rem;
    min-height: 5.625rem;
    background: -webkit-gradient(linear, left top, right top, from(#18f9bd), to(#0ab84b));
    background: linear-gradient(to right, #ffeee6, #d3a188);
    font-family: "Jost", sans-serif;
}
.rowLeftMargin{
    margin-left: 14%;
}
.row .option {

    width: 40%;
    color: #464646;
    font-size: 1rem;
    margin-right: 1.5625rem;
    padding: 1rem 1.5rem;
    font-weight: 800;
    cursor: pointer;
    border: 2px solid #d2d2d2;
    border-radius: 0.3125rem;
}
.row .option label {
    float: left;
    padding-left: 2.375rem;
}
.row .option span {
    top: 50%;
    left: 27px;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    z-index: 1;
}
.row .option:last-child {
    margin-right: 0;
}
.row .option label {
    cursor: pointer;
}
.row .option input {
    display: none;
}
.row .option:after {
    content: "";
    top: 50%;
    left: 15px;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    position: absolute;
    height: 2.5rem;
    width: 2.5rem;
    border-radius: 0.3125rem;
    background-color: #def7e5;
}
.row .option:before {
    content: "";
    top: 50%;
    left: 24px;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    font-size: 1.4375rem;
    line-height: 1;
    font-weight: 400;
    display: none;
    color: #fff;
    z-index: 1;
    font-family: "Font Awesome 5 Pro";
    position: absolute;
}
.row .option.active {
    color: #22a247;
    border: 2px solid #22a247;
    border-bottom: 3px solid #22a247;
    border-radius: 0.3125rem;
    background-color: #def7e5;
}
.row .option.active span {
    display: none;
}
.row .option.active:after {
    background: -webkit-gradient(linear, left top, right top, from(#46fac8), to(#0aba4f));
    background: linear-gradient(to right, #46fac8, #0aba4f);
}
.row .option.active:before {
    display: block;
    z-index: 2;
}
.row .option.active {
    color: #22a247;
    border: 2px solid #22a247;
    border-bottom: 3px solid #22a247;
    border-radius: 0.3125rem;
    background-color: #def7e5;
}
.row .option.active span {
    display: none;
}
.row .option.active:after {
    background: -webkit-gradient(linear, left top, right top, from(#46fac8), to(#0aba4f));
    background: linear-gradient(to right, #46fac8, #0aba4f);
}
.row .option.active:before {
    display: block;
    z-index: 2;
}
.d-none{
    display: none;
}
.count_number {
    width: 9rem;
    height: 4.5rem;
    background-color: #ffffff;
    margin-right: -0.4rem;
}
.count_number .count_hours:after, .count_number .count_min:after, .count_number .count_sec:after {
    content: "";
    width: 0.1875rem;
    height: 2.5625rem;
    top: 50%;
    right: 70px;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    position: absolute;
    background-color: #e1e1e1;
}
.count_number .count_hours:before, .count_number .count_min:before, .count_number .count_sec:before {
    content: "";
    width: 0;
    height: 2.5625rem;
    top: 50%;
    left: 80px;
    -webkit-transform: translateY(-50%);
    transform: translateY(-50%);
    position: absolute;
    background-color: #e1e1e1;
}
.row .option.wrong {
    color: #e72424;
    border: 2px solid #e02121;
    border-bottom: 3px solid #e21b1b;
    border-radius: 0.3125rem;
    background-color: #ffffff;
}
.multisteps_form_panel{
    height: 100vh;
}
.count_box {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 999;
   opacity: 0.6;

}
.vl-overlay .vl-background {
    opacity: 0.8 !important;
}
</style>
