<template>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div>
                    <div class="card-header align-items-center d-flex">
                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0 ">Pipelines</h4>
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item">
                                    <router-link to="/">Pipeline Dashboard</router-link>
                                </li>
                                <li class="breadcrumb-item active">Manage</li>
                            </ol>
                        </div>

                        <div class="flex-shrink-0">
                            <div class="form-check form-switch form-switch-right form-switch-md">

                                <v-btn class="btn btn-ax-color float-end" @click="addPipeline"><i
                                    class="ri-add-line align-bottom me-1"></i> Add New Pipeline
                                </v-btn>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <v-card class="mb-3">

        <v-card-text>

            <!-- Add New Pipeline Button -->
            <!--            <v-row v-if="showFilters">-->
            <v-row>

                <v-col cols="12" lg="3" xl="2">
                    <!-- Radio Buttons -->
                    <div class="btn-group w-100 h-100" role="group" aria-label="Basic radio toggle button group">
                        <input type="radio" class="btn-check" v-model="filters.status" value="Active" name="btnradio"
                               id="btnradio1" autocomplete="off" :disabled="tableXhrRequest">
                        <label class="btn btn-outline-secondary ma-0" for="btnradio1">Active</label>

                        <input type="radio" class="btn-check" v-model="filters.status" name="btnradio" value="Won"
                               id="btnradio3" autocomplete="off" :disabled="tableXhrRequest">
                        <label class="btn btn-outline-secondary border-s-0 border-e-0 ma-0" for="btnradio3">Won</label>

                        <input type="radio" class="btn-check" v-model="filters.status" value="Lost" name="btnradio"
                               id="btnradio2" autocomplete="off" :disabled="tableXhrRequest">
                        <label class="btn btn-outline-secondary ma-0" for="btnradio2">Lost</label>
                    </div>
                </v-col>

                <!--                <v-col cols="5" v-if="isAgent"></v-col>-->

                <v-col cols="12" lg="3" md="6" v-if="!isAgent">
                    <v-autocomplete
                        v-model="filters.department"
                        :items="departments.data"
                        :loading="departments.loading"
                        item-title="name"
                        item-value="value"
                        label="Select Department"
                        @update:model-value="handleDepartmentChange"
                        variant="outlined"
                        class="bg-white ml-1"
                        density="compact"
                        hide-details
                    >
                    </v-autocomplete>
                </v-col>

                <v-col cols="12" lg="3" md="6" v-if="!isAgent">
                    <UsersAutocomplete
                        :filters="filters"
                        :model="filters.user_id || null"
                        label="Select Agent"
                        return-object
                        @update:model="(value) => filters.user_id = value"
                        hide-details
                    />
                </v-col>

                <!--                <v-spacer/>-->

                <!--                <v-col cols="2" md="2">-->

                <!--                    <VueDatePicker v-model="date" range :multi-calendars="{ solo: true }"-->
                <!--                                   :preset-dates="presetDates" :enable-time-picker="false">-->
                <!--                        <template #preset-date-range-button="{ label, value, presetDate }">-->
                <!--                                <span role="button" :tabindex="0" @click="presetDate(value)"-->
                <!--                                      @keyup.enter.prevent="presetDate(value)" @keyup.space.prevent="presetDate(value)">-->
                <!--                                    {{ label }}-->
                <!--                                </span>-->
                <!--                        </template>-->
                <!--                    </VueDatePicker>-->

                <!--                </v-col>-->

                <!--                <v-col cols="12" md="3">
                                    <div class="d-flex justify-end gap-1">
                                        <button type="button" class="btn btn-primary" @click="resetFilters()">
                                            Reset Filters
                                        </button>
                                        <button type="button" class="btn btn-light" @click="resetFilters();showFilters = false">
                                            Close
                                        </button>
                                    </div>
                                </v-col>-->
            </v-row>

            <!--            <v-row v-if="!showFilters">
                            <v-spacer/>
                            <v-col cols="12" md="2" class="text-right">
                                <button type="button" class="btn btn-primary" @click="showFilters = true">
                                    <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                    Advanced Filters
                                </button>
                            </v-col>
                        </v-row>-->

            <!-- Simple Table with Form Inputs -->
            <v-row>
                <v-col cols="12" class="overflow-auto">
                    <table class="table align-middle">
                        <thead>
                        <tr style="background: #f0e4dd;white-space: nowrap">
                            <th>Agent</th>
                            <th>Type</th>
                            <th>Property Details</th>
                            <th>Deal Value</th>
                            <th>Commission</th>
                            <th>Comment</th>
                            <th>Other</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody style="vertical-align: center;">

                        <tr v-for="(pipeline, index1) in editablePipeline" :key="index1"
                            :class="{ 'row-flash': animatedRowIndex === index1 }">
                            <td>
                                <div class="d-flex align-center gap-2" v-if="pipeline?.agent">
                                    <UserCard1 :user="pipeline.agent" :size="36"/>
                                </div>
                                <UsersAutocomplete
                                    :filters="filters"
                                    v-if="!pipeline.user_id"
                                    :model="pipeline.user_id"
                                    label="Select Agent"
                                    @update:model="(value) => pipeline.user_id = value"
                                    @selectedObject="userSelected($event, pipeline)"
                                    hide-details
                                />
                                <!--                                    <UsersAutocomplete v-if="pipeline.user_id" :filters="filters" :model="pipeline.user_id"-->
                                <!--                                                       @update:model="(value) => pipeline.user_id = value"-->
                                <!--                                                       @selectedObject="userSelected($event, pipeline)"-->
                                <!--                                    ></UsersAutocomplete>-->
                                <!--                                <VueMultiselect v-if="!pipeline.user_id"-->
                                <!--                                                v-model="pipeline.user_id"-->
                                <!--                                                :id="index1"-->
                                <!--                                                :allowEmpty="true"-->
                                <!--                                                :searchable="true" :close-on-select="true"-->
                                <!--                                                :show-label="true"-->
                                <!--                                                label="name"-->
                                <!--                                                :options="agents" :multiple="false" :taggable="true"-->
                                <!--                                                :selectLabel="''" track-by="name"-->
                                <!--                                                @Select="userSelected($event, pipeline)">-->
                                <!--                                </VueMultiselect>-->

                            </td>

                            <td>
                                <div class="btn-group" role="group" aria-label="Deal Type">
                                    <input type="radio" class="btn-check" :id="'dealtype-sale-' + index1"
                                           autocomplete="off" value="Sale" v-model="pipeline.deal_type"/>
                                    <label class="btn btn-sm btn-outline-secondary m-0"
                                           :for="'dealtype-sale-' + index1">Sale</label>

                                    <input type="radio" class="btn-check" :id="'dealtype-rent-' + index1"
                                           autocomplete="off" value="Rent" v-model="pipeline.deal_type"/>
                                    <label class="btn btn-sm btn-outline-secondary m-0"
                                           :for="'dealtype-rent-' + index1">Rent</label>
                                </div>
                            </td>
                            <td>
                                <textarea
                                    v-model="pipeline.property_details"
                                    class="form-control" rows="1"
                                    style="min-width: 180px"
                                    placeholder="Enter Property Details"
                                ></textarea>
                            </td>
                            <td>
                                <input type="number" style="min-width: 150px" class="form-control"
                                       v-model="pipeline.deal_value"
                                       placeholder="Enter Deal Value"/>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <input
                                        class="form-control" type="number"
                                        v-model="pipeline.commission_value"
                                        placeholder="Expected Commission"
                                        style="min-width: 180px;border-radius: 4px 0 0 4px; border-right: 0"
                                    />
                                    <select class="form-control text-center"
                                            v-model="pipeline.commission_type"
                                            style="min-width: 60px; border-radius: 0 4px 4px 0"
                                    >
                                        <option value="AED">AED</option>
                                        <option value="%">%</option>
                                    </select>
                                </div>

                            </td>
                            <td>
                                <textarea
                                    v-model="pipeline.comment"
                                    style="min-width: 180px"
                                    class="form-control" rows="1"
                                    placeholder="Enter comment"></textarea>
                            </td>
                            <!-- filepath: /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/js/pages/pipeline/Form.vue -->
                            <td>
                                <v-tooltip text="Select priority level for this pipeline">
                                    <template v-slot:activator="{ props }">
                                        <select
                                            v-bind="props"
                                            v-model="pipeline.priority"
                                            class="form-select form-select-sm"
                                            aria-label="Priority Level"
                                        >
                                            <option value="" disabled>Select Priority</option>
                                            <option value="High">High</option>
                                            <option value="Medium">Medium</option>
                                            <option value="Low">Low</option>
                                        </select>
                                    </template>
                                </v-tooltip>

                                <v-tooltip text="Expected closing date for this deal">
                                    <template v-slot:activator="{ props }">
                                        <input
                                            v-bind="props"
                                            type="date"
                                            class="form-control form-control-sm mt-1"
                                            v-model="pipeline.closing_date"
                                            placeholder="Closing Added"
                                        />
                                    </template>
                                </v-tooltip>

                                <v-tooltip text="Select status of this pipeline">
                                    <template v-slot:activator="{ props }">
                                        <select
                                            v-bind="props"
                                            v-model="pipeline.status"
                                            class="form-select form-select-sm mt-1"
                                            aria-label="Pipeline Status"
                                        >
                                            <option value="" disabled>Select Status</option>
                                            <option value="Active">Active</option>
                                            <option value="Won">Won</option>
                                            <option value="Lost">Lost</option>
                                        </select>
                                    </template>
                                </v-tooltip>
                            </td>
                            <td>
                                <div class="d-flex align-center gap-2">
                                    <!-- create save btn-->
                                    <button type="button" class="btn btn-sm btn-light btn-icon"
                                            @click="savePipeline(pipeline)">
                                        <v-icon>mdi-content-save</v-icon>
                                    </button>
                                    <button v-if="!pipeline?.id" type="button" class="btn btn-sm btn-light btn-icon"
                                            @click="removeTempPipeline(index1)">
                                        <v-icon>mdi-delete</v-icon>
                                    </button>
                                    <!---- cancel btn -->
                                    <button v-if="pipeline?.id" type="button" class="btn btn-sm btn-light btn-icon"
                                            @click="cancel(pipeline)">
                                        <v-icon>mdi-close</v-icon>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <tr v-for="(pipeline, index) in pipelines" :key="index">
                            <td>
                                <div class="d-flex align-center gap-2" v-if="pipeline?.agent">
                                    <UserCard1 :user="pipeline.agent" :size="36"
                                               @click.native="openModal(pipeline?.agent?.image_url)"/>
                                </div>
                            </td>

                            <td>
                                {{ pipeline.deal_type }}
                            </td>

                            <td>
                                <ul class="list-group" style="padding: 0; margin: 0; font-size: 10px">
                                    <li class="list-group-item " style="padding: 0;">
                                        <div class="flex-grow-1">
                                            <div class="d-flex" style="margin: 0;">
                                                <div class="ms-1" style="margin: 0;">
                                                    <h6 class="fs-12 mb-0" style="margin: 0;">
                                                        {{ pipeline.property_details }}
                                                    </h6>
                                                </div>
                                            </div>
                                        </div>

                                    </li>
                                </ul>
                            </td>
                            <td>
                                {{ numberFormat(pipeline.deal_value, false) }}
                            </td>
                            <td class="fs-12">
                                {{ numberFormat(pipeline.commission, false) }}
                                ({{
                                    pipeline.commission_type == 'AED' ? calculatePercentage(pipeline.deal_value, pipeline.commission_value) :
                                        pipeline.commission_value
                                }}%)
                            </td>
                            <td>
                                <p class="fs-11 ma-0"
                                   style="overflow-y: auto; max-height: 70px; min-width: 100px; max-width: 200px;">
                                    {{ pipeline.comment }}
                                </p>

                            </td>
                            <td>
                                <div class="fs-11">
                                    Priority: {{ pipeline.priority }}
                                    <br>
                                    Closing On: {{
                                        pipeline.closing_date ? new
                                        Date(pipeline.closing_date).toLocaleDateString() : 'N/A'
                                    }}
                                    <br>
                                    Status: <span :class="statusBadgeClass(pipeline.status)">
                                        {{ pipeline.status }}
                                </span>
                                </div>
                            </td>
                            <td>
                                <!-- create save btn-->
                                <button v-if="!pipeline?.id" type="button" class="btn btn-ax-color"
                                        @click="savePipelines">
                                    <v-icon>mdi-content-save</v-icon>
                                </button>


                                <div class="dropdown">
                                    <button class="btn btn-light btn-icon btn-sm dropdown" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false"><i
                                        class="ri-more-fill align-bottom"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                               v-if="pipeline?.id"
                                               @click="viewPipeline(pipeline)"
                                               title="View pipeline details"
                                               href="javascript:void(0)">
                                                <v-icon>mdi-eye</v-icon>
                                                Details</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                               v-if="pipeline?.id"
                                               @click="editPipeline(pipeline)"
                                               title="Edit existing data in this document"
                                               href="javascript:void(0)">
                                            <v-icon>mdi-pencil</v-icon>
                                            Edit</a></li>

                                        <li><a class="dropdown-item" href="javascript:void(0)"
                                               title="Download this file"
                                               v-if="pipeline?.id"
                                               @click="openDeleteModal(pipeline.id)"
                                        >
                                            <v-icon>mdi-delete</v-icon>
                                            Delete</a>
                                        </li>


                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="pipelines.length">
                            <td colspan="3"></td>
                            <td>Total Deal Value:
                                <strong>{{ numberFormat(totalDealValue, false) }}</strong>
                            </td>
                            <td colspan="2">
                                Total Comm: <b>{{ numberFormat(totalCommissionValue, false) }}</b>
                                <br>
                                Avg Comm %:
                                <b>{{ avgCommissionPercentage }}%</b>
                            </td>
                            <td colspan="2">

                            </td>
                        </tr>
                        </tbody>
                    </table>
                </v-col>
            </v-row>
        </v-card-text>


    </v-card>


    <v-dialog v-model="deleteDialog" max-width="400">
        <v-card>
            <v-card-title class="headline">Confirm Delete</v-card-title>
            <v-card-text>Are you sure you want to delete this pipeline?</v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="grey" text @click="deleteDialog = false">Cancel</v-btn>
                <v-btn color="red" text @click="confirmDelete">Delete</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
    <!-- Pipeline Details Modal -->
    <v-dialog v-model="showPipelineDetailsModal" max-width="600">
        <v-card>
            <v-card-title class="headline">Pipeline Details</v-card-title>
            <v-card-text>
                <table class="table table-bordered pipeline-details-table">
                    <tbody>
                    <tr>
                        <th>Agent</th>
                        <td>{{ selectedPipeline?.agent?.name || 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Deal Type</th>
                        <td>{{ selectedPipeline?.deal_type || 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Property Details</th>
                        <td>{{ selectedPipeline?.property_details || 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Deal Value</th>
                        <td>{{ numberFormat(selectedPipeline?.deal_value, false) }}</td>
                    </tr>

                    <tr>
                        <th>Commission Value</th>
                        <td>{{ numberFormat(selectedPipeline?.commission, false) }}
                            ({{
                                selectedPipeline.commission_type == 'AED' ? calculatePercentage(selectedPipeline.deal_value, selectedPipeline.commission_value) :
                                    selectedPipeline.commission_value
                            }}%)
                        </td>
                    </tr>
                    <tr>
                        <th>Priority</th>
                        <td>{{ selectedPipeline?.priority || 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span :class="selectedPipeline?.status">
                            {{ selectedPipeline?.status || 'N/A' }}</span></td>
                    </tr>
                    <tr>
                        <th>Closing Date</th>
                        <td>{{
                                selectedPipeline?.closing_date ? new Date(selectedPipeline.closing_date).toLocaleDateString() : 'N/A'
                            }}
                        </td>
                    </tr>
                    <tr>
                        <th>Comment</th>
                        <td>{{ selectedPipeline?.comment || 'N/A' }}</td>
                    </tr>
                    <tr v-if="selectedPipeline.created_by">
                        <th>Created by</th>
                        <td>
                            <UserCard1 :user="selectedPipeline.created_by" :size="36"
                                       :date="selectedPipeline.created_at"
                                       @click.native="openModal(selectedPipeline?.created_by?.image_url)"/>
                        </td>
                    </tr>
                    <tr v-if="selectedPipeline.updated_by">
                        <th>Updated By</th>
                        <td>
                            <UserCard1 :user="selectedPipeline.updated_by" :size="36"
                                       :date="selectedPipeline.updated_at"
                                       @click.native="openModal(selectedPipeline?.updated_by?.image_url)"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="primary" text @click="showPipelineDetailsModal = false">Close</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script setup>
import {ref, onMounted, watch, nextTick} from "vue";
import VueMultiselect from "vue-multiselect";
import 'vue-multiselect/dist/vue-multiselect.css';
import UsersAutocomplete from "../../components/common/UsersAutocomplete.vue";
import UserCard1 from "../../components/common/UserCard1.vue";

const agents = ref([]);
const agentOptions = ref([]);
const pipelines = ref([]);
const editablePipeline = ref([]);
const animatedRowIndex = ref(null);
const selectedFilterUser = ref(null);
const totalDealValue = ref(0);
const avgCommissionPercentage = ref(0);
const totalCommissionValue = ref(0);
const options = ref([]);
const isLoading = ref(false);
const showFilters = ref(false);
const filters = ref({
    department: '',
    status: null,
    deal_type: null,
    agent: null,
    user_id: null,
    priority: null,
    closing_date: null,
});
const deleteDialog = ref(false);
const pipelineToDelete = ref(null);
const tableXhrRequest = ref(false);
const department = ref(null);
const departments = ref({
    data: [],
    loading: true,
})
const showPipelineDetailsModal = ref(false); // Modal visibility state
const selectedPipeline = ref(null); // Selected pipeline data
const isAgent = ref(false); // Check if the user is an agent
const statusBadgeClass = (value) => {
    switch (value) {
        case 'Active':
            return "badge align-middle badge color-bg-8";
        case 'Won':
            return "badge align-middle badge color-bg-12";
        case 'Lost':
            return "badge align-middle badge color-bg-17";
        default:
            return '-';
    }
};

const addPipeline = () => {
    editablePipeline.value.unshift({
        id: null,
        pipeline_id: null,
        user_id: null,
        agent: null,
        deal_type: "Sale",
        property_details: "",
        deal_value: null,
        commission_type: '%',
        commission_value: null,
        comment: "",
        commission: null,
        priority: "Medium",
        status: "Active",
        closing_date: null,
    });
    nextTick(() => {
        animatedRowIndex.value = 0;
        setTimeout(() => {
            animatedRowIndex.value = null;
        }, 1200); // Animation duration in ms
    });
};

const getAgents = async () => {
    try {
        agents.value = [];

        const response = await axios.get('/pipeline/agents?department=' + filters.value.department);
        if (response.data.status === "success") {

            agents.value = response.data.data;

        } else {
            console.error('Failed to fetch agents:', response.data.message);
        }
    } catch (error) {
        console.error('Error fetching agents:', error);
    }
};

const userSelected = (event, item) => {
    console.log('User selected:', event);
    item.agent = event;
    item.user_id = event.id;
};

function clearUserFilter() {
    filters.value.user_id = null;
    filters.value.agent = null;
    selectedFilterUser.value = null;
}

const searchAgents = async (query) => {
    if (!query) {
        options.value = [];
        return;
    }

    isLoading.value = true;

    try {
        const response = await axios.get('/pipeline/agents', {
            params: {
                q: query,
                department: filters.value.department  // Ensure department is included in the request
            },
        });

        options.value = response.data.data; // Expected format: [{ id, name }, ...]
    } catch (error) {
        console.error("Error fetching agents:", error);
        options.value = [];
    } finally {
        isLoading.value = false;
    }
};


const numberFormat = (value, inDecimal = true) => {
    if (value === null || value === undefined) {
        return '';
    }
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'AED',
        minimumFractionDigits: inDecimal ? 2 : 0,
        maximumFractionDigits: inDecimal ? 2 : 0,
    }).format(value);
};

const viewPipeline = (pipeline) => {
    if (!pipeline || !pipeline.id) {
        showToastify('Pipeline not found or unsaved', 'error');
        return;
    }
    selectedPipeline.value = pipeline; // Set the selected pipeline for the modal
    showPipelineDetailsModal.value = true; // Open the modal
};

const editPipeline = (pipeline) => {
    pipeline.isEditing = true;
    if (pipeline.id === null) {
        showToastify('Cannot edit unsaved pipeline', 'error');
        return;
    }
    editablePipeline.value = [pipeline];
    nextTick(() => {
        animatedRowIndex.value = 0;
        setTimeout(() => {
            animatedRowIndex.value = null;
        }, 1200);
    });
};
const cancel = (pipeline) => {

    editablePipeline.value = editablePipeline.value.filter(p => p !== pipeline);
};


const savePipeline = async (pipeline) => {
    if (!pipeline) {
        console.error('No pipeline data provided for saving');
        return;
    }

    if (!pipeline.user_id) {
        showToastify('Please select an agent for the pipeline', 'error');
        return;
    }
    //check if new pipeline has deal_value and commission_value
    if (pipeline.deal_value === null || pipeline.deal_value === undefined || pipeline.deal_value === '') {
        showToastify('Please enter a valid deal value', 'error');
        return;
    }
    if (pipeline.commission_value === null || pipeline.commission_value === undefined || pipeline.commission_value === '') {
        showToastify('Please enter a valid commission value', 'error');
        return;
    }

    axios.post('/pipeline/save', {pipelines: [pipeline]})
        .then(response => {
            if (response.data.status === "success") {
                showToastify(response.data.message, 'success');
                console.log('Pipeline saved successfully:', response.data.message);
                editablePipeline.value = [];
                getPipelines();
            } else {
                showToastify('Failed to save pipeline', 'error');
                console.error('Failed to save pipeline:', response.data.message);
            }
        })
        .catch(error => {
            console.error('Error saving pipeline:', error);
        });
};

const getPipelines = async () => {
    if (tableXhrRequest.value) {
        return;
    }
    tableXhrRequest.value = true;
    let params = {
        department: filters.value.department,
        status: filters.value.status,
        deal_type: filters.value.deal_type,
        agent: filters.value.agent,
        user_id: filters.value.user_id,
        priority: filters.value.priority,
        closing_date: filters.value.closing_date,
    };
    try {
        // const response = await axios.get('/pipeline/list');
        const queryParams = new URLSearchParams(params).toString();
        const response = await axios.get(`/pipeline/list?${queryParams}`);
        if (response.data.status === "success") {
            pipelines.value = response.data.data;
            tableXhrRequest.value = false;
            // totalDealValue.value = pipelines.value.reduce((sum, p) => parseInt(sum) + parseInt(p.deal_value || 0), 0);
            // let avgCommPerc = pipelines.value.reduce((sum, p) => parseInt(sum) + parseInt(p.commission_value || 0), 0) / pipelines.value.length || 0;
            // avgCommissionPercentage.value = avgCommPerc.toFixed(2);
        } else {
            showToastify('Failed to fetch pipelines', 'error');
            console.error('Failed to fetch pipelines:', response.data.message);
            tableXhrRequest.value = false;
        }
    } catch (error) {
        showToastify('Error fetching pipelines', 'error');
        tableXhrRequest.value = false;
        console.error('Error fetching pipelines:', error);
    }
};

const removeTempPipeline = (index) => {
    if (editablePipeline.value[index].id === null) {
        editablePipeline.value.splice(index, 1);
    } else {
        showToastify('Cannot remove saved pipeline', 'error');
    }
};
const removePipeline = async (pipeline_id) => {
    if (pipeline_id !== null && pipeline_id !== undefined) {
        axios.post('/pipeline/delete/' + pipeline_id)
            .then(response => {
                if (response.data.status === "success") {

                    showToastify(response.data.message, 'success');
                    pipelines.value = pipelines.value.filter(p => p.id !== pipeline_id);
                } else {
                    showToastify('Failed to remove pipeline', 'error');
                    console.error('Failed to remove pipeline:', response.data.message);
                }
            })
            .catch(error => {
                console.error('Error removing pipeline:', error);
            });
    } else {
        // Remove unsaved pipeline (no id)
        pipelines.value = pipelines.value.filter(p => p.pipeline_id !== pipeline_id);
    }
};
const openDeleteModal = (id) => {
    pipelineToDelete.value = id;
    deleteDialog.value = true;
}

const confirmDelete = () => {
    if (pipelineToDelete.value !== null) {
        removePipeline(pipelineToDelete.value);
        deleteDialog.value = false;
        pipelineToDelete.value = null;
    }
}
const getDepartments = async () => {
    departments.value.loading = true;

    try {
        const {data} = await axios.get("/reporting/dashboard/get-departments");
        if (data.data) {
            departments.value.data = data.data;
            isAgent.value = data.is_agent || false;
            if (data.data.length > 0) {
                department.value = data.data[0].value;
                filters.value.department = data.data[0].value;
                await getAgents();
                await getPipelines();
            } else {
                await getPipelines();
            }
        }
    } catch (error) {
        console.error(error);
    } finally {
        departments.value.loading = false;
    }
};
const calculatePercentage = (value1, value2) => {
    if (!value1 || !value2) return 0;
    return ((Number(value2) / Number(value1)) * 100).toFixed(2);
}
const handleDepartmentChange = async () => {
    // Reset all dependent values
    filters.value.user_id = null;
    filters.value.agent = null;
    selectedFilterUser.value = null;

    // Clear agents array first
    agents.value = [];

    // Wait for the next tick to ensure reactivity
    await nextTick();

    // Fetch new agents
    await getAgents();
};
const calculateTotal = (pipelines) => {
    if (!pipelines || pipelines.length === 0) {
        totalDealValue.value = 0;
        avgCommissionPercentage.value = 0;
        return;
    }

    // Calculate total deal value
    totalDealValue.value = pipelines.reduce((sum, p) => sum + (parseFloat(p.deal_value) || 0), 0);

    // Calculate average commission percentage using the commission column
    // Convert commission amounts back to percentages based on deal_value
    let totalCommissionPercentage = 0;
    let validPipelines = 0;
    let totalCommissionVal = 0;

    pipelines.forEach(p => {
        if (p.deal_value && p.commission && parseFloat(p.deal_value) > 0) {
            totalCommissionVal += parseFloat(p.commission);
            const commissionPercentage = (parseFloat(p.commission) / parseFloat(p.deal_value)) * 100;
            totalCommissionPercentage += commissionPercentage;
            validPipelines++;
        }
    });
    totalCommissionValue.value = totalCommissionVal.toFixed(2);
    const avgCommPerc = validPipelines > 0 ? totalCommissionPercentage / validPipelines : 0;
    avgCommissionPercentage.value = avgCommPerc.toFixed(2);
};
watch(pipelines, (newPipelines) => {
    calculateTotal(newPipelines);
}, {deep: true});
// Watch each pipeline for commission calculation
watch(
    editablePipeline,
    (newPipelines) => {
        newPipelines.forEach((pipeline) => {
            if (pipeline.commission_type === "%") {
                pipeline.commission =
                    pipeline.deal_value && pipeline.commission_value
                        ? (Number(pipeline.deal_value) * Number(pipeline.commission_value)) / 100
                        : 0;
            } else if (pipeline.commission_type === "AED") {
                pipeline.commission = Number(pipeline.commission_value) || 0;
            } else {
                pipeline.commission = 0;
            }

        });
    },
    {deep: true}
);
watch(
    filters,
    async () => {
        await getPipelines();
        await getAgents();
    },
    {deep: true}
);


const userFilterSelected = (event) => {
    filters.value.user_id = event.id;
    filters.value.agent = event;
    //filters.value.agent = selected?.name || null;
};
const resetFilters = () => {
    department.value = departments.value.data[0]?.value || '';
    filters.value.department = department.value;
    filters.value.user_id = null
    selectedFilterUser.value = null
    filters.value.status = null

}

onMounted(async () => {
    await getDepartments();
    agents.value = [];


});
</script>
<style scoped>
.btn-ax-color {
    background: #d3a188 !important;
}

.btn-outline-secondary {
    border-color: #d3a188;
    color: #000000;
}

.btn-check:active + .btn-outline-secondary,
.btn-check:checked + .btn-outline-secondary,
.btn-outline-secondary.active,
.btn-outline-secondary.dropdown-toggle.show,
.btn-outline-secondary:active {
    background-color: #d3a188;
    border-color: #d3a188;
    color: #fff;
}

.custom-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
}

.custom-table thead {
    background-color: #e0e0e0;
    text-align: left;
}

.custom-table th {
    padding: 12px 16px;
    font-weight: bold;
    font-size: 14px;
    border-bottom: 1px solid #ccc;
}

.custom-table tbody tr {
    border-bottom: 1px solid #ccc;
}

.custom-table td {
    padding: 12px 16px;
    font-size: 14px;
    vertical-align: middle;
}

.custom-table tbody tr:hover {
    background-color: #f1f1f1;
}

.custom-table tbody tr:last-child {
    border-bottom: none;
}

.custom-table th,
.custom-table td {
    text-align: left;
}

.custom-table th:first-child,
.custom-table td:first-child {
    border-left: none;
}

.custom-table th:last-child,
.custom-table td:last-child {
    border-right: none;
}

.custom-input {
    height: 56px;
}

@keyframes rowFlash {
    0% {
        background-color: #ffe9c7;
    }

    60% {
        background-color: #ffe9c7;
    }

    100% {
        background-color: transparent;
    }
}

.row-flash {
    animation: rowFlash 1.2s ease;
}

.multiselect__content-wrapper {
    width: 200px !important;
}

.btn-outline-secondary:hover {
    background-color: #d3a188;
}

.multiselect__option--highlight {
    background-color: #dff0d8 !important;
    color: #333;
}

.vue-select-option {
    display: inline-block !important;
    margin-bottom: 0px !important;
    padding-top: 0px !important;
}

.multiselect {
    border: 0.5px solid;
    border-radius: 5px;
    max-height: 35px;
}

.multiselect__tags {
    height: 35px !important;
}

.pipeline-details-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.pipeline-details-table th, .pipeline-details-table td {
    padding: 8px;
    text-align: left;

}

.pipeline-details-table th {
    background-color: #f8f9fa;
}

.Active {
    color: #007bff;
}

.Won {
    color: #28a745;

}

.Lost {
    color: #dc3545;
}
</style>
