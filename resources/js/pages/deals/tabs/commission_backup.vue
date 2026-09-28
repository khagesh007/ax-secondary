<template>
    <div class="tab-pane" id="nav-commission" role="tabpanel">
        <div class="row mb-2">
            <div class="col-9"><h4>Commissions</h4></div>
            <div class="col-3"><button class="btn btn-primary action-add float-end">Add New</button> </div>
        </div>
        <div class="row gy-2 mb-2">
            <div class="col-md-12 col-lg-12" v-for="(split, index) in splits" :key="index">
                <div class="card mb-0">
                    <div class="card-body">
                        <div class="d-lg-flex align-items-center">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded"><img src="assets/images/users/avatar-10.jpg" alt=""
                                                                    class="member-img img-fluid d-block rounded"></div>
                            </div>
                            <div class="ms-lg-3 my-3 my-lg-0"><a href="pages-profile.html"><h5 class="fs-16 mb-2">{{split.user.full_name}}</h5></a>
                                <p class="text-muted mb-0">{{split.user.position}}</p></div>

                            <div class="d-flex flex-wrap gap-2 align-items-center mx-auto my-3 my-lg-0">
                                <div class="badge text-bg-success"><i class="mdi mdi-star me-1"></i>4.2</div>
                                <div class="text-muted">AED {{split.split_amount}}</div>
                            </div>
                            <div><a href="#!" class="btn btn-soft-success">View Details</a> <a href="#!"
                                                                                               class="btn btn-ghost-danger btn-icon custom-toggle"
                                                                                               data-bs-toggle="button"
                                                                                               aria-pressed="false">
                                <span class="icon-on"><i class="ri-bookmark-line align-bottom"></i></span> <span
                                class="icon-off"><i class="ri-bookmark-3-fill align-bottom"></i></span> </a></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="items-align-center obligation">
                            <div class="mt-3">
                                <i class="bx bx-plus-circle fs-24"></i>
                                <br>
                                Create Obligation
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <h4>Debit (1)</h4>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header font-weight-bold">
                        Total Commission
                    </div>
                    <div class="card-body">
                        <div class="position-relative m-4">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="25"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <button type="button"
                                    class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill"
                                    style="width: 2rem; height:2rem;">1
                            </button>
                            <button type="button"
                                    class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-primary squre-pill"
                                    style=" height:2rem;">AED 100,000
                            </button>
                            <button type="button"
                                    class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-light rounded-pill"
                                    style="width: 2rem; height:2rem;">3
                            </button>
                        </div>
                        <div class="mt-3 pt-2">
                            <div class="progress progress-lg rounded-pill">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"
                                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-info" role="progressbar" style="width: 18%"
                                     aria-valuenow="18"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-success" role="progressbar" style="width: 22%"
                                     aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 16%"
                                     aria-valuenow="16" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 19%"
                                     aria-valuenow="19"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="card-price-span mt-2">
                        <span>
                            AED 100,000
                        </span>
                            <span>
                            AED 500,000
                        </span><span>
                            AED 600,000
                        </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <h4>Credit (3)</h4>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header font-weight-bold">
                        Agent Share
                    </div>
                    <div class="card-body">
                        <div class="position-relative m-4">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="25"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <button type="button"
                                    class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill"
                                    style="width: 2rem; height:2rem;">1
                            </button>
                            <button type="button"
                                    class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-primary squre-pill"
                                    style=" height:2rem;">AED 100,000
                            </button>
                            <button type="button"
                                    class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-light rounded-pill"
                                    style="width: 2rem; height:2rem;">3
                            </button>
                        </div>
                        <div class="mt-3 pt-2">
                            <div class="progress progress-lg rounded-pill">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"
                                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-info" role="progressbar" style="width: 18%"
                                     aria-valuenow="18"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-success" role="progressbar" style="width: 22%"
                                     aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 16%"
                                     aria-valuenow="16" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 19%"
                                     aria-valuenow="19"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="card-price-span mt-2">
                        <span>
                            AED 100,000
                        </span>
                            <span>
                            AED 500,000
                        </span><span>
                            AED 600,000
                        </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header font-weight-bold">
                        Manage Share
                    </div>
                    <div class="card-body">
                        <div class="position-relative m-4">
                            <div class="progress" style="height: 1px;">
                                <div class="progress-bar" role="progressbar" style="width: 50%;" aria-valuenow="25"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <button type="button"
                                    class="position-absolute top-0 start-0 translate-middle btn btn-sm btn-primary rounded-pill"
                                    style="width: 2rem; height:2rem;">1
                            </button>
                            <button type="button"
                                    class="position-absolute top-0 start-50 translate-middle btn btn-sm btn-primary squre-pill"
                                    style=" height:2rem;">AED 100,000
                            </button>
                            <button type="button"
                                    class="position-absolute top-0 start-100 translate-middle btn btn-sm btn-light rounded-pill"
                                    style="width: 2rem; height:2rem;">3
                            </button>
                        </div>
                        <div class="mt-3 pt-2">
                            <div class="progress progress-lg rounded-pill">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 25%"
                                     aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-info" role="progressbar" style="width: 18%"
                                     aria-valuenow="18"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-success" role="progressbar" style="width: 22%"
                                     aria-valuenow="22" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 16%"
                                     aria-valuenow="16" aria-valuemin="0" aria-valuemax="100"></div>
                                <div class="progress-bar bg-danger" role="progressbar" style="width: 19%"
                                     aria-valuenow="19"
                                     aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="card-price-span mt-2">
                        <span>
                            AED 100,000
                        </span>
                            <span>
                            AED 500,000
                        </span><span>
                            AED 600,000
                        </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import {onMounted, ref} from 'vue'
import { useDealStore} from "../store/dealState";
import useDeals from "../composables/deals";
const { getDealSplits, splits } = useDeals();
const deal = useDealStore();

onMounted(() => {
    console.log(deal.data)
    if(deal.data != null){
        getDealSplits(deal.data.id);
    }
    console.log(splits.value);

})
</script>
<style scoped>
.obligation {
    padding: 24px;
    text-align: center;
    border: 1px dashed;
    background: #F9FAFB;
}

.card-price-span {
    display: flex;
    width: 696px;
    padding: 12px 24px;
    justify-content: space-between;
    align-items: flex-start;
}
</style>

