<template>
    <div class="wrapper pt-5">
        <loading v-model:active="isLoading"
                 :can-cancel="true"
                 />
        <div class="container">

        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-2">
                    <div class="logo_area pt-5">
                        <a href="/">
                            <img src="../../../../../public/assets/images/logo/logo140.png"
                            style="width: 120px"
                            alt="image-not-found">
                        </a>
                    </div>
                </div>
                <div class="col-8">
                    <div class="row text-center">
                <!-- Circles which indicates the steps of the form: -->
                <div class="col step_progress d-flex d-none d-sm-block"
                style="margin-top: 45px"
                >

                    <p class="step bg-white rounded-pill" v-for="(qstn, index33) in questions" :key="index33"
                    style="height: 3px;"
                    ></p>
                </div>
            </div>
                </div>
                <div class="col-2 d-none d-md-block pt-2 pb-3">
                    <div class="step_content text-end pt-3 float-end">
                        <div class="count_number_card">
                        <div class="count_number countdown_timer pt-1" data-countdown="2022/10/24"><span>{{ countdown.minutes }}:{{ countdown.seconds }}</span></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <h3 class="text-center text-danger" v-if="isTimeup"
            style="font-family: 'AlethiaPro', sans-serif;"
            >Time is up. You will see next question in 5 second.</h3>

            <h3 class="text-center text-danger" v-if="!answerSelected"
            style="font-family: 'AlethiaPro', sans-serif;"
            >Please select an answer or skip.</h3>
            <form class="multisteps_form position-relative" id="wizard" method="get" :action="action_url">
                <!------------------------- Step-1 ----------------------------->
                <div class="multisteps_form_panel"
                 v-for="(qstn, index) in questions" :key="index"
                 :id="'questionSec'+index"
                 >
                    <!-- Form-content -->



                    <div class="row">

                    <!-- Form-items -->
                        <div :class="{ 'col-8': isItHaveMedia, 'col-12': !isItHaveMedia }" class="custom-card" >
                            <h4 class="question_title text-center text-white question-title"
                            style="font-family: 'AlethiaPro', sans-serif;"
                            >{{qstn.question}}</h4>
                    <div class="form_items d-flex justify-content-center">
                        <ul class="ps-0">
                            <li v-for="(option, index2) in qstn.options" :key="index2"
                                :id="'opt_'+qstn.id+''+option.id"
                                @click="selectOption(qstn, option, 'opt_'+qstn.id+option.id)"
                                class=" step_1 rounded-pill2 option bg-white-loose text-start animate__animated animate__fadeInRight animate_50ms">
                                <input v-model="qstn.answer" :value="option.id" type="radio" :id="'opt_'+index2" name="stp_1_select_option">
                                <label :for="'opt_'+qstn.id+''+option.id"
                                       @click="selectOption(qstn, option, 'opt_'+qstn.id+option.id)"
                                > {{ option.text }}</label>
                            </li>

                        </ul>
                    </div>
                        </div>
                        <div :class="{ 'col-4 mt-5 text-center': isItHaveMedia === true, 'd-none': !isItHaveMedia }">

                            <div class="question_image">
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
                </div>
                <div class="row ms-5 mt-5">
                    <div class="col-3">
                         <button v-if="skipBtn" type="button"
                    @click="submitAnswer(true)"
                    class="new_btn text-uppercase new-btn-border float-end" id="prevBtn" >Skip Question</button></div>
                    <div class="col-6 text-center"> <span class="text-uppercase justify-content-center align-items-center fw-100 text-brand-grad">Question {{currentQuestionNumber+1}} / {{questions.length}}</span></div>
                    <div class="col-3">
                        <button type="button" class="new_btn text-uppercase new-btn-border right-btn"

                    v-if="!isTimeup && showNextBtn"
                    id="nextBtn" @click="submitAnswer()">Next Question</button>
                    <button type="button" class="new_btn text-uppercase new-btn-border"
                    v-if="showContinueBtn"
                    @click="continueToNext()">Continue</button>
                    </div>
                </div>
                <!---------- Form Button ---------->



            </form>
        </div>
    </div>
</template>
<script setup>
import {onBeforeMount, onMounted, ref, watch} from 'vue';
import axios from "axios";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
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
const currentQuestionNumber = ref(0);
const currentQuestion = ref({});
const showQuestions = ref(false);
const selectedAnswer = ref('');
const showNextBtn = ref(true);
const skipBtn = ref(true);
const action_url = 'quiz/feedback?attempt='+props.attempt.id;
const isLoading = ref(true);
const isItHaveMedia = ref(false);
const showContinueBtn = ref(false);
const timerSkipped = ref(false);
const answerSelected = ref(true);
const questions = ref([


]);


const question = ref(questions[currentQuestion.value]);
var timer = null;
const steps = ref(0);
const countdown = ref({
    hours: '00',
    minutes: '00',
    seconds: '00'
});

watch(currentQuestion, (newVal, oldVal) => {
    if(newVal == null){
        return;
    }
    if(newVal.image_url != null){
        console.log('has image')
        isItHaveMedia.value = true;
    }else if(newVal.video_url != null){
        console.log('has video')
        isItHaveMedia.value = true;
    }else if(newVal.youtube_url != null){
        console.log('has youtube')
        isItHaveMedia.value = true;
    }else{
        isItHaveMedia.value = false;
    }
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
            onCountdownEnd();
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
const nextPrev = (n) => {
    showContinueBtn.value = false;
    // This function will figure out which tab to display
    var x = document.getElementsByClassName("multisteps_form_panel");
    if(x.length == 0){
        return false;
    }
    // Hide the current tab:
    x[currentQuestionNumber.value].style.display = "none";
    // Increase or decrease the current tab by 1:
    currentQuestionNumber.value = currentQuestionNumber.value + n;
    console.log(currentQuestionNumber.value+'--- number of questions '+x.length);
    // if you have reached the end of the form... :
    if (currentQuestionNumber.value == x.length) {
        //...the form gets submitted:

        window.location.replace(action_url);
        // document.getElementById("wizard").submit();
        return false;

    }
    currentQuestion.value = questions.value[currentQuestionNumber.value];
    clearInterval(timer);
    startCountdown(currentQuestion.value.time,onCountdownEnd,false);
    showTab(currentQuestionNumber.value);

}
function fixStepIndicator(n) {
    // This function removes the "active" class of all steps...
    var i, x = document.getElementsByClassName("step");
    for (i = 0; i < x.length; i++) {
        x[i].className = x[i].className.replace(" active", "");

    }
    //... and adds the "active" class to the current step:
    x[n].className += " active";
}
const  showTab = (n) => {
    showNextBtn.value = true;
    skipBtn.value = true;
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
    // ... and run a function that displays the correct step indicator:
    fixStepIndicator(n);
}
const onCountdownEnd = async () => {
    isTimeup.value = true;
    await submitAnswer(true);

}
const continueToNext = () => {
    timerSkipped.value = true;
    nextPrev(1,onCountdownEnd);
    showContinueBtn.value = false;
}
const submitAnswer = async (skipped = false) => {
    let answerVal = currentQuestion.value.answered ? currentQuestion.value.answer : null;
    if(answerVal == null && skipped === false){
        answerSelected.value = false;
        return '';
    }else{
        answerSelected.value = true;
    }
    showNextBtn.value = false;
    skipBtn.value = false;
    isTimeup.value = false;

    try {
        const response = await axios.post('/quiz/answer/save', {
            quiz_id: props.quiz.id,
            attempt_id: props.attempt.id,
            answer: answerVal,
            question_id: currentQuestion.value.id,
            skipped: skipped
        });
        let data = response.data;
        let timeout = 1000;
        let correct_answer = data.correct_answer;
        if(skipped === true){
            // nextPrev(1,onCountdownEnd);
            // return;
            document.getElementById('opt_'+currentQuestion.value.id+''+correct_answer.id).classList.add('active');
            timeout = 5000;
        }

        if(data.status == 2){
            let selectedAnsId = selectedAnswer.value.id;
            document.getElementById('opt_'+currentQuestion.value.id+''+selectedAnsId).classList.add('wrong');
            document.getElementById('opt_'+currentQuestion.value.id+''+selectedAnswer.value.id).classList.remove('active');
            document.getElementById('opt_'+currentQuestion.value.id+''+correct_answer.id).classList.add('active');

            timeout = 7000;
        }
        document.getElementById('opt_'+currentQuestion.value.id+''+correct_answer.id).classList.add('correct');
        showContinueBtn.value = true;
        setTimeout(() => {
            if(timerSkipped.value === false){
            nextPrev(1,onCountdownEnd);
        }
          timerSkipped.value = false;
        }, timeout);

    } catch (error) {
        console.error(error);
    }
}
const getQuestions = async (quiz_id) => {
    try {
        const response = await axios.get('/quiz/question/all?quiz_id='+quiz_id+'&attempt_id='+props.attempt.id);

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
                        value: index2,
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
            console.log(currentQuestionNumber.value);
            startCountdown(currentQuestion.value.time,onCountdownEnd,false);
            showTab(currentQuestionNumber.value);
        }, 1000);

        // nextPrev(0)
    } catch (error) {
        console.error(error);
    }
}
onBeforeMount(() => {
    setTimeout(() => {
                   isLoading.value = false;
                }, 3000)
                steps.value = questions.length;
    getQuestions(props.quiz.id);
})
onMounted(() => {
   // showTab(currentQuestion.value);

})
</script>
<style scoped>

.count_number_card{
    width: 75px;
    height: 75px;
    border-radius: 2%;
    background: rgb(157,90,65);
    background: linear-gradient(90deg, rgba(157,90,65,1) 9%, rgba(221,138,105,1) 51%, rgba(157,90,65,1) 94%);
    line-height: 69px;
    text-align: center;
    position: fixed;
    top: 75px;
    right: 20px;
    opacity: 0.7;
}
.count_number span{
    color: #000000;
}
.step_progress {
    white-space: nowrap;
    overflow-x: auto;
}

.step {
    display: inline-block;
    width: 10rem;
}
.row .option.wrong {
    color: #ffffff;
    background-color: #b92828 !important;
}
.row .option.correct {
    color: #ffffff;
    background-color: #28542c !important;
    background-color: #ffffff;
}


</style>
