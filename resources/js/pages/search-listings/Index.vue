<script setup>
import {onMounted, ref, watch} from "vue";
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
    selected: null,
    loading: false,
    search: '',
    per_page: 10,
    page: 1,
});

const beds = ref({
    data: [],
    loading: false,
});

const baths = ref({
    data: [],
    loading: false,
});

const categories = ref({
    data: [],
    loading: false,
});

const views = ref({
    data: [],
    loading: false,
})

const amenities = ref({
    data: [],
    loading: false,
})

const formData = ref({
    for: 'Residential for sale',
    property_category_id: null,
    bed_id: null,
    bath_id: null,
    location: null,
    built_up_area: null,
    plot_size: null,
    parking: null,
    view: [],
    title: null,
    price: null,
    description: null,
    furnished: null,
    features: [],
    group_topic: null,

});

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

const getViews = async () => {
    views.value.loading = true;
    try {
        const {data} = await axios.get("/api/get-views");
        views.value.data = data.data;
    } catch (error) {
        console.error(error);
    } finally {
        views.value.loading = false;
    }
};

const getAmenities = async () => {
    amenities.value.loading = true;
    try {
        const {data} = await axios.get("/api/get-amenities");
        amenities.value.data = data.data;
    } catch (error) {
        console.error(error);
    } finally {
        amenities.value.loading = false;
    }
};

const resetFormData = () => {
    formData.value = {
        for: 'Residential for sale',
        property_category_id: null,
        bed_id: null,
        bath_id: null,
        location: null,
        built_up_area: null,
        plot_size: null,
        parking: null,
        view: [],
        title: null,
        price: null,
        description: null,
        furnished: null,
        features: [],
    };
    listings.value.search = '';
    listings.value.selected = null;
    createForm.value?.resetValidation();
};

const selectedTab = ref('Search My Listings')
const tabs = ref(['Search My Listings', 'Create a New'])

const selectTab = (tab) => {
    selectedTab.value = tab
}

const listings = ref({
    data: [],
    selected: null,
    loading: false,
    submitting: false,
    search: '',
    per_page: 10,
    page: 1,
    chat_id: null,
});

const getListings = async () => {
    listings.value.loading = true;

    const params = {
        telegram_chat_id: listings.value.chat_id,
        search_keywords: listings.value.search,
    }
    console.log(params)
    try {
        const {data} = await axios.get(`/api/search-my-listing`, {params});
        listings.value.data = data.data;
    } catch (error) {
        console.error("Error fetching listings:", error);
    } finally {
        listings.value.loading = false;
    }
};

const debounceListings = debounce(() => {
    if (listings.value.search) {
        getListings();
    }
}, 500);

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

const debounceLocations = debounce(() => {
    if (locations.value.search) {
        getLocations();
    }
}, 500);

const onSendViaEmailAndTelegram = async () => {

    listings.value.submitting = true

    let payload = {
        telegram_chat_id: listings.value.chat_id,
        listings: [listings.value.selected],
        group_topic: formData.value.group_topic,
    }
    try {
        const response = await axios.post('/api/send-listings-to-telegram', payload)

        showNotification(response?.data?.message || 'Listing sent successfully')

    } catch (error) {
        console.error(error)
        showNotification(error.response?.data?.message || 'An error occurred while sending a listing.', 'error', 'mdi-alert-circle')

    } finally {
        listings.value.submitting = false
    }
}

const purposes = ref([
    'Residential for sale',
    'Residential for rent',
    'Commercial for sale',
    'Commercial for rent',
])

const furnishingStatuses = ref([
    {
        title: 'Furnished',
        value: 'FURNISHED'
    },
    {
        title: 'Semi-Furnished',
        value: 'SEMI_FURNISHED'
    },
    {
        title: 'Unfurnished',
        value: 'UNFURNISHED'
    },
])
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

const updateFor = (purpose) => {
    formData.value.for = purpose
}

const updateType = (id) => {
    formData.value.property_category_id = id
}

const updateBed = (id) => {
    formData.value.bed_id = id
}

const updateBath = (id) => {
    formData.value.bath_id = id
}

const updateFurnishingStatus = (value) => {
    formData.value.furnished = value
}

const toggleViews = (key) => {
    if (formData.value.view.includes(key)) {
        formData.value.view = formData.value.view.filter(view => view !== key)
    } else {
        formData.value.view.push(key)
    }
}

const toggleAmenity = (id) => {
    if (formData.value.features.includes(id)) {
        formData.value.features = formData.value.features.filter(amenity => amenity !== id)
    } else {
        formData.value.features.push(id)
    }
}

const createForm = ref(null)
const loading = ref(false)
const onCreate = async () => {

    // Trigger form validation and check if the form is valid
    const isFormValid = await createForm.value?.validate()

    if (!isFormValid.valid) {
        return // Stop the submission if the form is not valid
    }

    loading.value = true

    let payload = {
        ...formData.value,
        telegram_chat_id: listings.value.chat_id,
        location_id: formData.value.location?.id,
        location_type: formData.value.location?.type,
    }

    try {
        const {data} = await axios.post('/api/create-listing', payload)
        if (data) {
            showNotification(data?.message || 'Listing created successfully')
            selectedTab.value = 'Search My Listings'
            window.scrollTo({top: 0, behavior: 'smooth'}); // scroll to top on search
            resetFormData()
        }
    } catch (error) {
        showNotification(error.response?.data?.message || 'An error occurred while creating a listing.', 'error', 'mdi-alert-circle')
        console.error(error)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    const urlParams = new URLSearchParams(window.location.search);
    const chat_id = urlParams.get('telegram_chat_id');
    if (chat_id) {
        listings.value.chat_id = chat_id;
    }
})

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
onMounted(() => {
    getBeds();
    getBaths();
    getCategories();
    getViews();
    getAmenities();
});

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

            <h3 class="text-center">Post My Listing</h3>

            <div class="d-flex gap-2 my-4">
                <v-btn
                    v-for="tab in tabs"
                    color="success"
                    :variant="selectedTab === tab ? 'tonal' : 'text'"
                    :border="selectedTab !== tab"
                    @click="selectTab(tab)"
                    style="font-size: 12px; line-height: 1.2; padding: 4px 12px;"
                >
                    {{ tab }}
                </v-btn>
            </div>

            <v-card v-if="selectedTab === 'Search My Listings'" variant="flat" border>
                <v-card-text>
                    <VAutocomplete
                        v-model="listings.selected"
                        v-model:search="listings.search"
                        @update:search="debounceListings()"
                        :items="listings.data"
                        :loading="listings.loading"
                        item-value="id"
                        item-title="title"
                        variant="outlined"
                        density="compact"
                        clearable
                        clear-icon="mdi-close"
                        clear-on-select
                        return-object
                        placeholder="Search for listings"
                        prepend-inner-icon="mdi-magnify"
                    >
                        <template #item="{ props, item }">
                            <v-list-item v-bind="props" title="">
                                <v-list-item-title>{{ item?.raw?.title }}</v-list-item-title>
                                <v-list-item-subtitle>{{ item?.raw?.location }}</v-list-item-subtitle>
                            </v-list-item>
                        </template>
                    </VAutocomplete>

                    <v-card border v-if="listings.selected" :key="listings.selected.id" class="mb-5">
                        <v-card-text class="d-flex flex-column gap-2" style="position: relative">
                            <v-chip
                                v-if="listings.selected?.for"
                                rounded size="small"
                                variant="flat"
                                :color="getFormattedFor(listings.selected?.for) === 'Sale' ? '#ff6467' : '#51a2ff'"
                                style="position:absolute; top: 25px; left: 25px; z-index: 1">
                                {{ getFormattedFor(listings.selected?.for) }}
                            </v-chip>
                            <v-chip v-if="listings.selected?.publish_status" rounded size="small" variant="flat"
                                    color="success"
                                    style="position:absolute; top: 25px; right: 25px; z-index: 1">
                                {{ listings.selected?.publish_status }}
                            </v-chip>
                            <v-img
                                rounded
                                min-height="250"
                                max-height="250"
                                min-width="100%"
                                :lazy-src="listings.selected?.first_image?.thumbnail"
                                :src="listings.selected?.first_image?.thumbnail"
                                cover
                            />
                            <h4 class="ma-0">AED {{ formatCurrency(listings.selected?.price) }}</h4>
                            <div class="d-flex align-center gap-4">
                                <div class="d-flex align-center gap-1">
                                    <v-icon size="18" icon="mdi-bed-outline"/>
                                    <span>{{ listings.selected?.bed }}</span>
                                </div>
                                <div class="d-flex align-center gap-1">
                                    <v-icon size="18" icon="mdi-shower"/>
                                    <span>{{ listings.selected?.bath }}</span>
                                </div>
                                <div class="d-flex align-center gap-1">
                                    <v-icon size="18" icon="mdi-grid-large"/>
                                    <span>{{ listings.selected?.built_up_area }} sqft</span>
                                </div>
                            </div>
                            <p class="ma-0 text-truncate">{{ listings.selected?.title }}</p>
                            <p class="ma-0 text-truncate">{{ listings.selected?.location }}</p>
                            <v-divider class="my-1"/>
                            <div v-if="listings.selected.agent" class="d-flex align-center justify-content-between">
                                <div class="d-flex align-center gap-2">
                                    <v-avatar size="48" :image="listings.selected?.agent?.photo"/>
                                    <div>
                                        <h5 class="ma-0">{{ listings.selected?.agent?.name }}</h5>
                                        <p class="ma-0">{{ listings.selected?.agent?.email }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-center gap-2">
                                    <v-btn
                                        v-if="listings.selected?.agent?.mobile"
                                        @click="callToAgent(listings.selected?.agent?.mobile)"
                                        v-tooltip:top="'Call'"
                                        size="x-small"
                                        icon="mdi-phone-outline"
                                        rounded
                                        variant="tonal"
                                        color="success"
                                    />
                                    <v-btn
                                        v-if="listings.selected?.agent?.telegram_user_name"
                                        @click="sendTelegramMessage(listings.selected?.agent?.telegram_user_name)"
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
                    <v-col cols="12">
                        <v-select
                            v-if="listings.selected"
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
                    </v-col>
                    <v-btn
                        v-if="listings.selected"
                        @click="onSendViaEmailAndTelegram()"
                        :loading="listings.submitting"
                        color="success"
                        variant="tonal"
                        block
                    >
                        Post it into Group
                    </v-btn>

                </v-card-text>
            </v-card>

            <v-form v-if="selectedTab === 'Create a New'" ref="createForm">
                <v-card variant="flat" border>
                    <v-card-text>

                        <v-label>Select Category*</v-label>

                        <v-row dense>
                            <v-col cols="6" v-for="purpose in purposes">
                                <v-btn
                                    @click="updateFor(purpose)"
                                    :key="purpose"
                                    color="success"
                                    :variant="formData.for === purpose ? 'tonal' : 'text'"
                                    border
                                    block
                                    style="text-transform: capitalize;letter-spacing: normal; font-size: 12px; line-height: 1.2; padding: 4px 12px;"
                                >
                                    <span>{{ purpose }}</span>
                                    <v-icon class="ml-2" icon="mdi-check" v-if="formData.for === purpose"/>
                                </v-btn>
                            </v-col>
                        </v-row>

                        <v-divider/>

                        <v-label>Select Type*</v-label>

                        <v-card
                            max-width="85vw"
                            variant="flat"
                            class="d-flex gap-2 overflow-x-scroll"
                        >
                            <v-card
                                class="d-flex align-center justify-center w-100 text-center cursor-pointer"
                                v-for="category in categories.data"
                                :key="category.id"
                                @click="updateType(category.id)"
                                color="success"
                                :variant="formData.property_category_id === category.id ? 'tonal' : 'text'"
                                border
                                min-height="120"
                                min-width="120"
                            >
                                {{ category.name }}
                            </v-card>
                        </v-card>

                        <v-divider/>

                        <v-label>Beds*</v-label>

                        <v-card max-width="85vw" variant="flat" class="d-flex align-center gap-2 overflow-x-scroll">
                            <v-btn
                                v-for="bed in beds.data"
                                :key="bed.id"
                                @click="updateBed(bed.id)"
                                :variant="formData.bed_id === bed.id ? 'tonal' : 'text'"
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

                        <v-label>Baths*</v-label>

                        <v-card max-width="85vw" variant="flat" class="d-flex align-center gap-2 overflow-x-scroll">
                            <v-btn
                                v-for="bath in baths.data"
                                :key="bath.id"
                                @click="updateBath(bath.id)"
                                :variant="formData.bath_id === bath.id ? 'tonal' : 'text'"
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

                        <v-label>Location*</v-label>

                        <VAutocomplete
                            v-model="formData.location"
                            v-model:search="locations.search"
                            @update:search="debounceLocations()"
                            :items="locations.data"
                            :loading="locations.loading"
                            item-value="id"
                            item-title="name"
                            variant="outlined"
                            density="compact"
                            clearable
                            clear-icon="mdi-close"
                            clear-on-select
                            return-object
                            placeholder="Search for location"
                            prepend-inner-icon="mdi-magnify"
                            :rules="[(v) => !!v || 'Location is required']"
                        >
                            <template #item="{ props, item }">
                                <v-list-item v-bind="props" title="">
                                    <v-list-item-title>{{ item?.raw?.name }}</v-list-item-title>
                                    <v-list-item-subtitle>{{ item?.raw?.path }}</v-list-item-subtitle>
                                </v-list-item>
                            </template>
                        </VAutocomplete>

                        <v-divider/>

                        <v-label>Views*</v-label>

                        <v-card max-width="85vw" variant="flat" class="d-flex align-center gap-2 overflow-x-scroll">
                            <v-btn
                                v-for="view in views.data"
                                :key="view.id"
                                @click="toggleViews(view.key)"
                                :variant="formData.view.includes(view.key) ? 'tonal' : 'text'"
                                rounded
                                border
                                size="small"
                                color="success"
                                style="text-transform: capitalize;letter-spacing: normal;"
                            >
                                {{ view.value }}
                            </v-btn>
                        </v-card>

                        <v-divider/>

                        <v-label>Amenities*</v-label>

                        <v-card max-width="85vw" variant="flat" class="d-flex align-center gap-2 overflow-x-scroll">
                            <v-btn
                                v-for="amenity in amenities.data"
                                :key="amenity.id"
                                @click="toggleAmenity(amenity.id)"
                                :variant="formData.features.includes(amenity.id) ? 'tonal' : 'text'"
                                rounded
                                border
                                size="small"
                                color="success"
                                style="text-transform: capitalize;letter-spacing: normal;"
                            >
                                {{ amenity.title }}
                            </v-btn>
                        </v-card>

                        <v-divider/>

                        <v-row dense>
                            <v-col cols="12">
                                <v-text-field
                                    v-model="formData.title"
                                    label="Title*"
                                    variant="outlined"
                                    density="compact"
                                    :rules="[
                                        (v) => !!v || 'Title is required',
                                        (v) => v.length <= 50 || 'Title must be less than 50 characters',
                                        (v) => v.length >= 35 || 'Title must be at least 35 characters'
                                    ]"
                                >
                                    <template #counter="counter">
                                        {{ counter.value }} / 50
                                    </template>
                                </v-text-field>
                            </v-col>
                            <v-col cols="12">
                                <v-textarea
                                    v-model="formData.description"
                                    label="Description*"
                                    variant="outlined"
                                    density="compact"
                                    :rules="[
                                        (v) => !!v || 'Description is required',
                                        (v) => v.length >= 35 || 'Description must be at least 50 characters',
                                        (v) => v.length <= 2000 || 'Description must be less than 2000 characters'
                                    ]"
                                >
                                    <template #counter="counter">
                                        {{ counter.value }} / 2000
                                    </template>
                                </v-textarea>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model.number="formData.price"
                                    label="Price*"
                                    variant="outlined"
                                    density="compact"
                                    :rules="[(v) => !!v || 'Price is required']"
                                >
                                    <template #append-inner>
                                        {{ getHumanReadable(formData.price) }}
                                    </template>
                                </v-text-field>
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model.number="formData.parking"
                                    label="Parking*"
                                    variant="outlined"
                                    density="compact"
                                    :rules="[(v) => !!v || 'Parking is required']"
                                />
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model.number="formData.built_up_area"
                                    label="Built Up Area*"
                                    variant="outlined"
                                    density="compact"
                                    suffix="sqft"
                                    :rules="[(v) => !!v || 'Built Up Area is required']"
                                />
                            </v-col>
                            <v-col cols="6">
                                <v-text-field
                                    v-model.number="formData.plot_size"
                                    label="Plot Area*"
                                    variant="outlined"
                                    density="compact"
                                    suffix="sqft"
                                    :rules="[(v) => !!v || 'Plot Area is required']"
                                />
                            </v-col>

                            <v-col cols="4" v-for="status in furnishingStatuses">
                                <v-btn
                                    @click="updateFurnishingStatus(status.value)"
                                    :key="status.value"
                                    color="success"
                                    :variant="formData.furnished === status.value ? 'tonal' : 'text'"
                                    border
                                    block
                                    style="text-transform: capitalize;letter-spacing: normal; font-size: 12px; line-height: 1.2; padding: 6px 8px;"
                                >
                                    <span>{{ status.title }}</span>
                                   
                                </v-btn>
                            </v-col>
                            <v-col cols="12">
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
                            </v-col>

                            <v-col cols="3">
                                <v-btn @click="resetFormData" variant="tonal" color="error" block style="font-size: 12px; line-height: 1.2; padding: 4px 12px;">Reset</v-btn>
                            </v-col>
                            <v-col cols="9">
                                <v-btn @click="onCreate" :loading="loading" variant="flat"
                                       color="#D3A188" block style="font-size: 12px; line-height: 1.2; padding: 4px 12px;">
                                    Create & Post into Group
                                </v-btn>
                            </v-col>
                        </v-row>

                    </v-card-text>
                </v-card>
            </v-form>

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
</style>
