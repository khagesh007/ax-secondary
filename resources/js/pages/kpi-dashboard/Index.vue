<script setup>

import {computed, onMounted, ref, watch} from "vue";
import LiveListings from "../../components/kpi/LiveListings.vue";
import SalesTarget from "../../components/kpi/SalesTarget.vue";
import NewListings from "../../components/kpi/NewListings.vue";
import ActiveTruBroker from "../../components/kpi/ActiveTruBroker.vue";
import ActiveSuperAgent from "../../components/kpi/ActiveSuperAgent.vue";
import moment from "moment";
import AgentInsights from "../../components/kpi/AgentInsights.vue";
import Overall from "../../components/kpi/Overall.vue";
import GroupInsights from "../../components/kpi/GroupInsights.vue";

const date = ref(new Date());

const formatDate = (date) => {
  return moment(date).format('YYYY-MM');
};

const tab = ref('Overall')

const tabs = ref([
  'Overall',
  'Sales Target',
  'Live Listings',
  'New Listings',
  'Pipelines',
  'TruChecked Listings',
  'Active TruBroker',
  'Active Super Agent'
])

const permissions = ref({})

const overview = ref({
  data: {},
  loading: true,
})

const getOverviewData = async (loading = true) => {
  if (loading) {
    overview.value.loading = true;
  }

  let params = {
    month: moment(date.value).format('YYYY-MM')
  };

  try {
    const {data} = await axios.get(
        "/reports/kpi-v3/kpi-overview",
        {params: params}
    );
    if (data.data) {
      overview.value.data = data.data;
      permissions.value = data.permissions;
    }
  } catch (error) {
    console.error(error);
  } finally {
    overview.value.loading = false;
  }
};

const summary = ref({
  data: [],
  loading: true,
})

const groups = computed(() => {
  return Object.values(summary.value.data)
      .map(item => ({
        id: item.group?.id,
        name: item.group?.name,
        user_id: item.user?.id,
        user_name: item.user?.name,
        user_image: item.user?.image
      }))
      .filter(group => group.id !== undefined);
});

const getTeamSummaryData = async (loading = true) => {
  if (loading) {
    summary.value.loading = true;
  }

  let params = {
    month: moment(date.value).format('YYYY-MM')
  };

  try {
    const {data} = await axios.get(
        "/reports/kpi-v3/kpi-team-summary",
        {params: params}
    );
    if (data.data) {
      summary.value.data = data.data;
    }
  } catch (error) {
    console.error(error);
  } finally {
    summary.value.loading = false;
  }
};

const members = ref({
  data: [],
  loading: true,
})

const negativeMembers = ref({
  data: [],
  loading: true,
});

const getTeamMemberData = async (loading = true) => {
  if (loading) {
    members.value.loading = true;
  }

  const newData = []; // Temporary array to hold the new data

  // members.value.data = []; // Reset previous data to an empty array

  try {
    for (const group of groups.value) {

      let params = {
        month: moment(date.value).format('YYYY-MM'),
        group_id: group.id
      };

      const {data} = await axios.get("/reports/kpi-v3/team-member-overview", {params});

      newData.push({
        group_id: group.id,
        group_name: group.name,
        user_id: group.user_id,
        user_name: group.user_name,
        user_image: group.user_image,
        members: data.data || [] // Store response inside "members"
      });
    }

    // Assign the new array to `members.value.data` while keeping reactivity intact
    members.value = { ...members.value, data: newData };

      // Push response as an object, including group details
      // members.value.data.push({
      //   group_id: group.id,
      //   group_name: group.name,
      //   user_id: group.user_id,
      //   user_name: group.user_name,
      //   user_image: group.user_image,
      //   members: data.data || [] // Store response inside "members"
      // });

      // Optional delay
      // await new Promise(resolve => setTimeout(resolve, 300));
  } catch (error) {
    console.error(error);
  } finally {
    members.value.loading = false;
  }
};

onMounted(async () => {
  await getOverviewData();
  await getTeamSummaryData();
  await getTeamMemberData();
});

watch(date, async (newVal) => {
  await getOverviewData();
  await getTeamSummaryData();
  await getTeamMemberData();
});

const refresh = ref({
  teamLoading: false,
  agentLoading: false,
})

const refreshTeamsData = async () => {
  refresh.value.teamLoading = true;

  let params = {
    month: moment(date.value).format('YYYY-MM')
  };

  try {
    const response = await axios.get('/reports/kpi-v2/refresh-team-reports', {params});
    if (response.data.status) {
      await getOverviewData();
      await getTeamSummaryData();
    }
  } catch (e) {
    console.error(e);
  } finally {
    refresh.value.teamLoading = false;
  }
};

const refreshAgentsData = async () => {
  refresh.value.agentLoading = true;

  let params = {
    month: moment(date.value).format('YYYY-MM')
  };

  try {
    const response = await axios.get('/reports/kpi-v2/refresh-agent-reports', {params});
    if (response.data.status) {
      await getTeamMemberData();
    }
  } catch (e) {
    console.error(e);
  } finally {
    refresh.value.agentLoading = false;
  }
};

const filteredNegativeMembers = ref([]);

const filteredMembers = computed(() => {

  if (!members.value.data || members.value.data.length === 0) return {};

  return members.value.data.map(group => {
    let filteredMembers = [...(group.members || [])]; // Ensure it's an array

    if (['Sales Target', 'Live Listings', 'New Listings', 'TruChecked Listings'].includes(tab.value)) {
      const targetKeyMap = {
        'Sales Target': 'sales_value',
        'Live Listings': 'live_listings',
        'New Listings': 'new_listings',
          'TruChecked Listings': 'tru_checked_listings',
      };
      const targetKey = targetKeyMap[tab.value];

      // Sort by the relevant KPI percentage
      filteredMembers.sort((a, b) => {
        const aTarget = a.targets.find(t => t.kpi === targetKey);
        const bTarget = b.targets.find(t => t.kpi === targetKey);

        const aPercentage = aTarget ? aTarget.percentage : 0;
        const bPercentage = bTarget ? bTarget.percentage : 0;

        return bPercentage - aPercentage; // Descending order
      });
        filteredNegativeMembers.value = [];
    } else if (tab.value === 'Active TruBroker') {
        let allFilteredMembers = filteredMembers;
      filteredMembers = filteredMembers.filter(member => member.flags?.is_truBroker);
      filteredNegativeMembers.value = allFilteredMembers.filter(member => member.flags?.is_truBroker === 0);
    } else if (tab.value === 'Active Super Agent') {
        let allFilteredMembers = filteredMembers;
      filteredMembers = filteredMembers.filter(member => member.flags?.is_super_agent);
        filteredNegativeMembers.value = allFilteredMembers.filter(member => member.flags?.is_super_agent === 0);

    }


    return {
      ...group,
      members: filteredMembers,
        negativeMembers: filteredNegativeMembers.value,
    };
  });
});

</script>

<template>
  <v-app>
    <v-container fluid>
      <div class="mx-3">
        <v-row align="center" style="background: white; border-radius: 5px;">
          <v-col cols="12" md="6">
            <v-card-title class="pb-0">
              Key Performance Indicators
            </v-card-title>
            <v-card-subtitle class="pb-2">
              Overview of Team Performance
            </v-card-subtitle>
          </v-col>
          <v-col cols="12" md="6">
            <div class="d-flex justify-end">
              <VueDatePicker
                  style="width: 200px"
                  v-model="date"
                  :month-picker="true"
                  :enable-time-picker="false"
                  :format="formatDate"
              >
              </VueDatePicker>

            </div>
          </v-col>
        </v-row>
      </div>

      <v-card flat class="mt-5">

        <v-card-text>
            <div class="d-flex align-center justify-space-between">
                <div class="flex-1-1 pe-3" style="min-width: 0; max-width: 880px;">
                    <v-tabs
                        v-model="tab"
                        class="custom-tab-btn"
                        selected-class="selected-class"
                        hide-slider
                        density="compact"
                        show-arrows
                    >
                        <v-tab v-for="tab in tabs" :value="tab">{{ tab }}</v-tab>
                    </v-tabs>
                </div>
                <div class="flex-0-0">
                    <v-btn
                        v-if="permissions?.refresh_teams"
                        @click="refreshTeamsData"
                        :loading="refresh.teamLoading"
                        :disabled="refresh.teamLoading"
                        v-tooltip:top="'Refreshing may take a few seconds. The page will reload once done.'"
                        class="me-2"
                        variant="text"
                        icon="mdi-refresh"
                        size="small"
                        color="info"
                        rounded
                    />
                    <a v-if="permissions?.setup_team_kpi" href="/reports/kpi-v2/teams/team-setup" target="_blank">
                        <v-btn style="text-transform: capitalize" variant="tonal" color="info" append-icon="mdi-open-in-new">
                            Setup KPI
                        </v-btn>
                    </a>
                </div>
            </div>
<!--          <div class="d-flex align-center justify-space-between">-->
<!--            <v-tabs-->
<!--                v-model="tab"-->
<!--                class="custom-tab-btn"-->
<!--                selected-class="selected-class"-->
<!--                hide-slider-->
<!--                density="compact"-->
<!--            >-->
<!--              <v-tab v-for="tab in tabs" :value="tab">{{ tab }}</v-tab>-->
<!--            </v-tabs>-->
<!--            <div>-->
<!--              <v-btn-->
<!--                  v-if="permissions?.refresh_teams"-->
<!--                  @click="refreshTeamsData"-->
<!--                  :loading="refresh.teamLoading"-->
<!--                  :disabled="refresh.teamLoading"-->
<!--                  v-tooltip:top="'Refreshing may take a few seconds. The page will reload once done.'"-->
<!--                  class="me-2"-->
<!--                  variant="text"-->
<!--                  icon="mdi-refresh"-->
<!--                  size="small"-->
<!--                  color="info"-->
<!--                  rounded-->
<!--              />-->
<!--              <a v-if="permissions?.setup_team_kpi" href="/reports/kpi-v2/teams/team-setup" target="_blank">-->
<!--                <v-btn style="text-transform: capitalize" variant="tonal" color="info" append-icon="mdi-open-in-new">-->
<!--                  Setup KPI-->
<!--                </v-btn>-->
<!--              </a>-->
<!--            </div>-->
<!--          </div>-->

          <v-divider class="mt-3"/>

          <GroupInsights
              :tab="tab"
              :date="date"
              :overview="overview.data"
              :summary="summary.data"
              :members="members.data"
              @refresh-data="() => {
                getOverviewData();
                getTeamSummaryData();
              }"
          />

          <!--          <v-tabs-window v-model="tab" class="mt-2">

                      <v-tabs-window-item value="Overall">
                        <Overall
                            :overview="overview.data"
                            :summary="summary.data"
                            :members="members.data"
                        />
                      </v-tabs-window-item>

                      <v-tabs-window-item value="Sales Target">
                        <SalesTarget
                            :overview="overview.data?.sales_value"
                            :summary="summary.data"
                            :members="members.data"
                        />
                      </v-tabs-window-item>

                      <v-tabs-window-item value="Live Listings">
                        <LiveListings
                            :overview="overview.data?.live_listings"
                            :summary="summary.data"
                            :members="members.data"
                        />
                      </v-tabs-window-item>

                      <v-tabs-window-item value="New Listings">
                        <NewListings
                            :overview="overview.data?.new_listings"
                            :summary="summary.data"
                            :members="members.data"
                        />
                      </v-tabs-window-item>

                      <v-tabs-window-item value="Active TruBroker">
                        <ActiveTruBroker
                            :overview="overview.data?.tru_broker"
                            :summary="summary.data"
                            :members="members.data"
                        />
                      </v-tabs-window-item>

                      <v-tabs-window-item value="Active Super Agent">
                        <ActiveSuperAgent
                            :overview="overview.data?.super_agent"
                            :summary="summary.data"
                            :members="members.data"
                        />
                      </v-tabs-window-item>

                    </v-tabs-window>-->
        </v-card-text>
      </v-card>

      <v-card flat class="mt-5">
        <v-card-item>
          <div class="d-flex align-center justify-space-between">
            <div>
              <v-card-title>Agent Insight</v-card-title>
              <v-card-subtitle>Overview of Agent Performance</v-card-subtitle>
            </div>
            <div>
              <v-btn
                  v-if="permissions?.refresh_agents"
                  @click="refreshAgentsData"
                  :loading="refresh.agentLoading"
                  :disabled="refresh.agentLoading"
                  v-tooltip:top="'Refreshing may take a few seconds. The page will reload once done.'"
                  class="me-2"
                  variant="text"
                  icon="mdi-refresh"
                  size="small"
                  color="info"
                  rounded
              />
              <a v-if="permissions?.setup_agent_kpi" href="/reports/kpi-v2/setup" target="_blank">
                <v-btn style="text-transform: capitalize" variant="tonal" color="info" append-icon="mdi-open-in-new">
                  Setup KPI
                </v-btn>
              </a>
            </div>
          </div>
        </v-card-item>

        <v-card-text>
          <AgentInsights
              :data="filteredMembers"
              :negativeData="negativeMembers"
              :tab="tab"
              :date="date"
              @refresh-members-data="getTeamMemberData"
          />

        </v-card-text>
      </v-card>
    </v-container>
  </v-app>
</template>

<style>

.v-application {
  font-family: "Nunito", sans-serif !important;
  border-radius: 10px !important;
  background: transparent !important;
}

.custom-tab-btn .v-btn__content {
  color: #6B7280;
  font-weight: 500;
  text-transform: capitalize;
  letter-spacing: normal;
}

.selected-class {
  background: #EFF6FF;
  border-radius: 5px !important;
  text-transform: capitalize;
  letter-spacing: normal;
}

.selected-class .v-btn__content {
  color: #3B82F6;
}

.v-expansion-panel-title--active {
  background: #EFF6FF;
}

.custom-card-overflow .v-card-item__content {
  overflow: visible !important;
}

.v-expansion-panel--active:not(:first-child), .v-expansion-panel--active + .v-expansion-panel {
  margin-top: 0;
}
</style>
