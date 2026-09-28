// import { ref } from 'vue'
// import axios from 'axios'
// export default function usePipelineHelper() {
//     const params = ref({
//         tab: "overall",
//         date: date.value,
//         start_date: date.value[0],
//         end_date: date.value[1],
//         group: department.value,
//         inactiveUsers: is_inactive_agents.value,
//         archivedLeads: is_archived_leads.value,
//         exceptListingDateFilter: is_date_exempt.value,
//     })
//     const overview = ref([])

//         const getOverview = async () => {
//         let response = await axios.get('/api/vue/v1/commission-types')
//         overview.value = response.data.data
//     }

//     return {
//         params,
//         overview,
//         getOverview,
//     }
// }
