import { ref } from 'vue'
import axios from 'axios'
export default function useCommissionType() {
    const commissionType = ref([])
    const commissionTypes = ref([])

    const errors = ref('')

    const getCommissionTypes = async () => {
        let response = await axios.get('/api/vue/v1/commission-types')
        commissionTypes.value = response.data.data
    }

    const getCommissionType = async (id) => {
        let response = await axios.get(`/api/vue/v1/commission-types¸/${id}`)
        commissionTypes.value = response.data.data
    }

    // const storeCustomer = async (data) => {
    //     errors.value = ''
    //     try {
    //         await axios.post('/api/companies', data)

    //     } catch (e) {
    //         if (e.response.status === 422) {
    //             for (const key in e.response.data.errors) {
    //                 errors.value = e.response.data.errors
    //             }
    //         }
    //     }

    // }

    // const updateCustomer = async (id) => {
    //     errors.value = ''
    //     try {
    //         await axios.patch(`/api/companies/${id}`, company.value)

    //     } catch (e) {
    //         if (e.response.status === 422) {
    //             for (const key in e.response.data.errors) {
    //                 errors.value = e.response.data.errors
    //             }
    //         }
    //     }
    // }

    return {
        errors,
        commissionType,
        commissionTypes,
        getCommissionType,
        getCommissionTypes,
    }
}
