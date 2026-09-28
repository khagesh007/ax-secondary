<template>
  <v-container>
    <!-- Add New Button -->
    <v-btn color="primary" @click="showModal = true">Add New</v-btn>

    <!-- Modal -->
    <v-dialog v-model="showModal" max-width="1200px">
      <v-card>
        <v-card-title>
          <span class="text-h6">Add New Pipeline</span>
          <v-spacer></v-spacer>
          <v-btn icon @click="showModal = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-card-title>
        <v-card-text>
          <!-- Horizontal Scrollable Table -->
          <v-container>
            <v-row>
              <v-col>
                <v-btn color="secondary" @click="addColumn">Add New Pipeline</v-btn>
              </v-col>
            </v-row>
            <div style="display: flex; flex-wrap: nowrap; overflow-x: auto;">
              <v-row style="flex: 0 0 auto;">
                <v-col v-for="(agent, index) in agents" :key="index" class="pa-2" style="min-width: 300px;">
                  <v-card outlined>
                    <v-card-title>Pipeline {{ index + 1 }}</v-card-title>
                    <v-card-text>
                      <v-select
                        v-model="agent.agent"
                        :items="agentOptions"
                        label="Agent"
                        required
                      ></v-select>
                      <v-select
                        v-model="agent.type"
                        :items="['Rent', 'Sale']"
                        label="Type"
                        required
                      ></v-select>
                        <v-select
                            v-model="agent.propertyDetails"
                            :items="['axc-34343', 'axc-343432', 'axc-34576578', 'axc-34343']"
                        label="Property Details"
                        placeholder="Select Property Type"
                        required
                        ></v-select>
                      <v-select
                        v-model="agent.clientName"
                        :items="clientOptions"
                        label="Client Name (Optional)"
                      ></v-select>
                      <v-select
                        v-model="agent.leadSource"
                        :items="leadSourceOptions"
                        label="Lead Source (Optional)"
                      ></v-select>
                      <v-textarea
                        v-model="agent.comments"
                        label="Comments / Notes"
                      ></v-textarea>
                      <v-text-field
                        v-model="agent.dealValue"
                        label="Deal Value (AED)"
                        type="number"
                      ></v-text-field>
                        <v-row>
                            <v-col cols="6">
                                <v-select
                                    v-model="agent.expectedCommissionType"
                                    :items="['AED', '%']"
                                    label="Expected Commission Type"
                                    required
                                ></v-select>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model="agent.expectedCommission"
                                    :label="agent.expectedCommissionType === 'Percentage (%)' ? 'Percentage (%)' : 'Value (AED)'"
                                    type="number"
                                    required
                                ></v-text-field>
                            </v-col>
                        </v-row>

                      <v-text-field
                        v-model="agent.expectedCommission"
                        label="Expected Commission"
                        type="number"
                      ></v-text-field>
                      <v-select
                        v-model="agent.pipelineStatus"
                        :items="['Active', 'Lost', 'Won']"
                        label="Pipeline Status"
                        required
                      ></v-select>
                        <label class="mt-2">Date Added</label>
                        <VueDatePicker
                            v-model="agent.dateAdded"
                            :input-props="{ placeholder: 'Select Date', label: 'Date Added (Optional)' }"
                            :mode="'date'"
                        />
                        <label class="mt-2">Target Close Date</label>
                        <VueDatePicker
                            v-model="agent.targetCloseDate"
                            :input-props="{ placeholder: 'Select Date', label: 'Target Close Date (Optional)' }"
                            :mode="'date'"
                        />
                      <v-textarea
                        v-if="agent.pipelineStatus === 'Lost'"
                        v-model="agent.reasonForLoss"
                        label="Reason for Loss (Required)"
                        required
                      ></v-textarea>
                      <v-textarea
                        v-model="agent.managerRemarks"
                        label="Manager Remarks"
                      ></v-textarea>
                      <v-select
                        v-model="agent.priorityLevel"
                        :items="['High', 'Medium', 'Low']"
                        label="Priority Level"
                      ></v-select>
                    </v-card-text>
                  </v-card>
                </v-col>
              </v-row>
            </div>
          </v-container>
        </v-card-text>
        <v-card-actions>
          <v-btn color="primary" @click="savePipeline">Save</v-btn>
          <v-btn text @click="showModal = false">Cancel</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </v-container>
</template>

<script setup>
import { ref } from "vue";

const showModal = ref(false);
const agents = ref([]);
const agentOptions = ["Agent 1", "Agent 2", "Agent 3"]; // Example agent options
const clientOptions = ["Client 1", "Client 2", "Client 3"]; // Example client options
const leadSourceOptions = ["Property finder", "Bayut", "Dubizzle"]; // Example lead source options

const addColumn = () => {
  agents.value.push({
    agent: null,
    type: null,
    propertyDetails: "",
    clientName: null,
    leadSource: null,
    comments: "",
    dealValue: null,
    expectedCommissionType: null,
    expectedCommission: null,
    pipelineStatus: null,
    dateAdded: null,
    targetCloseDate: null,
    reasonForLoss: "",
    managerRemarks: "",
    priorityLevel: null,
  });
};

const savePipeline = () => {
  console.log("Pipeline data:", agents.value);
  showModal.value = false;
};
</script>
