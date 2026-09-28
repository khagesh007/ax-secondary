<script setup>
import { ref, onMounted, watch } from "vue";
import { VAutocomplete, VAvatar, VImg, VListItem } from "vuetify/components";
import axios from "axios";
const props = defineProps({
    filters: {
        type: Object,
        required: true,
    },
    model: {
        type: String,
        required: true,
    },
    itemValue: {
        type: [String, Number],
        default: "id",
    },
    revokedUsers: {
        type: Array,
        default: () => [],
    },
    for: {
        type: String,
        default: null,
    },
});

const search = ref("");
const loading = ref(false);
const items = ref([]);
const localModel = ref(props.model);
const emit = defineEmits(["update:model", "selectedObject"]);

watch(() => props.model, (newValue) => {
    localModel.value = newValue;
});
const fetch = async () => {
    loading.value = true;

    const selectedIds = Array.isArray(props.model)
        ? props.model.map((item) => (typeof item === "object" ? item.id : item))
        : props.model
            ? [typeof props.model === "object" ? props.model.id : props.model]
            : [];

    const params = {
        searchTerm: search.value,
        additionalData: selectedIds,
        for: props.for,
        department: props.filters?.department,
    };

    try {
        const response = await axios.get(`/pipeline/agents`, { params });
        const responseData = params.additionalData.length > 0 ? response.data : response.data.data;

        items.value = Array.isArray(responseData)
            ? responseData.filter((user) => !props.revokedUsers.includes(user[props.itemValue]))
            : [];
    } catch (error) {
        console.error("Error fetching users:", error);
        items.value = [];
    } finally {
        loading.value = false;
    }
};

const handleSearch = async (query) => {
    if (!query) {
        items.value = [];
        return;
    }

    loading.value = true;

    try {
        const response = await axios.get("/pipeline/agents", {
            params: {
                q: query,
                department: props.filters?.department,
            },
        });

        items.value = response.data.data;
    } catch (error) {
        console.error("Error fetching agents:", error);
        items.value = [];
    } finally {
        loading.value = false;
    }
};
function textToAvatar(text, size = 32) {
    if (!text) return '';

    // Extract initials
    const words = text.split(' ');
    const initials = words.length > 1
        ? words[0][0].toUpperCase() + words[1][0].toUpperCase()
        : text[0].toUpperCase();

    // Generate a random color based on text
    const hash = text.split('').reduce((acc, char) => acc + char.charCodeAt(0), 0);
    const color = `hsl(${hash % 360}, 70%, 50%)`;

    // Create avatar element
    const avatarStyle = `
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: ${size}px;
        height: ${size}px;
        border-radius: 50%;
        background-color: ${color};
        color: white;
        font-size: ${size / 2}px;
        font-weight: bold;
        text-transform: uppercase;
    `;

    return `<div style="${avatarStyle}">${initials}</div>`;
}
function handleModelValueUpdate(value) {
    console.log("Model value updated:", value);
    localModel.value = value.id;
    emit('update:model', value.id);
    const selectedObject = items.value.find(item => item[props.itemValue] === value);
    console.log("Selected object:", selectedObject);
    emit('selectedObject', selectedObject || null);
}
onMounted(fetch);
</script>

<template>
    <!--      v-model="localModel"    -->
    <VAutocomplete

        :model-value="props.model"
        v-model:search="search"
        @update:search="handleSearch"
        @update:model-value="handleModelValueUpdate"
        :items="items"
        :loading="loading"
        :item-value="itemValue"
        item-title="name"
        clearable
        clear-icon="tabler-x"
        variant="outlined"
        class="bg-white"
        density="compact"
    >
        <template #selection="{ item }">
      <span class="whitespace-no-wrap text-truncate">
        {{ item?.raw?.name }}
      </span>
        </template>

        <template #item="{ props, item }">
            <v-list-item v-bind="props" title="" class="pa-0 p-2">
                <template v-slot:default>
                    <div class="d-flex align-center gap-2 p-2">
                        <v-avatar size="32" color="primary" variant="tonal">
                            <v-img
                                v-if="item?.raw?.thumb_url"
                                :src="item.raw.thumb_url"
                                :lazy-src="item.raw.thumb_url"
                            />
                            <span v-else>{{ textToAvatar(item.raw.name) }}</span>
                        </v-avatar>
                        <div>
                            <div class="text-body-1 font-weight-medium">{{ item.raw.name }}</div>
                            <div class="text-body-2">{{ item.raw.email }}</div>
                        </div>
                    </div>
                </template>
            </v-list-item>
        </template>
    </VAutocomplete>
</template>
