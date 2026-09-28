<template>
    <div  class="card">

        <div class="card-body mt-0 form-steps">
            <ul class="nav nav-tabs nav-tabs-custom nav-success nav-justified mb-0" role="tablist">

                <li class="nav-item active">


                    <a class="nav-link active" data-bs-toggle="tab" href="#notesTab" role="tab"
                       aria-selected="true" >
                        <i class="mdi mdi-note-text align-middle"></i>
                        Notes</a>
                </li>


                <li class="nav-item addHide">
                    <a class="nav-link" data-bs-toggle="tab" href="#activityLogTab" role="tab"
                       aria-selected="false">
                        <i class="mdi mdi-history align-middle"></i>
                        Activity Log</a>
                </li>

            </ul>
        </div>


        <div class="tab-content text-muted">
            <div class="tab-pane active" id="notesTab" role="tabpanel">
                <NoteList :notes="notes"></NoteList>


            </div>
            <div class="tab-pane" id="activityLogTab" role="tabpanel">
                <div id="activityLogDiv">
                    <div class="">
                        <div class=" mt-0">
                            <div
                                class="acitivity-timeline profile-timeline scrollable-notes scrollable-notes-reduce-height">

                    <ActivityLogList :logs="logs"></ActivityLogList>

                            </div>
                            <div class="d-none p-lg-2" id="view-edit-mode">
                                <small class="mb-0 text-muted">
                                    <span id="created_at" class="d-none">Created:</span> <span
                                    id="created_by_span"></span>
                                    <br><span id="updated_at" class="d-none">Updated:</span> <span
                                    id="updated_by_span"> </span></small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</template>
<script setup>
import {onMounted, ref, watchEffect} from "vue";
import useDeals from "../composables/deals";
import useGlobalHelpers from "../../../composables/helpers/global";
import NoteMsgPlaceholder from "../../../utilities/placeholder/NoteMsgPlaceholder.vue";
import ActivityLogList from "../../../components/widgets/ActivityLogList.vue";
import NoteList from "../../../components/widgets/NoteList.vue";
const { formatDate } = useGlobalHelpers();
const { getNotes, notes, getActivityLog, logs} = useDeals();
const notePlaceholder = ref(true);
const activityLogPlaceholder = ref(true);
const props = defineProps(
    {
        deals: {
            type: Object,
            required: true
        }
    })
watchEffect(() => {
    if (notes.value) {
        setTimeout(() => {
            notePlaceholder.value = false;
           // $('#notes').scrollTop($('#notes')[0].scrollHeight);
        }, 4000);

    } else {
      notePlaceholder.value = true;
    }
})
onMounted(() => {
    getNotes(props.deals.id);
    getActivityLog(props.deals.id);
})
</script>
