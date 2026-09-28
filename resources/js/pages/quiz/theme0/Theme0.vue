<template>
    <div>
        <loading v-model:active="isLoading"
                 :can-cancel="true"
                />

        <div v-if="!isQuizDone">
            <form class="multisteps_form  position-relative" id="wizard" method="get" :action="action_url">

                <!------------------------- Step-1 ----------------------------->
                <div v-if="showQuestions" class="multisteps_form_panel"

                     v-for="(qstn, index) in questions" :key="index"
                   :id="'questionSec'+index"
                    style="display: none;"
                   >
                    <div class="question_title">

                        <h4 class="text-center mb-3 animate__animated animate__fadeInRight animate_25ms">{{qstn.question}}</h4>
                    </div>
                    <div class="text-center">
                            <div class="row">
                                <div :class="{ 'col-6': isItHaveMedia, 'col-12': !isItHaveMedia }">
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
                                        <iframe v-if="qstn.youtube_url" width="560"
                                        height="315" :src="`quiz/youtube/iframe/?video_url=`+qstn.youtube_url"
                                        title="YouTube video player" frameborder="0"></iframe>

                                </div>
                                </div>
                            <div :class="{ 'col-6': isItHaveMedia, 'col-10 offset-md-1': !isItHaveMedia }">
                                <div class="row">
                            <div  class="col-6 mt-3 option animate__animated animate__fadeInRight animate_50ms"
                                 v-for="(option, index2) in qstn.options" :key="index2"
                                  :id="'opt_'+qstn.id+''+option.id"
                                  @click="selectOption(qstn, option, 'opt_'+qstn.id+''+option.id+'label')"
                            >
                                <input  v-model="qstn.answer" class="d-none btn-check"  type="radio" :name="'answer'+index" :value="option.id">

                                <button type="button"
                                :for="'opt_'+qstn.id+''+option.id"
                                :id="'opt_'+qstn.id+''+option.id+'label'"
                                class="btn waves-effect waves-light custom-btn
                                option-btn
                                "

                                >{{ option.text }}</button>

                            </div>
                        </div>
                        </div>
                    </div>

                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="text-center">
                                <button type="button" class="btn btn-primary text-uppercase"
                                style="margin-top: 60px;"
                                v-if="showNextBtn"
                                id="nextBtn" @click="submitAnswer()">Next Question
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row" v-if="nextQuestionFlag">
                <div class="col-12">
                    <div class="text-center">
                      <p class="text-danger">Next question will appear in 5 second.</p>
                    </div>
                </div>
            </div>
                </div>


            </form>


            <div class="row mt-3" v-if="showWarning">
                        <div class="col-12">
                            <div class="text-center">

                                                                            <div class="alert alert-danger alert-dismissible border-2 bg-body-secondary fade show" role="alert">
                                                <strong> Opps!! </strong> It seems you didn't read the rules carefully. Please go back to the rules and read them carefully. You can attempt the quiz again.

                                            </div>

                                <button type="button" class="btn btn-danger text-uppercase"
                                style="margin-top: 60px;"
                                @click="redirectToRuels()"
                                >Go Back To Rules
                                </button>
                            </div>
                        </div>
                        </div>
        </div>
        <div v-else>
            <div class="row text-center">
                <div class="col-12">
                    <div class="shap_content position-relative">
                        <img style="width: 35% !important;" src="../../../../../public/theme/quiz/thankyou/assets/images/rectangle.png" alt="image_not_found">
                        <div class="title">
                            <h1 class="position-absolute"

                            style="width: 40%;
                            height: auto;
                            top: 93px;
                            left: 344px;
                            font-size: 2rem;
                            font-weight: 300;
                            font-family: 'Oswald', sans-serif;"
                            >Congratulations You have successfully completed the training quiz!</h1>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-5">
                    <h6>Now you will be redirected to main quiz....</h6>
                </div>
            </div>
        </div>

    </div>
</template>
<script setup>
import { ref, onMounted, onBeforeMount, watch } from 'vue';
import axios from "axios";
import Loading from 'vue-loading-overlay';
import 'vue-loading-overlay/dist/css/index.css';
import { set } from 'lodash';
const currentQuestionNumber = ref(0);
const currentQuestion = ref({});
const showQuestions = ref(false);
const selectedAnswer = ref('');
const nextQuestionFlag = ref(false);
const isItHaveMedia = ref(false);
const isLoading = ref(true);
const questions = ref([]);
const isQuizDone = ref(false);
const targetQuiz = ref({});
const question = ref(questions[currentQuestionNumber.value]);
const showNextBtn = ref(true);
const showWarning = ref(false);
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
        console.log('question count '+questions.value.length+' already attempted '+alreadyAttempted)
        if(questions.value.length <= alreadyAttempted){
            isQuizDone.value = true;
            redirectToMainQuiz();
           // window.location.replace(action_url);
        }

        setTimeout(() => {
           if(alreadyAttempted > 0){
            currentQuestionNumber.value = alreadyAttempted;
           }
           currentQuestion.value = questions.value[currentQuestionNumber.value];
            showTab(currentQuestionNumber.value);
        }, 1000);

       // nextPrev(0)
    } catch (error) {
        console.error(error);
    }
}
const selectOption = (question, option, elementId) => {
    question.answered = true;
    question.answer = option.id;
    selectedAnswer.value = option;
    var optionsElements = document.querySelectorAll('.option-btn');

    optionsElements.forEach(function(element) {
        element.classList.remove('custom-btn-selected');
    });
    document.getElementById(elementId).classList.add('custom-btn-selected');

}
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
const showTab = (n) =>{
    // This function will display the specified tab of the form ...
    var x = document.getElementsByClassName("multisteps_form_panel");
    var questionSec = document.getElementById('questionSec'+n);
    if(questionSec != null){
        questionSec.style.display = "block";
    }

    console.log(n)
    if(x.length == 0){
        return;
    }
    x[n].style.display = "block";
    // if (n == (x.length - 1)) {
    //     console.log('submit text')
    //     document.getElementById("nextBtn").innerHTML = "Submit";
    // } else {
    //     console.log('next text')
    //     document.getElementById("nextBtn").innerHTML = "Next Question" + ' <span><i class="fas fa-arrow-right"></i></span>';
    // }
    // ... and run a function that displays the correct step indicator:

}
const nextPrev = (n) => {
// This function will figure out which tab to display
var x = document.getElementsByClassName("multisteps_form_panel");
if(x.length == 0){
    return false;
}
if(questions.value.length == currentQuestionNumber.value+1){
            isQuizDone.value = true;
            redirectToMainQuiz();
        }
// Hide the current tab:
x[currentQuestionNumber.value].style.display = "none";
// Increase or decrease the current tab by 1:
currentQuestionNumber.value = currentQuestionNumber.value + n;
// if you have reached the end of the form... :
if (currentQuestionNumber.value > x.length) {
    //...the form gets submitted:

    window.location.replace(action_url);
    // document.getElementById("wizard").submit();
    return false;

}
currentQuestion.value = questions.value[currentQuestionNumber.value];
nextQuestionFlag.value = false;
showNextBtn.value = true;
showTab(currentQuestionNumber.value);

}
const getTargetQuiz = async () => {
    try {
        const response = await axios.get('/quiz/get/target/quiz?id='+props.quiz.id);
        targetQuiz.value = response.data.data;
    } catch (error) {
        console.error(error);
    }
}
const redirectToRuels = () => {
    window.location.replace('/quiz/rules/'+props.quiz.id);
}
const redirectToMainQuiz = () => {
    //window.location.replace(action_url);
    try{
        let data = {
            quiz_id: targetQuiz.value.id,
            attempt_id: props.attempt.id
        }
const response = axios.post('/quiz/start', data).then((response) => {
    console.log(response)
    setTimeout(() => {
        let url = response.data.url;
    window.location.replace(url);
    }, 3000);

});
        //window.location.replace

    }catch(e){
        console.log(e)
    }
}
const submitAnswer = async (skipped = false) => {
    showNextBtn.value = false;
    try {
        const response = await axios.post('/quiz/answer/save', {
            quiz_id: props.quiz.id,
            attempt_id: props.attempt.id,
            answer: currentQuestion.value.answered ? currentQuestion.value.answer : null,
            question_id: currentQuestion.value.id,
            skipped: skipped
        });
        let data = response.data;
        let timeout = 1000;
        if(skipped === true){
            nextPrev(1);
            return;
        }
        nextQuestionFlag.value = true;
        let correct_answer = data.correct_answer;


        if(data.status == 2){

            let selectedAnsId = selectedAnswer.value.id;
            document.getElementById('opt_'+currentQuestion.value.id+''+selectedAnsId+'label').classList.add('wrong');
            document.getElementById('opt_'+currentQuestion.value.id+''+selectedAnswer.value.id+'label').classList.remove('correct');
            timeout = 7000;
            setTimeout(() => {
                showQuestions.value = false;
                showWarning.value = true;
            }, 2000);
        }

        document.getElementById('opt_'+currentQuestion.value.id+''+correct_answer.id+'label').classList.add('correct');

        setTimeout(() => {
            nextPrev(1);
        }, timeout);

    } catch (error) {
        console.error(error);
    }
}
const pageUi = () =>{
    //on .option-btn button click remove class custom-btn-selected
    //add class custom-btn-selected to clicked button
    //add class active to clicked button
    let optionBtns = document.querySelectorAll('.option-btn');
    optionBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            optionBtns.forEach((btn) => {
                btn.classList.remove('custom-btn-selected');
            });
            e.target.classList.add('custom-btn-selected');
        });
    });
}
onBeforeMount(() => {
    setTimeout(() => {
                   isLoading.value = false;
                }, 3000)
    getQuestions();
})
onMounted(() => {
    getTargetQuiz();
})

</script>
<style scoped>
.custom-btn{
     width: 100%;
    border: 1px solid #d3a188;
    border-radius: 26px;
    text-align: left;
    background: white;
    cursor: pointer;
    height: 50px;
    font-size: 15px;
}
.custom-btn:active {
    background-color:#d3a188;
    color: black;
}
.custom-btn-selected {
    background-color:#d3a188;
    color: black;
}
.correct{
    background-color: #4CAF50;
    color: white;
}
.wrong{
    background-color: #f44336;
    color: white;
}
.vl-overlay .vl-background {
    opacity: 0.8 !important;
}
</style>
