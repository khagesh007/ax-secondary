<template>

    <div class="input-group" v-if="properties">


        <v-select
            v-model="formData.property_id"
            :options="properties"
            label="text"
            :filterable="true"
            :clearable="true"
            :placeholder="'Select Listing'"
            name="agent_id"
            @search="onSearch"
            @option:selected="onSelect($event)"
            @deselected="deselected($event)"
            @update:modelValue="selectionChanged($event)"
            :value="formData.property_id"
            :reduce="option => option.id"
            :get-option-label="option => option.ref_no + ' - ' + option.title"
            :get-option-value="option => option.id"
            :multiple="multiple"
            :close-on-select="true"
            :clear-on-select="false"
            :preserve-search="true"
            :hide-selected="false"
            :join-values="false"
            :max="Infinity"
            :allow-empty="true"
            :options-limit="300"
            :limit="Infinity"
            :loading="false"
            :input-id="'property_id'"
            :dir="'ltr'"
            :open-direction="'bottom'"
            :tabindex="null"
            :disabled="false"
            :disable-search="false"
            :taggable="false"
            :tag-position="'top'"
            :tag-placeholder="'Add this as new agent'"
            :tag-no-results="'No matching agent found'"
            :tag-disabled="false"
            tag-size="md"
            :push-tags="false"
            :searchable="true"
            :create-tag="false"
            :create-if-allowed="false"
            :options-are-literals="false"
            :limit-text="n => 'and ' + n + ' other options'"
            :loading-text="'Loading...'"
            :no-options-text="'No options available'"
            :search-no-results-text="'No results found'"
            :search-placeholder="'Search...'"
            :aria-labelledby="'property_id'"
            :select-on-tab="false"
            :selected-Value="formData.property_id"
            :id="'property_id'"
            :name="'property_id'"
            :open-on-focus="false"
            :native-size="20"
            :small-chips="false"

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
const { properties, getUserProperties } = useGlobalHelper();
const formData = ref({
    deal_id: '',
    deal_price: '',
    property_id: [],
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
    refresh: {
        type: Boolean,
        required: false,
        default: false,
    },
});
//emit selected value to parent component
const emit = defineEmits(['propertySelected']);
const onSelect = (value) => {
    emit('propertySelected', value);
}
const onSearch = (search) => {
    if(search.length >= 1){
        getUserProperties(search,props.user)
    }
}
const drawThumbnail = (image) => {
    if(image){
        return image.thumbnail;
    }
    return '';
}
const selectionChanged = (id) => {
formData.value.property_id = id;
if(id == null){
    emit('propertySelected', null);
}
}

watch(() => props.refresh, (val) => {
    if(val){
        //reset the v-select
    formData.value.property_id = [];
        getUserProperties('',props.user);
    }
});

watch(() => props.user, (usr) => {
    if(usr == null || usr == ''){
        formData.value.property_id = [];
    }
});


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
        getUserProperties('',props.user);
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
    //     getUserProperties();
    // }

});
</script>
