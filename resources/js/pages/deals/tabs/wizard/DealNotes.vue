<template>
    <div data-simplebar class="px-3 mb-2 scrollable-notes" id="notes">
        <div v-show="notePlaceholder">
            <NoteMsgPlaceholder v-for="index in 3" :key="index"></NoteMsgPlaceholder>
        </div>
        <div class="d-flex mb-0" v-show="!notePlaceholder" v-for="(note, index) in notes" :key="index">
            <div class="flex-shrink-0">
                <img :src="note?.created_by?.thumb_url" :alt="note.created_by ? note.created_by.name : 'System'"
                     class="avatar-xs rounded-circle"/>
            </div>
            <div class="flex-grow-1 ms-3">
                <h5 class="fs-13"><a href="javascript:void(0)">{{note.created_by ? note.created_by.name : "System" }}</a> <small
                    class="text-muted">{{ formatDate(note.created_at, 'DD/MM/YYYY h:i A') }}</small></h5>
                <p class="text-muted">{{ note.note }}</p>
            </div>
        </div>
    </div>
    <div v-if="deal_state.data.id != null" class="row align-items-center">
        <form @submit.prevent="onSubmit">
            <div class="input-group">
                <textarea class="form-control" id="note_input" v-model="formData.note" rows="2" placeholder="Enter a note" required></textarea>
                <button class="btn btn-primary btn-icon waves-effect waves-light"
                        type="submit" title="Save Note" id="addNoteButton"
                        style="height:auto;">
                    <i class="mdi mdi-send-outline"></i>

                </button>
                <br>  <p v-if="formData.note.$error">{{ formData.note.$errors[0] }}</p>

            </div>
        </form>
    </div>
</template>
<script setup>
import {onMounted, ref, watch, watchEffect} from "vue";
import useGlobalHelpers from "../../../../composables/helpers/global";
const {formatDate} = useGlobalHelpers();
import NoteMsgPlaceholder from "../../../../utilities/placeholder/NoteMsgPlaceholder.vue";
import { useDealStore} from "../../store/dealState";
const deal_state = useDealStore();
import useDeals from "../../composables/deals";
const {addDealNote, notes, getNotes} = useDeals();
const notePlaceholder = ref(false);
const note_input = ref('');
const formData = ref({
    note: ''
});
const deal_notes = ref([]);
const props = defineProps({
    notesProp: {
        type: Array,
        required: true
    }
});
watchEffect(() => {
    if (notes.value) {
        setTimeout(() => {
            notePlaceholder.value = false;
            $('#notes').scrollTop($('#notes')[0].scrollHeight);
        }, 4000);


    } else {
        notePlaceholder.value = true;
        setTimeout(() => {
            notePlaceholder.value = false;
            //if notes is still empty after 9 seconds, hide the placeholder
        }, 9000);
    }
})
const scrollToBottomx = () => {
    var notesElement = document.getElementById("notes");
    notesElement.animate({ scrollTop: notesElement.scrollHeight}, 1000);
};
const onSubmit = (e) => {
    e.preventDefault();
    if (formData.value.note) {
        // props.notes.push({
        //     note: formData.value.note,
        //     created_at: new Date(),
        //     created_by: {
        //         name: 'You',
        //         thumb_url: '/assets/images/users/avatar-1.jpg'
        //     }
        // });
        let formData1 = new FormData();
        formData1.append('note', formData.value.note);
        formData1.append('deal_id', deal_state.data.id);

        addDealNote(formData1, deal_state.data.id);
        formData.value.note = '';
        scrollToBottomx();
    }
};
watch(() => deal_state.notes, (val) => {
    if (val) {
        scrollToBottomx();
    }
});
onMounted(() => {

    if(deal_state.data.id != null){
        getNotes(deal_state.data.id)
        deal_state.notes = notes.value;
    }else{
        notes.value = deal_state.notes;
    }

});

</script>
