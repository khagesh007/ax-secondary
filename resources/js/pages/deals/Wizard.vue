<template>
    <div>
        <main class="my-5">
            <div class="container">
                <div id="wizard" class="wizard clearfix">
                    <div class="steps clearfix">
                        <ul role="tablist">
                            <li v-for="(step, index) in steps" :key="index"
                                :class="{ 'current': step.selected, 'done': step.completed, 'disabled' : !step.completed && index > 0  }"
                                role="tab" :aria-disabled="false" :aria-selected="step.selected">
                                <a :id="'wizard-t-' + index" :href="'#wizard-h-' + index"
                                   :aria-controls="'wizard-p-' + index"
                                   @click="selectStep(index)">
                                    <span class="current-info audible">current step: </span>
                                    <div class="media">
                                        <div class="bd-wizard-step-icon"><i :class="step.icon"></i></div>
                                        <div class="media-body">
                                            <div class="bd-wizard-step-title">{{ step.title }}</div>

                                        </div>
                                    </div>
                                </a>
                            </li>
                        </ul>

                    </div>
                    <div class="content clearfix">
                        <!-- Placement Offcanvas -->

                            <button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">Toggle Right offcanvas</button>


                        <Initial v-if="currentStep == 'initial'" @next_step="nextStep"></Initial>
                        <OwnerTab v-if="currentStep == 'owner'" @next_step="nextStep"></OwnerTab>
                        <LeadsTab v-if="currentStep == 'leads'" @next_step="nextStep"></LeadsTab>
                        <Attachments v-if="currentStep == 'attachments'" @next_step="nextStep"></Attachments>
                        <FinancialTab v-if="currentStep == 'financial'"></FinancialTab>

                    </div>

                </div>
            </div>
        </main>
        <div v-if="deal_state.data">
            <div id="chat-circle" class="btn btn-raised">
                <div id="chat-overlay"></div>
                <i class="mdi mdi-message"></i>
            </div>
            <div class="chat-box">
                <div class="chat-box-header">
                    <span class="chat-box-toggle"><i class="mdi mdi-close"></i></span>
                </div>
                <div class="row">


                    <div class="col-12">
                        <DealNotes :notesProp="notes"></DealNotes>
                    </div>
                </div>

            </div>
        </div>
<SideBarRight></SideBarRight>
    </div>
</template>
<script setup>
import {onBeforeMount, provide, onMounted, ref, watch} from "vue";
import {useDealStore} from "./store/dealState";
import useDeals from "./composables/deals";
import Initial from "./tabs/wizard/Initial.vue";
import OwnerTab from "./tabs/wizard/OwnerTab.vue";
import LeadsTab from "./tabs/wizard/LeadsTab.vue";
import FinancialTab from "./tabs/wizard/FinancialTab.vue";
import CardPlaceholder from "../../utilities/placeholder/CardPlaceholder.vue";
import NoteActivity from "./widgets/NoteActivity.vue";
import Attachments from "./tabs/wizard/Attachments.vue";
import NoteList from "../../components/widgets/NoteList.vue";
import DealNotes from "./tabs/wizard/DealNotes.vue";
import SideBarRight from "./includes/SideBarRight.vue";
const deal_state = useDealStore();
import useGlobalHelper from "../../composables/helpers/global";

const {getCountries, countries} = useGlobalHelper();
const {getNotes, notes} = useDeals();
provide('countries', countries)
provide('genders', genders)
const currentStep = ref("initial");
const placeholder = ref(true);
const props = defineProps({
    deal: {
        type: Object,
        required: false
    }
});
const genders = ref(
    {value: 'Male', text: 'Male'},
    {value: 'Female', text: 'Female'},
    {value: 'Unknown', text: 'Unknown'}
);
const steps = ref([
    {
        tab: "initial",
        title: 'Property',
        subtitle: 'Step 1',
        icon: 'ri ri-building-line',
        selected: true,
        completed: false
    },
    {tab: "owner", title: 'Seller', subtitle: 'Step 2', icon: 'mdi mdi-account-box', selected: false, completed: false},
    {
        tab: "leads",
        title: 'Buyer',
        subtitle: 'Step 3',
        icon: 'mdi mdi-account-network',
        selected: false,
        completed: false
    },
    {
        tab: "attachments",
        title: 'Attachments',
        subtitle: 'Step 4',
        icon: 'mdi mdi-file',
        selected: false,
        completed: false
    },
    {tab: "financial", title: 'Financial', subtitle: 'Step 5', icon: 'mdi mdi-bank', selected: false, completed: false}


]);
const selectStep = (index) => {
    //if its initial than don't let click on other steps and if its owner than enable initial and owner and if its leads than enable initial, owner and leads and if its financial than enable all
    // let i = index;

    let current_state = deal_state.data.state;

    let limit = 0;
    steps.value.forEach((step, i) => {


        // if(current_state === 'initial'){
        //     if(i > 0){
        //         limit = 0;
        //         return false;
        //     }
        // }
        // if(current_state === 'owner'){
        //
        //     if(i > 1){
        //         limit = 1;
        //         return false;
        //     }
        // }
        // if(current_state === 'leads'){
        //     if(i > 2){
        //         limit = 2;
        //         return false;
        //     }
        // }
        // if(current_state === 'financial'){
        //     if(i > 3){
        //         limit = 3;
        //         return false;
        //     }
        // }


        if (i > limit) {
            step.completed = false;
        } else {

        }
        step.selected = i === index;
        step.completed = i < index;

        if (step.selected === true) {
            currentStep.value = step.tab
        }
    });
}
const nextStep = (next_step) => {
    console.log(next_step)
    currentStep.value = next_step;
    steps.value.forEach((step, i) => {
        if (step.tab === currentStep.value) {
            selectStep(i)
        }
    });
}
watch(() => deal_state.deal_type, (newVal, oldVal) => {
  if(deal_state.deal_type == 'Rental'){
    steps.value.forEach((step, i) => {
      if (step.tab === 'leads') {
        step.title = 'Tenant'
      }
      if(step.tab === 'owner'){
        step.title = 'Owner'
      }
    });
  }else{
    steps.value.forEach((step, i) => {
      if (step.tab === 'leads') {
        step.title = 'Buyer'
      }
      if(step.tab === 'owner'){
        step.title = 'Seller'
      }
    });
  }
})
onBeforeMount(() => {
    if (props.deal) {
        deal_state.data = props.deal;
        //deal_state.agents.push(props.deal.agent);
    }
})
onMounted(() => {
    setTimeout(() => {
        getCountries();
        if (props.deal) {
            getNotes(props.deal.id)
        }
    }, 2000);

    if(props.deal && props.deal.type =='Rental'){
        steps.value.forEach((step, i) => {
            if (step.tab === 'leads') {
                step.title = 'Tenant'
            }
            if(step.tab === 'owner'){
                step.title = 'Owner'
            }
        });
    }else{
      if(deal_state.deal_type == 'Rental'){
        steps.value.forEach((step, i) => {
          if (step.tab === 'leads') {
            step.title = 'Tenant'
          }
          if(step.tab === 'owner'){
            step.title = 'Owner'
          }
        });
      }else{
        steps.value.forEach((step, i) => {
          if (step.tab === 'leads') {
            step.title = 'Buyer'
          }
          if(step.tab === 'owner'){
            step.title = 'Seller'
          }
        });
      }
    }

    $(document).delegate(".chat-btn", "click", function () {
        var value = $(this).attr("chat-value");
        var name = $(this).html();
        $("#chat-input").attr("disabled", false);

    })

    $("#chat-circle").click(function () {
        $("#chat-circle").toggle('scale');
        $(".chat-box").toggle('scale');
    })

    $(".chat-box-toggle").click(function () {
        $("#chat-circle").toggle('scale');
        $(".chat-box").toggle('scale');
    })

})
</script>

<style>
#chat-circle {
    position: fixed;
    bottom: 150px;
    right: 23px;
    background: #c48667;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    color: white;
    padding: 15px;
    cursor: pointer;
    box-shadow: 0px 3px 16px 0px rgb(0 0 0 / 17%), 0 3px 1px -2px rgba(0, 0, 0, 0.2), 0 1px 5px 0 rgba(0, 0, 0, 0.12);
}


#chat-overlay {
    background: rgba(255, 255, 255, 0.1);
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: none;
}

.chat-box {
    display: none;
    background: #efefef;
    position: fixed;
    right: 30px;
    bottom: 50px;
    width: 350px;
    max-width: 85vw;
    max-height: 100vh;
    border-radius: 5px;
    box-shadow: 0px 5px 35px 9px #ccc;
    z-index: 1;
}

.chat-box-toggle {
    float: right;
    margin-right: 15px;
    cursor: pointer;
}

.chat-box-header {
    background: #d3a188;
    height: 36px;
    border-top-left-radius: 5px;
    border-top-right-radius: 5px;
    color: white;
    text-align: center;
    font-size: 19px;
    padding-top: 5px;
}


.chat-input > form {
    margin-bottom: 0;
}


@media only screen and (max-width: 500px) {
    .chat-logs {
        height: 40vh;
    }
}

.chat-msg.user > .msg-avatar img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    float: left;
    width: 15%;
}

.chat-msg.self > .msg-avatar img {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    float: right;
    width: 15%;
}

.cm-msg-text {
    background: white;
    padding: 10px 15px 10px 15px;
    color: #666;
    max-width: 75%;
    float: left;
    margin-left: 10px;
    position: relative;
    margin-bottom: 20px;
    border-radius: 30px;
}

.chat-msg {
    clear: both;
}

.chat-msg.self > .cm-msg-text {
    float: right;
    margin-right: 10px;
    background: #5a5eb9;
    color: white;
}

.cm-msg-button > ul > li {
    list-style: none;
    float: left;
    width: 50%;
}

.cm-msg-button {
    clear: both;
    margin-bottom: 70px;
}

.media-body{
    margin-top: 8px !important;
}

</style>
