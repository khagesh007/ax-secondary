<template>
    <div class="mt-5">
        <dealCreateForm v-if="showDealCreateForm" :property_id="property_id" :owner_id="owner_id" @modalRemoved="modalRemoved"></dealCreateForm>
    </div>
</template>
<script setup>
import {onMounted, ref} from "vue";
import dealCreateForm from './dealCreateForm.vue'
const property_id = ref('');
const showDealCreateForm = ref(false);
const owner_id = ref('');
const showModal = () => {
    showDealCreateForm.value = true;
   // $('#dealCreateFormModal').modal('show');
}
const modalRemoved = () => {
    showDealCreateForm.value = false;
}
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("listingDealCreateModal").onclick = function() {
        console.log('listingDealCreateModal clicked');
        let property = document.getElementById("id");
        let owner = document.getElementById("owner_id");
        property_id.value = property.value;
        owner_id.value = owner.value;
        showModal();
    };
});


</script>
