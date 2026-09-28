<template>
  <!-- Default Modals -->
  <div id="agentInviteModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
      <div class="modal-content">
          <form @submit.prevent="onSubmit" enctype="multipart/form-data">
        <div class="modal-header">
          <h5 class="modal-title" id="myModalLabel">Invite Agent</h5>
          <button type="button" @click="removeModal" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
        </div>
        <div class="modal-body">
          <h5 class="fs-15">
           Invite other agent to add {{ dealProp.type =='Rental' ? 'Tenant' : 'Buyer' }}
          </h5>

         <div class="row">
           <div class="col-md-12  col-sm-12 mt-2 pr-0">
             <div class="input-group">
               <UserSelect @userSelected="userSelected" :roles="['Agent']" :passedUsers="[]"
                           :multiple="false"
               />

             </div>
           </div>
         </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary ">Invite</button>
        </div>
          </form>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
</template>

<script setup>
import {onMounted, ref} from "vue";
import UserSelect from "../../../components/formElements/UserSelect.vue";
const emit = defineEmits(['removeInviteModal']);
import useDeals from "../composables/deals";
const { addAgentInvitation } = useDeals();
const removeModal = () => emit('removeInviteModal');
const props = defineProps({
    dealProp: {
        type: Object,
        required: true
    }
});
const formData = ref({
  agent_id: null,
  deal_id: null
});
const userSelected = (user) => {
  formData.value.agent_id = user.id;
  formData.value.deal_id = props.dealProp.id;
}
const onSubmit = () => {
  addAgentInvitation(formData.value);
}
onMounted(() => {
  $('#agentInviteModal').modal('show');
})
</script>
