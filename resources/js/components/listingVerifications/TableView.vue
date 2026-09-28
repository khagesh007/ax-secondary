<script setup>
import {ref} from "vue";
import moment from "moment";

const props = defineProps({
    data: {type: Object, required: true},
    getSubmissions: {type: Function, required: true},
    toggleSnackbar: {type: Function, required: true},
});

const headers = ref([
    {
        title: 'Property',
        align: 'start',
        sortable: true,
        key: 'property_id',
    },
    {
        title: 'Agent',
        align: 'start',
        sortable: true,
        key: 'user_id',
    },
    {
        title: 'Status',
        align: 'start',
        sortable: true,
        key: 'status',
    },
    {
        title: 'Created At',
        align: 'start',
        sortable: true,
        key: 'portal_created_at',
    },
    {
        title: 'Expires At',
        align: 'start',
        sortable: true,
        key: 'portal_expires_at',
    },
    {
        title: 'Updated At',
        align: 'start',
        sortable: true,
        key: 'portal_updated_at',
    },
    {
        title: 'Action',
        align: 'start',
        sortable: false,
        key: 'actions',
    },
])

const showingConfirmDialog = ref(false)
const reSubmitting = ref(false)

const selected = ref(null);

const reSubmit = async () => {
    reSubmitting.value = true;
    try {
        const formData = new FormData();

        formData.append("id", selected.value.id);

        const {data} = await axios.post('/reporting/admin/listing-verifications/re-submit-for-verification',
            formData,
        );
        if (data.status) {
            showingConfirmDialog.value = false;
            selected.value = false;
            props.getSubmissions();
            props.toggleSnackbar(data.message)
        } else {
            props.toggleSnackbar(data.message, "error")
        }

    } catch (error) {
        console.error(error);
    } finally {
        reSubmitting.value = false;
    }
}

const confirmReSubmit = (item = null) => {
    if (item) {
        selected.value = item;
        showingConfirmDialog.value = true;
    } else {
        selected.value = null;
        showingConfirmDialog.value = false;
    }
}

</script>

<template>
    <v-dialog
        v-model="showingConfirmDialog"
        max-width="400"
        persistent
    >
        <v-card
            prepend-icon="mdi-help"
            text="Are you sure want to re-submit?"
            title="Confirm"
        >
            <template v-slot:actions>
                <v-spacer></v-spacer>

                <v-btn
                    @click="selected = null; showingConfirmDialog = false"
                    :disabled="reSubmitting"
                >
                    No
                </v-btn>

                <v-btn
                    @click="reSubmit"
                    :loading="reSubmitting"
                >
                    Yes
                </v-btn>
            </template>
        </v-card>
    </v-dialog>

    <v-data-table-server
        v-model:items-per-page="data.perPage"
        :headers="headers"
        :items="data.data"
        :items-length="data.total"
        :loading="data.loading"
        :page="data.page"
        item-value="name"
        :search="data.search"
        @update:options="getSubmissions"
    >
        <template #item.property_id="{ item }">
            <div v-if="item.property">

                <!--                <VImg-->
                <!--                    v-if="item?.property?.thumbnail"-->
                <!--                    :src="item?.property?.thumbnail"-->
                <!--                    :lazy-src="item?.property?.thumbnail"-->
                <!--                    min-height="60"-->
                <!--                    max-height="60"-->
                <!--                    min-width="60"-->
                <!--                    max-width="60"-->
                <!--                    rounded-->
                <!--                    cover-->
                <!--                />-->
                <!--                <VAvatar v-else rounded border size="60">-->
                <!--                    &lt;!&ndash;                                        <VImg :src="ListingPlaceholderImage"/>&ndash;&gt;-->
                <!--                </VAvatar>-->
                <span class="badge align-middle badge-soft-dark">{{ item.property?.for }}</span>
                <div class="flex-grow-1">
                    <div class="d-flex" style="margin: 0;">
                        <div class="ms-1" style="margin: 0;">
                            <h6 class="fs-12 mb-0" style="margin: 0;">{{ item.property?.ref_no }} -
                                {{ item.property?.category }}</h6>
                            <small class="text-muted" style="margin: 0;">{{ item.property?.location }}</small><br>
                            <span class="text-info"
                                  style="margin: 0;font-weight: 500;">AED {{ item.property?.price?.price }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else>-</div>

        </template>

        <template #item.user_id="{ item }">
            <div
                v-if="item.user"
                class="d-flex align-items-center userDetails" data-agent-id="1398" title="Leilei Chen">
                <div class="flex-shrink-0 me-2">
                    <img :src="item.user?.image_url" alt="" class="avatar-xs rounded-circle">
                </div>
                <div class="flex-grow-1">{{ item.user?.name }}
                    <p title="Created at" class="mb-0">
                        <small>{{ item.user?.email }}</small>
                    </p>
                </div>
            </div>
            <div v-else>-</div>
        </template>

        <template #item.portal_created_at="{ item }">
                                      <span v-if="item.portal_created_at" class="cursor-pointer">
                                        <VTooltip activator="parent" location="top">
                                          {{ moment(item.portal_created_at).format("MMMM Do YYYY, h:mm:ss a") }}
                                        </VTooltip>
                                        {{ moment(item.portal_created_at).fromNow() }}
                                      </span>
            <span v-else>-</span>
        </template>

        <template #item.portal_expires_at="{ item }">
                                      <span v-if="item.portal_expires_at" class="cursor-pointer">
                                        <VTooltip activator="parent" location="top">
                                          {{ moment(item.portal_expires_at).format("MMMM Do YYYY, h:mm:ss a") }}
                                        </VTooltip>
                                        {{ moment(item.portal_expires_at).fromNow() }}
                                      </span>
            <span v-else>-</span>
        </template>

        <template #item.portal_updated_at="{ item }">
                                      <span v-if="item.portal_updated_at" class="cursor-pointer">
                                        <VTooltip activator="parent" location="top">
                                          {{ moment(item.portal_updated_at).format("MMMM Do YYYY, h:mm:ss a") }}
                                        </VTooltip>
                                        {{ moment(item.portal_updated_at).fromNow() }}
                                      </span>
            <span v-else>-</span>
        </template>

        <template #item.actions="{ item }">
            <v-btn
                v-if="item.status == 'expired' || item.status == 'rejected'"
                variant="tonal"
                density="compact"
                @click="confirmReSubmit(item)"
            >
                Re Submit
            </v-btn>
        </template>
    </v-data-table-server>
</template>

<style scoped>
</style>
