<template>
    <div v-show="placeholder">
        <NoteMsgPlaceholder v-for="index in 3" :key="index"></NoteMsgPlaceholder>
    </div>
    <div v-show="!placeholder" class="accordion-item border-0" v-for="(item, index) in logs">
        <div class="accordion-header" id="heading6">
            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapse{{item.id}}" aria-expanded="false">

                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <img src="" alt="" class="avatar-xs rounded-circle">
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="fs-14 mb-1"> {{item.causer ? item.causer.name :"System"}}  </h6>
                        <small class="text-muted">{{item.description}} - <small class="mb-0 text-muted">{{timeAgo(item.created_at)}}</small> </small>
                    </div>
                </div>
            </a>
        </div>
        <div id="collapse{{item.id}}" class="accordion-collapse collapse hide" aria-labelledby="heading6" data-bs-parent="#accordion{{item.id}}">
            <div class="accordion-body ms-2 ps-5 pt-2">
                <div class="code-view" id="hiddenLog{{item.id}}">
                                 <span class="language-markup">
                                    <small class="text-muted"><strong>New data:</strong> <br>{{activityChangeDataFormat(item.properties.attributes)}}<br><strong>Old Data:</strong><br>{{activityChangeDataFormat(item.properties.old)}} </small></span></div> </div>
        </div>
    </div>
</template>
<script setup>
import {ref, watchEffect} from "vue";
import NoteMsgPlaceholder from "../../utilities/placeholder/NoteMsgPlaceholder.vue";
import useGlobalHelper from "../../composables/helpers/global";
const { timeAgo, activityChangeDataFormat } = useGlobalHelper()
const placeholder = ref(true)
const props = defineProps({
    logs: {
        type: Object,
        required: true
    }
})
watchEffect(() => {
    if (props.logs.length >0) {
        setTimeout(() => {
            placeholder.value = false;
        }, 1000);
    } else {
        placeholder.value = true;
    }
})
</script>
