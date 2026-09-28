<template>

    <div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModallLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="taskModallLabel">Create/Update Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            @click="modalClose()"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <form v-if="deals != null" @submit.prevent="onSubmit" enctype="multipart/form-data">

                            <div class="row g-3">

                                <div class="col-3">
                                    <input type="hidden" name="deal_id" v-model="formData.deal_id">
                                   Subject
                                </div>
                                <div class="col-9">
                                    <input type="text" class="form-control" v-model="formData.subject" name="subject">
                                </div>
                                <div class="col-3">
                                    Description
                                </div>
                                <div class="col-9">
                                  <textarea rows="4" class="form-control" v-model="formData.description" name="description"></textarea>
                                </div>
                                <div class="col-2">
                                    Due Date
                                </div>
                                <div class="col-4">
                                   <input type="date" v-model="formData.due_date" class="form-control">
                                </div>
                                <div class="col-2">
                                   Priority
                                </div>
                                <div class="col-4">
                                  <select class="form-control" v-model="formData.priority">
                                      <option value="low">Low</option>
                                      <option value="medium">Medium</option>
                                      <option value="high">High</option>
                                  </select>
                                </div>
                            </div>




                            <div class="mt-4">

                                <div class="hstack gap-2 justify-content-right float-end">
                                    <a href="javascript:void(0);" class="btn btn-light action-close fw-medium"
                                       data-bs-dismiss="modal" @click="modalClose()"><i
                                        class="ri-close-line me-1 align-middle"></i> Close</a>
                                    <button type="submit" class="btn btn-success action-add">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>



                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {ref, onMounted, watchEffect, onUnmounted} from 'vue'
import {useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
import axios from "axios";

const emit = defineEmits(['removeLeadModal']);
const modalLoader = ref(true);
const { storeDealTask } = useDeals();
const { leadResources} = useDealStore();
const deal = useDealStore();
const leadTypeOptions = ref([]);
const deals = deal.data;
const props = defineProps({

});
const documents = ref([]);
const documentsType = ref({
    passport: 'Passport copy',
    visa: 'Visa copy',
});
const selectedDocument = ref({
    other_documents: []
});
const formData = ref({
    deal_id: deal.data.id,
    subject: '',
    description: '',
    due_date: '',
    priority: '',
});




watchEffect(() => {


})
const modalClose = () => {
    emit('removeLeadModal');
    modalLoader.value = false;
}
const onSubmit = () => {
    const formRes = storeDealTask(formData.value).then(res => {
        deal.data = res.data.data;
        if(res.data.status === '1') {
            $('#taskModal').modal('hide');
            modalLoader.value = true;
            //emit('removeLeadModal');
        }
    }).catch(err => {
        console.log(err);
    });

}
onMounted(() => {


});

onUnmounted(() => {

});

</script>
<style scoped>
.modal {
    position: fixed !important;

}
</style>
