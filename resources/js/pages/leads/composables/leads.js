import { ref } from 'vue'
import axios from 'axios'

export default function useLeads() {
    const errors = ref('')
    const lead = ref({})
    const leadSource = ref([])
    const leadMediums = ref([])


    const getLead = async (id) => {
        try {
            let response = await axios.get(route('admin.work.leads.get') + `?id=${id}`)
            lead.value = response.data.data
        }catch (error){
            throw error;
        }
    }
    //owner related function
    const getLeadSource = async (id) => {
        let response = await axios.get(route('vue.api.lead.source.list'))
        leadSource.value = response.data.data
    }
    const getLeadMediums = async (id) => {
        let response = await axios.get(route('vue.api.lead.medium.list'))
        leadMediums.value = response.data.data
    }
    const storeLeadUbo = async (data) => {
        errors.value = ''
        try {
            const config = {
                headers: {
                    'Content-type': 'multipart/form-data'
                }
            }
            let response = await axios.post('/api/vue/v1/store-lead-ubo-store', data).then(function(response){

                if(response.data.status){

                    showToastify('UBO profile saved successfully', 'success');
                }else{
                    showToastify('Something went wrong', 'error')
                }
                return response;
            }).catch(function(){
                return response;
                //console.log('FAILURE!!');
            });

            return response;


        } catch (e) {
            if (e.response.status === 422) {
                for (const key in e.response.data.errors) {
                    errors.value = e.response.data.errors
                }
            }
            return e;
        }

    }
    const getUboList = async (id) => {
        let response = await axios.get(route('vue.api.lead.company.ubo.lis', id))
        return response.data.data
    }

    return {
        errors,
        leadSource,
        getLeadSource,
        storeLeadUbo,
        leadMediums,
        getLeadMediums,
        getLead,
        lead,
    }
}
