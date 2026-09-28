<template>

    <div class="input-group" v-if="properties">
        <v-select
            v-model="formData.agent_id"
            :options="properties"
            label="text"
            :filterable="true"
            :clearable="true"
            :placeholder="'Select Listing'"
            name="agent_id"
            @search="onSearch"
            @option:selected="onSelect($event)"
            @option:deselected="deselected($event)"
            :value="formData.agent_id"
            :reduce="option => option.id"
            :get-option-label="option => option.ref_no + ' - ' + option.title"
            :get-option-value="option => option.id"
            :multiple="multiple"
        >
            <template #option="option">
                <span><img v-if="option.first_image" :src="drawThumbnail(option.first_image)" class="img-flag rounded-5" width="18" /> {{ option.ref_no }} - {{ option.category }} - {{ option.unit_no }}
                  </span> <br> <span class='small d-inline-block'>
                <i class='mdi mdi-bed-double'></i> {{ option?.bed?.name }}</span>
                <span class='small ml-1 d-inline-block'>
                <i class='mdi mdi-google-maps'></i>{{ option?.location }}</span>
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
const { properties, getProperties } = useGlobalHelper();
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
    user: {
        type: Number,
        required: false,
    },
});
//emit selected value to parent component
const emit = defineEmits(['propertySelected']);
const onSelect = (value) => {
    emit('propertySelected', value);
}
const onSearch = (search) => {
    if(search.length >= 1){
        getProperties(search,props.user)
    }
}
const drawThumbnail = (image) => {
    console.log(image)
    if(image){
        return image.thumbnail;
    }
    return '';
}
const deselected = (event) => {
    console.log(event)
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
        getProperties('',props.user);
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
    //     getProperties();
    // }

});
</script>
