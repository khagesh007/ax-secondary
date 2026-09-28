<template>
    <div>
        <Nav/>
        <div class="row">
            <div class="col-4" >
                <Sidebar :dealTasks="dealTasks"/>
            </div>
            <div  class="col-8">
                <Content/>
            </div>
        </div>
    </div>
</template>
<script setup>
import {ref, provide, onMounted, watch} from 'vue';
import Nav from './includes/Nav.vue';
import Sidebar from './includes/Sidebar.vue';
import Content from './Content.vue';
import useDeals from './composables/deals';
import { useDealStore} from "./store/dealState";
import useGlobalHelper from "../../composables/helpers/global";
const deal = useDealStore();
const { formStep } = useDealStore();
const { getDealTasks, dealTasks} = useDeals();
const { getCountries, countries } = useGlobalHelper();
provide('deal', props.deals)
provide('countries', countries)
deal.data = props.deals;
deal.agents.push(props.deals.agent);

const props = defineProps(
    {
        deals: {
            type: Object,
            required: true
        }
    })
onMounted(() => {

    getDealTasks(props.deals.id,'2','PENDING');
    setTimeout(() => {
         getCountries();
    }, 6000);
})


</script>
