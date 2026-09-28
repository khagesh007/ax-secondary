<template>
    <div>
    <div class="card">
        <div class="card-body">
            <ul class="list-group" id="owner_properties_div">
                <li class="list-group-item">
                <a :href="propertyLink" target="_blank"><div class="d-flex align-items-center">

                    <div class="flex-grow-1">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img v-if="property.first_image" :src="property?.first_image?.thumbnail" alt="" class="avatar-xs rounded">
                            </div>
                            <div class=" ms-2">
                                <h6 class="fs-14 mb-0">{{property?.ref_no}} - {{property?.property_category?.title}}</h6>
                                <small class="text-muted">{{property?.location}}</small>
                            </div>
                        </div>
                    </div>

                    <div class="flex-shrink-0">
                        <span class="badge badge-soft-success">{{property?.publish_status}}</span><br>
                        <span class="text-warning">AED {{property?.price?.price}}</span>
                    </div>

                </div></a>
            </li>

                <li class="list-group-item" v-if="contact">
               Owner :  <button type="button" class="btn  rounded-pill btn-light waves-effect capsule">
                    <span class="">
                       {{contact.first_name}} {{contact.last_name}}</span></button>
                </li>
                <li class="list-group-item" v-for="(agent, index) in deal.agents">
               {{index == 0 ? 'Property Agent' : 'Lead Agent'}} :  <button type="button" class="btn  rounded-pill btn-light waves-effect capsule">
                    <span class=" userDetails" :data-agent-id="agent.id">
                        <img v-if="agent.thumbnail" :src="agent.thumbnail" alt="" class="avatar-xs rounded-circle">{{agent.name}}</span></button>
                </li>
                <li class="list-group-item" v-if="contact">
                    Deal Price: {{deal.data.deal_price}}
                </li>
                <li class="list-group-item" v-if="contact">
                    Gross Commission: {{deal.data.gross_commission}}
                </li>
                <li class="list-group-item" v-if="contact">
                    Net Commission: {{deal.data.net_commission}}
                </li>

            </ul>
        </div>
    </div>
    <div v-if="formStep !==1" class="card">
        <div class="card-header">
            Pending Tasks
        </div>
        <div class="card-body">
            <div v-show="placeholder" class="row">
                <div class="col-12" v-for="index in 2" :key="index">
                    <CardPlaceholder></CardPlaceholder>
                </div>
            </div>
            <div v-show="!placeholder" v-for="(item, index) in dealTasks" :key="index" class="card mt-2">
                <div class="card-header">
                    <span class="badge badge-soft-info">System</span>
                    <h5>{{item.subject}}</h5>
                    <ul class="list-group list-group-horizontal-md mt-2">
                        <li class="list-group-item">
                            <div class="text-mute">Due:</div>
                            <div class="">{{item.due_date}}</div>

                        </li>
                        <li class="list-group-item">
                            <div class="text-mute">Assigned to:</div>
                            <div class="">
                                <div class="d-flex align-items-center userDetails" :data-agent-id="item.user_id"
                                     :title="item?.user?.full_name">
                                    <div class="flex-shrink-0 me-2">
                                        <img
                                            :src="item?.user?.image_url"
                                            alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">{{ item?.user?.full_name }}<p title="Created at" class="mb-0">
                                        <small>Agent</small></p></div>
                                </div>
                            </div>
                        </li>

                    </ul>
                    <div class="mt-2">
                        <div class="btn btn-light">Deny</div>
                        <div class="btn btn-light action-add float-end text-white">Approve</div>
                    </div>
                </div>
            </div>

            <div class="align-items-center text-center">
                <a href="javascript:void(0)" class="btn btn-light width-100  btn-sm">Show all tasks</a>
            </div>
        </div>
    </div>
    <div class="row">
        <div v-show="placeholder" class="col-12">
            <CardPlaceholder></CardPlaceholder>
        </div>
        <div v-show="!placeholder" class="col-12">
            <NoteActivity :deals="deal.data"></NoteActivity>
        </div>
    </div>
    </div>
</template>
<script setup>
import { ref } from "vue";
import CardPlaceholder from "../../../utilities/placeholder/CardPlaceholder.vue";
import NoteActivity from "../widgets/NoteActivity.vue";
const placeholder = ref(true);
import { useDealStore } from "../store/dealState";
const { formStep } = useDealStore();
const deal = useDealStore();
const property = deal?.data?.property || {};
const propertyLink = route('admin.listing.index')+'?ref_number='+property?.ref_no;
const contact = deal?.data?.contact || {};
setTimeout(() => {
    placeholder.value = false;
}, 3000);

const props = defineProps({
    dealTasks: {
        type: Object,
        required: true,
    },
});
</script>
<style scoped>
.width-100 {
    width: 100%;
}
.capsule{
    padding: 4px 5px;
}
</style>
