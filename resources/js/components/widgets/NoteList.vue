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
    <div class="row align-items-center">
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
import {onMounted, ref, watchEffect} from "vue";
import NoteMsgPlaceholder from "../../utilities/placeholder/NoteMsgPlaceholder.vue";
const notePlaceholder = ref(false);
const note_input = ref('');
const formData = ref({
    note: ''
});
const props = defineProps({
    notes: {
        type: Array,
        required: true
    }
});
watchEffect(() => {
    if (props.notes.value) {
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
const onSubmit = (e) => {
    formData
};
onMounted(() => {
console.log('notes mounted')
});

</script>
