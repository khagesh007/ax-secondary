<template>
    <div>
<div class="wrapper position-relative">
<div class="top_content_area pt-2">
   <div class="container-fluid">
      <div class="row">
         <div class="col-md-6">
            <div class="logo_area ms-5">
               <a href="index.html">
                  <img src="../../../../../public/assets/images/logo/logo140.png" alt="image_not_found">
               </a>
            </div>
         </div>
         <div class="col-md-6 d-none d-md-block">
            <div class="count_box overflow-hidden rounded-pill d-flex float-end me-5">
               <div class="count_clock ps-2">
                  <img src="/theme/quiz/theme4/assets/images/counter/clock.png" alt="image_not_found">
               </div>
               <div class="count_title">
                  <h5 class="ps-1">Next</h5>
                  <span class="px-1">Question in</span>
               </div>
               <div class="count_number rounded-pill bg-white overflow-hidden me-2 text-center countdown_timer" data-countdown="2022/10/24">
                <!--                                <div  class="count_hours">-->
<!--                                    <h3>{{ countdown.hours }}</h3>-->
<!--                                    <span class="text-uppercase">hrs</span>-->
<!--                                </div>-->
                <!-- <div v-if="countdown" class="count_min">
                                    <h3>{{ countdown.minutes }}</h3>
                                    <span class="text-uppercase">min</span>
                                </div> -->
                                <div v-if="countdown" class="count_sec">
                                    <h3>{{ countdown.seconds }}</h3>
                                    <span class="text-uppercase">sec</span>
                                </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<div class="container">
   <form class="multisteps_form position-relative overflow-hidden mt-5" id="wizard" method="POST" :action="action_url">
      <!-- Form-header-content -->
      <div class="form_header_content text-center pt-4">
         <h2>AX CAPITAL</h2>

      </div>
      <span class="question_number text-uppercase mb-3 float-end">QUESTION {{currentQuestionNumber+1}} / {{questions.length}}</span>
         <div class="progress rounded-pill">
            <div id="myBar" class="progress-bar" role="progressbar" style="width: 25%;" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
         </div>
        <div v-if="showQuestions"
          v-for="(qstn, index) in questions" :key="index"
                   :id="'questionSec'+index"
        class="multisteps_form_panel step">
         <!-- Form-content -->

         <h1 class="question_title px-5 py-3 animate__animated animate__fadeInRight animate_25ms">{{qstn.question}}</h1>
            <!-- Form-items -->
            <div class="form_items ps-5">
               <ul class="list-unstyled p-0">
                  <li class=" step_1 ps-5 option rounded-pill animate__animated animate__fadeInRight animate_50ms"
                  v-for="(option, index2) in qstn.options" :key="index2"
                                  :id="'opt_'+qstn.id+''+option.id"
                                  @click="selectOption(qstn, option, 'opt_'+qstn.id+''+option.id)"
                  >
                     <input  v-model="qstn.answer" type="radio" :name="'answer'+index" :value="option.id" value="Activate Developer Tab">
                     <label :for="'opt_'+qstn.id+''+option.id">{{ option.text }}</label>
                  </li>

               </ul>
            </div>
         </div>
        </form>
    </div>
   <!---------- Form Button ---------->
   <div class="py-5">
   <div v-if="showQuestions && showButtons" class="form_btn  text-center">
      <button type="button" class="f_btn disable text-uppercase rounded-pill text-white" @click="submitAnswer(true)">Skip</button>
      <button type="button" class="f_btn active text-uppercase rounded-pill text-white" id="nextBtn" @click="submitAnswer()"> Next Question <i class="fas fa-arrow-right"></i></button>
   </div>
   <div v-if="showQuestions && showContinueBtn" class="form_btn text-center">
    <button type="button"
                class="f_btn active text-uppercase rounded-pill text-white"  @click="continueToNext()">Continue</button>
   </div>
</div>
</div>
</div>
</template>
<script setup>
import {ref,onMounted, onBeforeMount, watch} from "vue";
import axios from "axios";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
const isLoading = ref(true);
const showQuestions = ref(false);
const showButtons = ref(true);
const showContinueBtn = ref(false);
const selectedAnswer = ref('');
const progressBarValue = ref(0);
const questions = ref([]);
const currentQuestion = ref({});
const currentQuestionNumber = ref(0);
const question = ref(questions[currentQuestionNumber.value]);
const isTimeup = ref(false);
const submitOnTimeup = ref(true);
const showNextBtn = ref(true);
const timerSkipped = ref(false);
const action_url = 'quiz/feedback?attempt='+props.attempt.id;
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
    const atoz = [
    'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'
];
var timer = null;
    const countdown = ref({
    hours: 0,
    minutes: 0,
    seconds: 0
});
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
const onCountdownEnd = async () => {
    isTimeup.value = true;
     //setTimeout( async () => {
    if(submitOnTimeup.value === true) {
        await submitAnswer(true);
    }
//}, 5000);
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
const selectOption = (question, option, elementId) => {
    question.answered = true;
    question.answer = option.id;

    selectedAnswer.value = option;
    var optionsElements = document.querySelectorAll('.option');
    optionsElements.forEach(function(element) {
        element.classList.remove('active');
    });
    document.getElementById(elementId).classList.add('active');

}
const continueToNext = () => {
    timerSkipped.value = true;
    nextPrev(1,onCountdownEnd);
    showContinueBtn.value = false;
}
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
const fixStepIndicator = (n) => {
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
     //   document.getElementById("prevBtn").style.display = "inline";
    }
    fixStepIndicator(n)
    var totalQuestions = questions.value.length;
    var currentQn = currentQuestionNumber.value;
    var percent = (currentQn / totalQuestions) * 100;
    progressbarUpdate(percent)
}
function nextPrev(n, onCountdownEnd) {
    showContinueBtn.value = false;
    showButtons.value = true;
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("multisteps_form_panel");
    if(x.length == 0){
        return;
    }
    // Exit the function if any field in the current tab is invalid:

    // Hide the current tab:
    x[currentQuestionNumber.value].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentQuestionNumber.value = currentQuestionNumber.value + n;
    // if you have reached the end of the form... :
    if (currentQuestionNumber.value == x.length) {

        window.location.replace(action_url);
        // document.getElementById("wizard").submit();
        return false;
        }
        currentQuestion.value = questions.value[currentQuestionNumber.value];

// startCountdown(currentQuestion.value.time,true);
 clearInterval(timer);
 startCountdown(currentQuestion.value.time,onCountdownEnd,false);
 submitOnTimeup.value = true;
 isTimeup.value = false;
 showTab(currentQuestionNumber.value);
}
const submitAnswer = async (skipped = false) => {
   // return false;
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
        document.getElementById('opt_'+currentQuestion.value.id+correct_answer.id).classList.add('correct');
        // nextPrev(1,onCountdownEnd);
        // return;
        timeout = 5000;
    }
    if(data.status == 2){

        let selectedAnsId = selectedAnswer.value.id;

        document.getElementById('opt_'+currentQuestion.value.id+selectedAnsId).classList.add('wrong');
        document.getElementById('opt_'+currentQuestion.value.id+selectedAnswer.value.id).classList.remove('correct','active');

        timeout = 7000;
    }
    document.getElementById('opt_'+currentQuestion.value.id+correct_answer.id).classList.add('correct','active');

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
onBeforeMount(() => {
    setTimeout(() => {
                   isLoading.value = false;
                }, 3000)
    getQuestions();
})
</script>
<style scoped>
.multisteps_form {
    width: 100% !important;
}
.count_title span {
    font-size: 1.20rem;
}
.count_title {
   width: 45%;
}
.count_box {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 999;
   opacity: 0.6;

}
li.wrong {
    color: #e72424 !important;
    border: 2px solid #e02121 !important;
    border-bottom: 3px solid #e21b1b !important;
}
li.correct{
    border: 1px solid #3de43d;
}
.count_box {
    max-width: 25rem;
    min-height: 5.625rem;
    font-family: "Jost", sans-serif;
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
.count_sec::after{
    display: none;
}
</style>
