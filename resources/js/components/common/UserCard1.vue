<script setup>
import moment from "moment/moment.js";
const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
    date: {
        type: String,
        required: false,
        default: null,
    },
    marketedBy: {
        type: Object,
        default: null,
    },
    leadSource: {
        type: Object,
        default: null,
    },
    chipText: {
        type: String,
        default: null,
    },
    splitCommission: {
        type: Object,
        default: null,
    },
    showJustAvatar: {
        type: Boolean,
        default: false,
    },
    size: {
        type: Number,
        required: false,
        default: 34
    },
    isShowDepartmentType: {
        type: Number,
        required: false,
        default: 0
    },
    fromCompany: {
        type: Boolean,
        required: false,
        default: false
    }
});

const getColorByText = (text) => {
    switch (text) {
        case "Paid":
            return "success";
        case "Partially Paid":
            return "warning";
        case "Unpaid":
            return "error";
        default:
            return "primary";
    }
};
const getAvatarColor = (text) => {
    if (!text) return "primary";

    const colors = [
        "primary",
        "secondary",
        "success",
        "warning",
        "info",
        "error"
    ];

    const hash = Array.from(text).reduce((acc, char) => acc + char.charCodeAt(0), 0);
    return colors[hash % colors.length];
};

const getDepartmentColor = (departmentType) => {
    switch (departmentType) {
        case "Primary":
            return "primary";
        case "Secondary":
            return "secondary";
        case "AX Corporate":
            return "success";
        case "Property Management":
            return "warning";
        case "Owner Relations":
            return "info";
        case "Exclusive":
            return "error";
        default:
            return "primary";
    }
};
const avatarText = value => {
    if (!value) return ''

    const nameArray = value.trim().split(' ').filter(word => word) // Remove extra spaces

    if (nameArray.length === 1) {
        // If only one word, take the first two letters
        return nameArray[0].slice(0, 2).toUpperCase()
    }

    // Otherwise, take the first letter of the first two words
    return nameArray.slice(0, 2).map(word => word.charAt(0).toUpperCase()).join('')
}
</script>
<template>

    <div v-if="user" class="d-flex align-center gap-x-2">
        <VBadge
            :model-value="!!user?.parent?.group"
            :content="user?.parent?.group?.name?.charAt(0)"
            :color="getDepartmentColor(user?.parent?.group?.name)"
            bordered
            location="bottom end"
        >
            <VTooltip v-if="user?.department_type" activator="parent">
                {{ user?.parent?.group?.name }}
            </VTooltip>
            <VAvatar
                :size="size"
                class="cursor-pointer"
                :variant="
        !user.thumb_url || !user.image_path ? 'tonal' : undefined
      "
                :color="getAvatarColor(avatarText(user.first_name))"
            >
                <VImg v-if="user.thumb_url" :src="user.thumb_url"/>
                <span v-else>{{ avatarText(user.first_name) }} </span>


                <VMenu activator="parent" location="bottom" class="pa-4">
                    <template #default>
                        <v-card variant="flat" class="pa-4">
                            <div class="d-flex align-center gap-2">
                                <VAvatar
                                    color="primary"
                                    variant="tonal"
                                    size="48"
                                    class="text-lg"
                                >
                                    <VImg
                                        v-if="user.thumb_url"
                                        :src="user.thumb_url"
                                    />
                                    <span v-else>
                  {{ avatarText(user?.name) }}
                </span>
                                </VAvatar>
                                <div>
                                    <h3 class="text-h6 text-no-wrap" v-text="user?.name"></h3>
                                    <span class="text-xs" v-if="user?.legal_name">
                  Legal Name: {{ user?.legal_name }}
                </span>
                                    <p class="mb-0 text-sm" v-text="user?.email"/>
                                    <div class="d-flex align-center gap-1 mt-1" v-if="user?.parent">
                                        <v-icon icon="tabler-corner-down-right"/>
                                        <VAvatar
                                            color="primary"
                                            variant="tonal"
                                            size="20"
                                            class="text-lg"
                                        >
                                            <VImg
                                                v-if="user?.parent?.thumb_url"
                                                :src="user?.parent?.thumb_url"
                                            />
                                            <span v-else>
                      {{ avatarText(user?.parent?.name) }}
                    </span>
                                        </VAvatar>
                                        <span class="text-sm">
                    {{ user?.parent?.name }}
                  </span>
                                    </div>
                                </div>
                            </div>
                            <v-divider class="my-3"/>
                            <div class="d-flex align-center justify-space-between">
                                <div class="d-flex gap-2">
                                    <VTooltip v-if="user?.whatsapp">
                                        <template #activator="{ props }">
                                            <VIcon

                                                color="primary"
                                                v-bind="props"
                                                icon="tabler-brand-whatsapp"
                                                size="20"
                                            />
                                        </template>
                                        <span>{{ item?.user?.whatsapp }}</span>
                                    </VTooltip>
                                    <VTooltip v-if="user?.mobile">
                                        <template #activator="{ props }">
                                            <VIcon

                                                color="primary"
                                                v-bind="props"
                                                icon="tabler-phone"
                                                size="20"
                                            />
                                        </template>
                                        <span>{{ user?.mobile }}</span>
                                    </VTooltip>
                                    <VTooltip v-if="user?.email">
                                        <template #activator="{ props }">
                                            <VIcon

                                                color="primary"
                                                v-bind="props"
                                                icon="tabler-mail"
                                                size="20"
                                            />
                                        </template>
                                        <span>{{ user?.email }}</span>
                                    </VTooltip>
                                </div>
                                <div class="text-subtitle-2">
                                    {{ user?.designation }}
                                </div>
                            </div>
                        </v-card>
                    </template>
                </VMenu>
            </VAvatar>


        </VBadge>
        <div class="d-flex flex-column ml-2" v-if="!showJustAvatar">
      <span :class="fromCompany ? 'text-warning' : 'text-primary'" style="white-space: nowrap">
        {{ user.first_name }}

        <v-chip
            :color="getColorByText(leadSource)"
            v-if="leadSource"
            size="small"
            style="font-size: 11px; padding: 6px; height: 16px !important"
        >
          {{ leadSource }}
        </v-chip>


      </span>
            <span class="text-xs">
        {{ user.email }}
      </span>
            <div class="cursor-pointer" v-if="date">
                <VTooltip activator="parent" location="top">
                    <!--          <DateDisplay :date="date"/>-->
                    <div class="cursor-pointer">
                        <VTooltip activator="parent" location="top">
                            {{ moment(date).format("MMMM Do YYYY, h:mm:ss a") }}
                        </VTooltip>

                        <span class="text-xs">
                {{ moment(date).fromNow() }}
              </span>
                    </div>
                </VTooltip>
                <!--        <DateDisplay :date="date"/>-->
                <div class="cursor-pointer">
                    <VTooltip activator="parent" location="top">
                        {{ moment(date).format("MMMM Do YYYY, h:mm:ss a") }}
                    </VTooltip>

                    <span class="text-xs">{{ moment(date).fromNow() }}</span>
                </div>
            </div>

        </div>
    </div>
</template>

<style lang="scss" scoped>
:deep(.custom-tooltip-bg) {
    background-color: white !important;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

:deep(.v-badge__badge) {
    font-size: 10px !important;
    padding: 0 !important;
    height: 1.125rem !important;
    width: 1.125rem !important;
    border-radius: 10px !important;
    min-width: 0;
}
</style>
