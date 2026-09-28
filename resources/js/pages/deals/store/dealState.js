import { ref } from 'vue'
import { defineStore } from 'pinia'

export const useDealStore = defineStore('deal_state', () => {
    const data = ref({})
    const deal_type = ref('Rental')
    const agents = ref([])
    const currentModal = ref(null);
    const leadResources = ref({})
    const formStep = ref(2);
    const notes = ref([]);
    // function increment() {
    //     count.value++
    // }

    return { data, agents,
        currentModal,leadResources, formStep, notes, deal_type
       // increment
    }
})


