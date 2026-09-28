<script setup>
import {onMounted, ref, watch, computed} from "vue";
import {debounce} from "lodash";
onMounted(() => {
  const tg = window.Telegram?.WebApp;
  if (tg) {
    tg.ready();
    tg.expand(); // Allow full-height rendering
  }
});
const locations = ref({
    data: [],
    selected: [],
    loading: false,
    search: '',
    per_page: 10,
    page: 1,
});

const beds = ref({
    data: [],
    loading: false,
});
const selectedResults = ref([]);

const baths = ref({
    data: [],
    loading: false,
});

const categories = ref({
    data: [],
    loading: false,
});

const formData = ref({
    for: 'buy',
    locations: [],
    completion_status: 'all',
    category_ids: [],
    price_range: [0, 10000000],
    min_price: 0,
    max_price: 10000000,
    bed_ids: [],
    bath_ids: [],
    area_range: [0, 500000],
    furnished: ['all'],
    search_keywords: '',
    note: '',
    buyer_type: null,
    buyer_timeline: null,
    group_topic: null,
});

const groupTopics = ref([
    {
        title: 'Plot / Building / Commercial',
        value: 32231
    },
    {
        title: 'Meydan, D1, Sobha, etc',
        value: 26818
    },
    {
        title: 'Jumeirah Village Circle (JVC)',
        value: 26758
    },
    {
        title: 'Mina Rashid',
        value: 93024
    },
    {
        title: 'Downtown',
        value: 26810
    },
    {
        title: 'Business Bay',
        value: 26808
    },
    {
        title: 'DIFC',
        value: 110199
    },
    {
        title: 'Al Barari',
        value: 26782
    },
    {
        title: 'Damac Hills 1',
        value: 26802
    },
    {
        title: 'Madinat Jumeirah Living (MJL)',
        value: 26816
    },
    {
        title: 'Emaar Beachfront',
        value: 26820
    },
    {
        title: 'Dubai Marina',
        value: 26822
    },
    {
        title: 'Not related to specific area!',
        value: 26828
    },
    {
        title: 'Creek Harbour',
        value: 26806
    },
    {
        title: 'Dubai Hills Estate',
        value: 26774
    },     
]);

const toggleResultSelection = (id) => {
    const index = selectedResults.value.indexOf(id);
  if (index !== -1) {
    selectedResults.value.splice(index, 1);
  } else {
    selectedResults.value.push(id);
  }
};

  
const isAllSelected = computed(() =>
    results.value?.data?.length > 0 &&
    selectedResults.value.length === results.value.data.length
  )
  
const toggleSelectAll = () => {
    if (isAllSelected.value) {
      selectedResults.value = []
    } else {
      selectedResults.value = results.value.data.map(item => item.id)
    }
}
  
const toggleBed = (id) => {
    const index = formData.value.bed_ids.indexOf(id);
    if (index > -1) {
        formData.value.bed_ids.splice(index, 1);
    } else {
        formData.value.bed_ids.push(id);
    }
};

const toggleBath = (id) => {
    const index = formData.value.bath_ids.indexOf(id);
    if (index > -1) {
        formData.value.bath_ids.splice(index, 1);
    } else {
        formData.value.bath_ids.push(id);
    }
};

const toggleCategory = (id) => {
    const index = formData.value.category_ids.indexOf(id);
    if (index > -1) {
        formData.value.category_ids.splice(index, 1); // remove
    } else {
        formData.value.category_ids.push(id); // add
    }
};


const getLocations = async () => {
    locations.value.loading = true;
    const params = {
        keyword: locations.value.search,
        per_page: locations.value.per_page,
        page: locations.value.page,
    };

    try {
        const {data} = await axios.get(`/api/get-locations`, {params});
        locations.value.data = data.data;
    } catch (error) {
        console.error("Error fetching locations:", error);
    } finally {
        locations.value.loading = false;
    }
};

const getBeds = async () => {
    beds.value.loading = true;
    try {
        const {data} = await axios.get("/api/get-beds");
        beds.value.data = data.data;
    } catch (error) {
        console.error(error);
    } finally {
        beds.value.loading = false;
    }
};

const getBaths = async () => {
    baths.value.loading = true;
    try {
        const {data} = await axios.get("/api/get-baths");
        baths.value.data = data.data;
    } catch (error) {
        console.error(error);
    } finally {
        baths.value.loading = false;
    }
};

const getCategories = async () => {
    categories.value.loading = true;
    try {
        const {data} = await axios.get("/api/get-types");
        categories.value.data = data.data;
    } catch (error) {
        console.error(error);
    } finally {
        categories.value.loading = false;
    }
};

const debounceFetch = debounce(() => {
    if (locations.value.search) {
        getLocations();
    }
}, 500);

const updateFor = (value) => {
    formData.value.for = value;
};

const updateCompletionStatus = (value) => {
    formData.value.completion_status = value;
};

const updateFurnishingStatus = (status) => {
    if (status === 'all') {
        formData.value.furnished = ['all'];
    } else {
        const index = formData.value.furnished.indexOf(status);
        if (index > -1) {
            formData.value.furnished.splice(index, 1);
        } else {
            formData.value.furnished = formData.value.furnished.filter(s => s !== 'all');
            formData.value.furnished.push(status);
        }

        // If no other options selected, revert back to 'all'
        if (formData.value.furnished.length === 0) {
            formData.value.furnished = ['all'];
        }
    }
};


const resetFormData = () => {
    formData.value = {
        for: 'buy',
        locations: [],
        completion_status: 'all',
        category_ids: [],
        price_range: [0, 10000000],
        min_price: 0,
        max_price: 10000000,
        bed_ids: [],
        bath_ids: [],
        area_range: [0, 500000],
        furnished: 'all',
        search_keywords: '',
        note: '',
        buyer_type: null,
        buyer_timeline: null,
    };
};

onMounted(() => {
    getBeds();
    getBaths();
    getCategories();

    const urlParams = new URLSearchParams(window.location.search);
    const chat_id = urlParams.get('telegram_chat_id');
    if (chat_id) {
        telegramChatId.value = chat_id;
    }
});

const results = ref({
    loading: false,
    data: [],
})
const onSearch = async () => {

    results.value.loading = true

    let payload = {
        ...formData.value,
        min_price: formData.value.price_range[0],
        max_price: formData.value.price_range[1],
        min_size: formData.value.area_range[0],
        max_size: formData.value.area_range[1],
    }

    try {
        const {data} = await axios.post('/api/matching-listing', payload)
        if (data.data.length) {
            window.scrollTo({top: 0, behavior: 'smooth'}); // scroll to top on search
            results.value.data = data.data
        }
        if (data.data.length === 0) {
            showNotification('No results found for your search criteria.', 'info', 'mdi-information')
        }
    } catch (error) {
        console.error(error)
    } finally {
        results.value.loading = false
    }
}
watch(
  () => results.value.data,
  (newResults) => {
    if (Array.isArray(newResults) && newResults.length) {
      const newIds = newResults.map(r => r.id)
      selectedResults.value = [...new Set([...selectedResults.value, ...newIds])]
    }
  },
  { immediate: true }
)

const telegramChatId = ref(null)

const hidePostButton = ref(false)

const loadingPostButton = ref(false)
const onPosting = async () => {

    loadingPostButton.value = true

    let payload = {
        telegram_chat_id: telegramChatId.value,
        ...formData.value,
        min_price: formData.value.price_range[0],
        max_price: formData.value.price_range[1],
        min_size: formData.value.area_range[0],
        max_size: formData.value.area_range[1],
    }

    try {
        const response = await axios.post('/api/send-listing-req-into-telegram', payload)
        if (response) {
            hidePostButton.value = true
            showNotification(response?.data?.message || 'Your requirement posted in the group successfully.')
        }
    } catch (error) {
        console.error(error)
        showNotification(error.response?.data?.message || 'An error occurred while posting your requirements.', 'error', 'mdi-alert-circle')
    } finally {
        loadingPostButton.value = false
    }
}

const hideSendEmailButton = ref(false)
const hideSendTelegramButton = ref(false)

const loadingSendEmailButton = ref(false)
const loadingSendTelegramButton = ref(false)

const onSendViaEmailAndTelegram = async (via) => {

    if(via === 'email') {
        loadingSendEmailButton.value = true
    } else if(via === 'telegram') {
        loadingSendTelegramButton.value = true
    }

    let payload = {
        telegram_chat_id: telegramChatId.value,
        ids: JSON.parse(JSON.stringify(selectedResults.value)),
        //listings: results.value.data,
        via: via
    } 
    try {
        const response = await axios.post('/api/send-listings-to-user', payload)
        if (response) {
            if(via === 'email') {
                hideSendEmailButton.value = true
            } else if(via === 'telegram') {
                hideSendTelegramButton.value = true
            }
            showNotification(response?.data?.message || 'Listing results sent to your email and telegram successfully.')
        }
    } catch (error) {
        console.error(error)
        showNotification(error.response?.data?.message || 'An error occurred while sending your listings.', 'error', 'mdi-alert-circle')

    } finally {
        if(via === 'email') {
            loadingSendEmailButton.value = false
        } else if(via === 'telegram') {
            loadingSendTelegramButton.value = false
        }
    }
}

const callToAgent = (mobile) => {
    window.open(`tel:${mobile}`, '_blank');
}

const sendTelegramMessage = (username) => {
    window.open(`https://t.me/${username}`, '_blank');
}

const formatCurrency = (value, decimal = 2) => {
    if (value == null || isNaN(value)) {
        return "-" // Fallback value if input is null or not a number
    }

    // Ensure the number is parsed and fixed to 2 decimal places
    const number = parseFloat(value)

    // Add comma formatting for thousands
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
}

const getHumanReadable = (value) => {
    return Number(value) > 1000000000 ? (Number(value) / 1000000000) + 'B' : Number(value) > 1000000 ? (Number(value) / 1000000) + 'M' : Number(value) > 1000 ? (Number(value) / 1000) + 'K' : value
};

const getFormattedFor = (value) => {
    if (value === 'Residential for sale' || value === 'Commercial for sale') {
        return 'Sale'
    } else if (value === 'Residential for rent' || value === 'Commercial for rent') {
        return 'Rent'
    }
}

const notification = ref({
    show: false,
    color: 'success',
    icon: 'mdi-check',
    message: '',
})

const showNotification = (message, color = 'success', icon = 'mdi-check') => {
    notification.value = {
        show: true,
        color,
        icon,
        message,
    }
}

const userData = ref(null)

onMounted(() => {
    const tele = window?.Telegram?.WebApp

    if (tele) {
        tele.ready?.()
        console.log('initDataUnsafe', tele?.initDataUnsafe)
        userData.value = tele?.initDataUnsafe?.user
        console.log('Telegram user:', userData.value)
    } else {
        console.warn('Telegram WebApp not available')
    }
})

</script>

<template>
    <v-app>
        
        <!--Notification Alert-->
        <v-snackbar
            v-model="notification.show"
            :color="notification.color"
            variant="tonal"
            location="top right"
        >
            <v-icon :icon="notification.icon" class="mr-2"/>
            <span class="text-body-2 font-weight-medium">{{ notification.message }}</span>
        </v-snackbar>

        <v-container max-width="650" class="pb-16">
            <v-card v-if="results.data.length" variant="flat">
                <v-card-title class="d-flex align-center justify-content-between">
                    <h4 class="ma-0">Search Results</h4>
                    <v-icon icon="mdi-magnify" @click="results.data = []" size="20"/>
                </v-card-title>
                <v-divider class="mt-0"/>
                    <!-- Select All & Count -->
                    <div class="d-flex justify-space-between align-center my-2" v-if="results.data.length">
                    <!-- Selected Count -->
                    <span class="text-caption text-success font-weight-medium">
                        {{ selectedResults.length }} Selected
                    </span>

                    <!-- Select All Button -->
                    <v-btn
                        size="small"
                        variant="outlined"
                        color="primary"
                        @click="toggleSelectAll"
                    >
                        {{ isAllSelected ? 'Deselect All' : 'Select All' }}
                    </v-btn>
                    </div>
                <v-card border v-for="result in results.data" :key="result.id" class="mb-5 result-card" @click="toggleResultSelection(result.id)" :class="{ 'selected-result': selectedResults.includes(result.id) }">
                    <v-card-text class="d-flex flex-column gap-2" style="position: relative">
                        <v-chip
                            v-if="result?.for"
                            rounded size="small"
                            variant="flat"
                            :color="getFormattedFor(result?.for) === 'Sale' ? '#ff6467' : '#51a2ff'"
                            style="position:absolute; top: 25px; left: 25px; z-index: 1">
                            {{ getFormattedFor(result?.for) }}
                        </v-chip>
                        <v-chip v-if="result?.publish_status" rounded size="small" variant="flat" color="success"
                                style="position:absolute; top: 25px; right: 25px; z-index: 1">
                            {{ result?.publish_status }}
                        </v-chip>
                        <v-img
                            rounded
                            min-height="250"
                            max-height="250"
                            min-width="100%"
                            :lazy-src="result?.first_image?.thumbnail"
                            :src="result?.first_image?.thumbnail"
                            cover
                        />
                        <h4 class="ma-0">AED {{ formatCurrency(result?.price) }}</h4>
                        <div class="d-flex align-center gap-4">
                            <div class="d-flex align-center gap-1">
                                <v-icon size="18" icon="mdi-bed-outline"/>
                                <span>{{ result?.bed }}</span>
                            </div>
                            <div class="d-flex align-center gap-1">
                                <v-icon size="18" icon="mdi-shower"/>
                                <span>{{ result?.bath }}</span>
                            </div>
                            <div class="d-flex align-center gap-1">
                                <v-icon size="18" icon="mdi-grid-large"/>
                                <span>{{ result?.built_up_area }} sqft</span>
                            </div>
                        </div>
                        <p class="ma-0 text-truncate">{{ result?.title }}</p>
                        <p class="ma-0 text-truncate">{{ result?.location }}</p>
                        <v-divider class="my-1"/>
                        <div v-if="result.agent" class="d-flex align-center justify-content-between">
                            <div class="d-flex align-center gap-2">
                                <v-avatar size="48" :image="result?.agent?.photo"/>
                                <div>
                                    <h5 class="ma-0">{{ result?.agent?.name }}</h5>
                                    <p class="ma-0">{{ result?.agent?.email }}</p>
                                </div>
                            </div>
                            <div class="d-flex align-center gap-2">
                                <v-btn
                                    v-if="result?.agent?.mobile"
                                    @click="callToAgent(result?.agent?.mobile)"
                                    v-tooltip:top="'Call'"
                                    size="x-small"
                                    icon="mdi-phone-outline"
                                    rounded
                                    variant="tonal"
                                    color="success"
                                />
                                <v-btn
                                    v-if="result?.agent?.telegram_user_name"
                                    @click="sendTelegramMessage(result?.agent?.telegram_user_name)"
                                    v-tooltip:top="'Telegram'"
                                    size="x-small"
                                    icon
                                    rounded
                                    variant="tonal"
                                    color="success"
                                >
                                    <v-icon icon="mdi-send-outline" class="rotate-icon mb-1 ml-1"/>
                                </v-btn>
                            </div>
                        </div>
                    </v-card-text>
                </v-card>
                <v-divider/>
                <div>
                    <div class="d-flex justify-space-between align-center mb-2">
                        <span class="text-caption text-success font-weight-medium">
                        {{ selectedResults.length }} Selected
                    </span>
                    </div>
                    <v-row dense v-if="selectedResults.length">
                        <v-col v-if="!hideSendEmailButton" cols="12">
                            <v-btn
                                @click="onSendViaEmailAndTelegram('email')"
                                :loading="loadingSendEmailButton"
                                color="success" variant="tonal" block
                            >
                                Send Results Via Email
                            </v-btn>
                        </v-col>
                        <v-col v-if="!hideSendTelegramButton" cols="12">
                            <v-btn
                                @click="onSendViaEmailAndTelegram('telegram')"
                                :loading="loadingSendTelegramButton"
                                color="success" variant="tonal" block
                            >
                                Send Results Via Telegram
                            </v-btn>
                        </v-col>
                    </v-row>
                    <p class="text-caption text-center" v-else>No listings selected to send.</p>

                    <template v-if="!hidePostButton">
                        <p class="ma-0 my-2 text-center">Can't find what you're looking for?</p>

                        <v-select
                            v-if="formData.for === 'buy'"
                            v-model="formData.buyer_type"
                            :items="['Cash', 'Pre Approved Mortgage', 'Mortgage']"
                            :label="`Select ${formData.for === 'buy' ? 'buyer' : 'tenant'} type`"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                        />

                        <v-select
                            class="mt-2"
                            v-model="formData.buyer_timeline"
                            :items="[
                                'ASAP / Urgent',
                                'Within This Week',
                                'Next Week',
                                'Within 2 Weeks',
                                'Within 1 Month',
                                'Within 2 Months',
                                'Within 3 Months',
                                'Within 6 Months',
                                'In 6–12 Months',
                                'No Specific Timeline / Just Exploring'
                            ]"
                            :label="`Select ${formData.for === 'buy' ? 'buyer' : 'tenant'} timeline`"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                        />
                        <v-select
                            class="mt-2"
                            v-model="formData.group_topic"
                            :items="groupTopics"
                            item-value="value"
                            item-title="title"
                            label="Select Group Topic"
                            variant="outlined"
                            density="compact"
                            hide-details
                            clearable
                        />

                        <v-textarea
                            class="mt-2"
                            v-model="formData.note"
                            label="Enter Additional Notes"
                            rows="2"
                            variant="outlined"
                            hide-details
                        />

                        <v-btn
                            style="text-transform: capitalize;margin: 0;letter-spacing: normal"
                            variant="tonal"
                            color="info"
                            @click="onPosting"
                            :loading="loadingPostButton"
                            class="mt-2"
                        >
                            Post it in the group!
                        </v-btn>
                    </template>
                </div>
            </v-card>

            <div v-else>
                <h3 class="text-center">Find Matching Listing</h3>

                <div class="d-flex gap-2 mt-4">
                    <v-btn
                        width="49.5%"
                        color="success"
                        :variant="formData.for === 'buy' ? 'tonal' : 'text'"
                        :border="formData.for === 'rent'"
                        @click="updateFor('buy')"
                    >
                        Buy
                    </v-btn>

                    <v-btn
                        width="49.5%"
                        color="success"
                        :variant="formData.for === 'rent' ? 'tonal' : 'text'"
                        :border="formData.for === 'buy'"
                        @click="updateFor('rent')"
                    >
                        Rent
                    </v-btn>
                </div>

                <v-label class="mt-4">Location</v-label>

                <VAutocomplete
                    v-model="formData.locations"
                    v-model:search="locations.search"
                    @update:search="debounceFetch()"
                    :items="locations.data"
                    :loading="locations.loading"
                    item-value="id"
                    item-title="name"
                    variant="outlined"
                    density="compact"
                    multiple
                    chips
                    closable-chips
                    clear-on-select
                    return-object
                    placeholder="Search for location"
                >
                    <template #item="{ props, item }">
                        <v-list-item v-bind="props" title="">
                            <v-list-item-title>{{ item?.raw?.name }}</v-list-item-title>
                            <v-list-item-subtitle>{{ item?.raw?.path }}</v-list-item-subtitle>
                        </v-list-item>
                    </template>
                </VAutocomplete>

                <v-divider/>

                <v-label>Completion Status</v-label>

                <div class="d-flex gap-2">
                    <v-btn
                        v-for="status in ['all', 'ready', 'off-plan']"
                        :key="status"
                        rounded
                        color="success"
                        :variant="formData.completion_status === status ? 'tonal' : 'text'"
                        :border="formData.completion_status !== status"
                        @click="updateCompletionStatus(status)"
                        style="text-transform: capitalize;"
                    >
                        {{ status }}
                    </v-btn>
                </div>

                <v-divider/>

                <v-label>Property Type</v-label>

                <v-card
                    max-width="92vw"
                    variant="flat"
                    class="d-flex gap-2 overflow-x-scroll mt-4"
                >
                    <v-card
                        class="d-flex align-center justify-center w-100 text-center cursor-pointer"
                        v-for="category in categories.data"
                        :key="category.id"
                        @click="toggleCategory(category.id)"
                        color="success"
                        :variant="formData.category_ids.includes(category.id) ? 'tonal' : 'text'"
                        border
                        min-height="120"
                        min-width="120"
                    >
                        {{ category.name }}
                    </v-card>
                </v-card>

                <v-divider/>

                <v-label>Price Range (AED)</v-label>

                <v-range-slider
                    v-model="formData.price_range"
                    strict
                    :min="0"
                    :max="10000000"
                    :step="1000"
                    thumb-label
                >
                    <template #thumb-label="{ modelValue }">
                        <span class="text-body-2">{{ formatCurrency(modelValue) }}</span>
                    </template>
                </v-range-slider>

                <div class="d-flex align-center gap-4">
                    <v-text-field
                        v-model.number="formData.price_range[0]"
                        :placeholder="formatCurrency(0)"
                        variant="outlined"
                        density="compact"
                        messages="Minimum"
                    >
                        <template #append-inner>
                            {{ getHumanReadable(formData.price_range[0]) }}
                        </template>
                    </v-text-field>
                    <span class="text-body-2 mb-5">to</span>
                    <v-text-field
                        v-model.number="formData.price_range[1]"
                        :placeholder="formatCurrency(10000000)"
                        variant="outlined"
                        density="compact"
                        messages="Maximum"
                    >
                        <template #append-inner>
                            {{ getHumanReadable(formData.price_range[1]) }}
                        </template>
                    </v-text-field>

                </div>

                <v-divider/>

                <v-label>Bedrooms</v-label>

                <v-card max-width="92vw" variant="flat" class="d-flex align-center gap-2 overflow-x-scroll">
                    <v-btn
                        v-for="bed in beds.data"
                        :key="bed.id"
                        @click="toggleBed(bed.id)"
                        :variant="formData.bed_ids.includes(bed.id) ? 'tonal' : 'text'"
                        rounded
                        border
                        size="small"
                        color="success"
                        style="text-transform: capitalize;letter-spacing: normal;"
                    >
                        {{ bed.name }}
                    </v-btn>
                </v-card>

                <v-divider/>

                <v-label>Baths</v-label>

                <v-card max-width="92vw" variant="flat" class="d-flex align-center gap-2 overflow-x-scroll">
                    <v-btn
                        v-for="bath in baths.data"
                        :key="bath.id"
                        @click="toggleBath(bath.id)"
                        :variant="formData.bath_ids.includes(bath.id) ? 'tonal' : 'text'"
                        rounded
                        border
                        size="small"
                        color="success"
                        style="text-transform: capitalize;letter-spacing: normal;"
                    >
                        {{ bath.name }}
                    </v-btn>
                </v-card>

                <v-divider/>

                <v-label>Area Range (Square Feet)</v-label>

                <v-range-slider
                    v-model="formData.area_range"
                    thumb-label
                    strict
                    :min="0"
                    :max="50000"
                    :step="100"
                >
                    <template #thumb-label="{ modelValue }">
                        <span class="text-body-2">{{ formatCurrency(modelValue) }}</span>
                    </template>
                </v-range-slider>

                <div class="d-flex align-center gap-4">
                    <v-text-field
                        v-model.number="formData.area_range[0]"
                        placeholder="0"
                        variant="outlined"
                        density="compact"
                        hide-details
                        suffix="sqft"
                    />
                    <span class="text-body-2">to</span>
                    <v-text-field
                        v-model.number="formData.area_range[1]"
                        placeholder="Any"
                        variant="outlined"
                        density="compact"
                        hide-details
                        suffix="sqft"
                    />
                </div>

                <v-divider/>

                <v-label>Furnishing Status</v-label>

                <div class="d-flex flex-wrap gap-2">
                    <v-btn
                        v-for="status in ['all', 'furnished', 'unfurnished', 'semi-furnished']"
                        :key="status"
                        rounded
                        color="success"
                        :variant="formData.furnished.includes(status) ? 'tonal' : 'text'"
                        :border="!formData.furnished.includes(status)"
                        @click="updateFurnishingStatus(status)"
                        style="text-transform: capitalize;"
                    >
                        {{ status }}
                    </v-btn>
                </div>

                <v-divider/>

                <v-label>Keywords</v-label>

                <v-text-field
                    v-model="formData.search_keywords"
                    placeholder="Enter relevant keywords"
                    variant="outlined"
                    density="compact"
                    hide-details
                />

                <v-divider/>

                <div class="d-flex align-center gap-2">
                    <v-btn @click="resetFormData" width="19.5%" variant="tonal" color="error">Reset</v-btn>
                    <v-btn @click="onSearch" :loading="results.loading" width="79.5%" variant="flat" color="success">
                        Search
                    </v-btn>
                </div>
            </div>

        </v-container>
    </v-app>
</template>

<style scoped>
html,
body,
#app,
.v-application {
  height: 100%;
  width: 100%;
  margin: 0;
  padding: 0;
  overflow: hidden;
}

.v-application {
    font-family: "Nunito", sans-serif !important;
    border-radius: 10px !important;
    background: transparent !important;
}

.v-text-field :deep(.v-input__details) {
    padding: 0;
}

.rotate-icon {
    transform: rotate(-45deg); /* adjust the angle as needed */
}

.selectable-card {
  cursor: pointer;
  transition: box-shadow 0.3s, border 0.3s;
}

.selectable-card.selected {
  border: 2px solid #4caf50 !important; /* Vuetify success color */
  box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
}
.result-card {
  cursor: pointer;
  transition: border 0.2s, background-color 0.2s;
}

::v-deep(.result-card.selected-result) {
  border: 2px solid #4caf50 !important;
  background-color: #f0fff4;
}
</style>
