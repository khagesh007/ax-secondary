<template>
    <div class="p-3 d-flex flex-column h-100">
        <div class="mb-3">
            <h5 class="mb-0 fw-semibold">My Drive</h5>
        </div>
        <div class="search-box">
            <input type="text" class="form-control bg-light border-light" placeholder="Search here..."
                data-ddg-inputtype="unknown">
            <i class="ri-search-2-line search-icon"></i>
        </div>
        <div class="mt-3 mx-n4 px-4 file-menu-sidebar-scroll simplebar-scrollable-y" data-simplebar="init">
            <div class="simplebar-wrapper" style="margin: 0px -24px;">
                <div class="simplebar-height-auto-observer-wrapper">
                    <div class="simplebar-height-auto-observer"></div>
                </div>
                <div class="simplebar-mask">
                    <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                        <div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content"
                            style="height: 100%; overflow: hidden scroll;">
                            <div class="simplebar-content" style="padding: 0px 24px;">
                                <ul class="list-unstyled file-manager-menu">
                                    <li>
                                        <a data-bs-toggle="collapse" href="#collapseExample" role="button"
                                            aria-expanded="false" aria-controls="collapseExample" class="collapsed active">
                                            <i class="ri-folder-2-line align-bottom me-2"></i> <span
                                                class="file-list-link">My Drive</span>
                                        </a>
                                        <div class="collapse" id="collapseExample" style="">
                                            <ul class="sub-menu list-unstyled">
                                                <li>
                                                    <a href="#!">Assets</a>
                                                </li>
                                                <li>
                                                    <a href="#!">Marketing</a>
                                                </li>
                                                <li>
                                                    <a href="#!">Personal</a>
                                                </li>
                                                <li>
                                                    <a href="#!">Projects</a>
                                                </li>
                                                <li>
                                                    <a href="#!">Templates</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li>
                                        <a href="#!"><i class="ri-file-list-2-line align-bottom me-2"></i> <span
                                                class="file-list-link">Documents</span></a>
                                    </li>
                                    <li>
                                        <a href="#!"><i class="ri-image-2-line align-bottom me-2"></i> <span
                                                class="file-list-link">Media</span></a>
                                    </li>
                                    <li>
                                        <a href="#!"><i class="ri-history-line align-bottom me-2"></i> <span
                                                class="file-list-link">Recent</span></a>
                                    </li>
                                    <li>
                                        <a href="#!"><i class="ri-star-line align-bottom me-2"></i> <span
                                                class="file-list-link">Important</span></a>
                                    </li>

                                    <li>
                                        <a href="#!"><i class="ri-delete-bin-line align-bottom me-2"></i> <span
                                                class="file-list-link">Deleted</span></a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="simplebar-placeholder" style="width: 316px; height: 193px;"></div>
            </div>
            <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
            </div>
            <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                <div class="simplebar-scrollbar"
                    style="height: 64px; display: block; transform: translate3d(0px, 0px, 0px);"></div>
            </div>
        </div>


        <div class="mt-auto">
            <h6 class="fs-11 text-muted text-uppercase mb-3">Storage Status</h6>
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0">
                    <i class="ri-database-2-line fs-17"></i>
                </div>
                <div class="flex-grow-1 ms-3 overflow-hidden">
                    <div class="progress mb-2 progress-sm">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25"
                            aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span class="text-muted fs-12 d-block text-truncate"><b>47.52</b>GB used of <b>119</b>GB</span>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import useGlobalHelpers from "../../../composables/helpers/global";
import { onMounted, ref, watchEffect } from "vue";

const { formatDate } = useGlobalHelpers();


</script>
<style scoped>
.link-icon {
    margin-left: 5px;
}
</style>
