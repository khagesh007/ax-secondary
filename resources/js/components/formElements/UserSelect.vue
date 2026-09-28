<template>

    <div class="input-group" v-if="users">
        <v-select
            v-model="formData.agent_id"
            :options="users"
            label="text"
            :filterable="true"
            :clearable="true"
            :placeholder="'Select Agent'"
            name="agent_id"
            @search="onUserSearch"
            @option:selected="onUserChange($event)"
            @cleared="onUserChange($event)"
            :value="formData.agent_id"
            :reduce="option => option.id"
            :get-option-label="option => option.text"
            :get-option-value="option => option.id"
            :multiple="multiple"
        >
            <template #option="option">
                <span><img  :src="option.image" class="img-flag rounded-circle" height="18" /> {{ option.text }}</span>

            </template>
        </v-select>

    </div>
</template>
<script setup>
import {onBeforeMount, onMounted, ref, watch} from "vue";
import "vue-select/dist/vue-select.css";
import vSelect from 'vue-select';
import useGlobalHelpers from "../../composables/helpers/global";
import useGlobalHelper from "../../composables/helpers/global";
const { getUsers, users, getAgents } = useGlobalHelper();
const formData = ref({
    deal_id: '',
    deal_price: '',
    agent_id: [],
});
const props = defineProps({
    multiple: {
        type: Boolean,
        required: false,
        default: false,
    },
    passedUsers: {
        type: Array,
        required: false,
    },
    roles: {
        type: Array,
        required: false,
    }
});
//emit selected value to parent component
const emit = defineEmits(['userSelected','userSelectionCleared']);
watch(() => formData.value.agent_id, (val) => {
   if(val === null){

       emit('userSelectionCleared', val);
   }
});

const onUserChange = (value) => {

    emit('userSelected', value);
}
const onUserSearch = (search) => {
    if(search.length >= 1){
            getUsers(search,props.roles)
    }
}
onBeforeMount(() => {
    if(Array.isArray(props.passedUsers) && props.passedUsers.length){
        props.passedUsers.forEach((usr) => {
            if(usr.id == null){
                return;
            }
            users.value.push({
                id: usr.id,
                text: usr.name,
                image: usr.image
            });
            formData.value.agent_id.push(usr.id);
        });
    }else{
        getUsers('',props.roles)

    }
});
onMounted(() => {

    // if(props.passedUsers){
    //
    //     props.passedUsers.forEach((usr) => {
    //        users.value.push({
    //             id: usr.id,
    //             text: usr.name,
    //             image: usr.image
    //        });
    //         formData.value.agent_id.push(usr.id);
    //     });
    // }else{
    //     getUsers();
    // }

});
</script>
