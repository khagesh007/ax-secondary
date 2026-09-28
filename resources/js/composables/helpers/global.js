import {ref, reactive} from 'vue'
import axios from 'axios'

export default function useGlobalHelper() {
    const countries = ref([])
    const users = ref([])
    const properties = ref([])
    const formatDate = (date, format = '') => {
        if (format != '') {
            let d = moment(date).format(format);
            return d === 'Invalid date' ? '' : d;
        }
        let d = moment(date).format('DD/MM/YYYY');
        return d === 'Invalid date' ? '' : d;
    }
    const timeAgo = (date) => {
        let d = moment(date).fromNow();
        return d === 'Invalid date' ? '' : d;
    }

    const activityChangeDataFormat = (data) => {
        if (data == null) return;
        let html = '';
        let val = '';
        for (const [key, value] of Object.entries(data)) {
            if (key == 'created_at' || key == 'updated_at') {
                html += key + ' - ' + moment(value).format("DD/MM/YY HH:mm") + '<br>';
            } else {
                val = value == null ? '' : value;
                html += key + ' - ' + val + '<br>';
            }
        }
        return html;
    }
    const humanReadableString = (string) => {
        string = string.replace(/_/g, " ");
        const firstLetter = string[0].toUpperCase();
        return `${firstLetter}${string.slice(1)}`;
    }
    const getCountries = async () => {
        let response = await axios.get(`/api/vue/v1/get-countries`)
        countries.value = response.data.data
    }
    const getUsers = async (term = '',roles = []) => {
        let data = {
            term: term,
            roles: roles
        }
        let response = await axios.post(route('ajax.getUsersSelect2'),data)
        users.value = response.data.data
    }
    const getAgents = async (term = '') => {
        let response = await axios.get(route('ajax.getAgentsSelect2')+"?term="+term)
        users.value = response.data.data
    }
    const getProperties  = async (term = '') => {
        let response = await axios.get(route('ajax.properties.select2')+"?term="+term)
        properties.value = response.data.data
    }
    const getUserProperties  = async (term = '',user = '') => {
        let data = {
            term: term,
            agent_id: user
        }
        let response = await axios.post('/ajax/deal-properties-select2',data)
        properties.value = response.data.data

    }


    return {
        formatDate,
        timeAgo,
        activityChangeDataFormat,
        humanReadableString,
        countries,
        getCountries,
        users,
        getUsers,
        getAgents,
        properties,
        getProperties,
        getUserProperties

    }
}
