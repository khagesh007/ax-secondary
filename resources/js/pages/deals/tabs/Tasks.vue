<template>
    <div class="tab-pane" id="nav-light-profile" role="tabpanel">

        <div class="row mb-2">
            <div class="col-9"><h4>Upcoming Task</h4></div>
            <div class="col-3"><button class="btn btn-primary action-add float-end" @click="showTaskModal()">Create Task</button> </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1 py-1">Task History</h4>
                        <div class="flex-shrink-0">
                            <div class="dropdown card-header-dropdown">
                                <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="text-muted">All Tasks <i class="mdi mdi-chevron-down ms-1"></i></span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" style="">
                                    <a class="dropdown-item" href="#">All Tasks</a>
                                    <a class="dropdown-item" href="#">Completed </a>
                                    <a class="dropdown-item" href="#">Inprogress</a>
                                    <a class="dropdown-item" href="#">Pending</a>
                                </div>
                            </div>
                        </div>
                    </div><!-- end card header -->
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-borderless table-nowrap table-centered align-middle mb-0">
                                <thead class="table-light text-muted">
                                <tr>
                                    <th scope="col">Name</th>
                                    <th scope="col">Dedline</th>
                                    <th scope="col">Status</th>
                                    <th scope="col">Assignee</th>
                                </tr>
                                </thead><!-- end thead -->
                                <tbody>
                                <tr v-for="(task, index) in dealTasks">
                                    <td>
                                        <div class="form-check">
                                            <input class="form-check-input fs-15" type="checkbox" value="" id="checkTask1">
                                            <label class="form-check-label ms-1" for="checkTask1">
                                               {{task.subject}}
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-muted">{{task.due_date}}</td>
                                    <td><span class="badge badge-soft-success">{{task.status}}</span></td>
                                    <td>
                                        <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" :title="task?.user?.full_name" :data-bs-original-title="task?.user?.full_name">
                                            <img :src="task?.user?.image_url" alt="" class="rounded-circle avatar-xxs">
                                        </a>
                                    </td>
                                </tr><!-- end -->

                                </tbody><!-- end tbody -->
                            </table><!-- end table -->
                        </div>
                        <div class="mt-3 text-center">
                            <a href="javascript:void(0);" class="text-muted text-decoration-underline">Load More</a>
                        </div>
                    </div><!-- end cardbody -->
                </div>
            </div>
        </div>
        <TaskModel></TaskModel>
    </div>

</template>
<script setup>
import useDeals from "../composables/deals";
import TaskModel from "../includes/TaskModel.vue";
import {inject, onMounted, ref, watchEffect} from "vue";
const { getDealTasks, dealTasks} = useDeals();
const deals = inject('deal');

const showTaskModal = () => {
    $('#taskModal').modal('show');
}
watchEffect(() => {

})
onMounted(() => {
    getDealTasks(deals.id)
})
</script>
