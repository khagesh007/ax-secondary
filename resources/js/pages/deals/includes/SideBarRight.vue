<template>
    <div class="offcanvas offcanvas-end" data-bs-scroll="true" data-bs-backdrop="false" tabindex="-1"
         id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasRightLabel">Deal Timeline</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">

            <!-- canvas body start -->

            <div data-simplebar="init" style="height: calc(100vh - 112px);" class="simplebar-scrollable-y">
                <div class="simplebar-wrapper" style="margin: 0px;">
                    <div class="simplebar-height-auto-observer-wrapper">
                        <div class="simplebar-height-auto-observer"></div>
                    </div>
                    <div class="simplebar-mask">
                        <div class="simplebar-offset" style="right: 0px; bottom: 0px;">
                            <div class="simplebar-content-wrapper" tabindex="0" role="region"
                                 aria-label="scrollable content" style="height: 100%; overflow: hidden scroll;">
                                <div class="simplebar-content" style="padding: 0px;">
                                    <div class="acitivity-timeline p-4">

                                        <div class="acitivity-item py-3 d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs acitivity-avatar">
                                                    <div
                                                        class="avatar-title rounded-circle bg-danger-subtle text-success">
                                                        <i class="ri-shopping-bag-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">New document uploaded for Buyer primary</h6>
                                                <p class="text-muted mb-2">KYC Document uploaded</p>
                                                <small class="mb-0 text-muted">19 Nov</small>
                                            </div>
                                        </div>
                                        <div class="acitivity-item py-3 d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs acitivity-avatar">
                                                    <div
                                                        class="avatar-title rounded-circle bg-danger-subtle text-danger">
                                                        <i class="ri-shopping-bag-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Agent has added secondary Buyer</h6>
                                                <p class="text-muted mb-2">added a new member to velzon dashboard</p>
                                                <small class="mb-0 text-muted">19 Nov</small>
                                            </div>
                                        </div>
                                        <div class="acitivity-item py-3 d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs acitivity-avatar">
                                                    <div
                                                        class="avatar-title rounded-circle bg-danger-subtle text-danger">
                                                        <i class="ri-shopping-bag-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Agent has added primary Buyer<span
                                                    class="badge bg-danger-subtle text-danger align-middle ms-1">Documents missing</span>
                                                </h6>
                                                <p class="text-muted mb-2">Agent has added new primary agent to deal but documents is missing.</p>
                                                <small class="mb-0 text-muted">16 Nov</small>
                                            </div>
                                        </div>

                                        <div class="acitivity-item py-3 d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs acitivity-avatar">
                                                    <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                                        <i class="ri-line-chart-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">Owner/Seller Information updated</h6>
                                                <p class="text-muted mb-2">Owner <span class="text-danger">Alex Gerral</span>
                                                   data updated by agent.</p>
                                                <small class="mb-0 text-muted">15 Oct</small>
                                            </div>
                                        </div>
                                        <div class="acitivity-item d-flex">
                                            <div class="flex-shrink-0">
                                                <div class="avatar-xs acitivity-avatar">
                                                    <div class="avatar-title rounded-circle bg-info-subtle text-info">
                                                        <i class="ri-line-chart-line"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-1">New Deal created <span
                                                    class="badge bg-success-subtle text-success align-middle">Completed</span>
                                                </h6>
                                                <p class="text-muted mb-2">Agent <span
                                                    class="text-secondary">Erica245</span> has created a deal.</p>
                                                <small class="mb-0 text-muted">26 Aug</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="simplebar-placeholder" style="width: 399px; height: 985px;"></div>
                </div>
                <div class="simplebar-track simplebar-horizontal" style="visibility: hidden;">
                    <div class="simplebar-scrollbar" style="width: 0px; display: none;"></div>
                </div>
                <div class="simplebar-track simplebar-vertical" style="visibility: visible;">
                    <div class="simplebar-scrollbar"
                         style="height: 184px; transform: translate3d(0px, 242px, 0px); display: block;"></div>
                </div>
            </div>

            <!-- canvas body end -->

        </div>
    </div>


</template>
<script setup>
</script>
<style scoped>
.offcanvas .offcanvas-header {
    background-color: #f4f4f4 !important;
}
</style>
