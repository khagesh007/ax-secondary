<div class="modal fade" id="adminApprovalModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Approve and send for approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="sendApprovalList">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="deal_id" value="{{$deal->id}}">
                            <!-- put a animated success.json animated image file here -->
                            <div class="alert alert-warning d-none" role="alert">
                                Below listed users will receive notification for approval, Once everyone approved admin
                                will be notified.
                            </div>
                            <div class="alert alert-warning" role="alert">
                                You will be marking this deal as completed and it will be sent to Account department for further process.
                            </div>


                        </div>
                        <div class="col-12 approval-send-users-list">


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
                            <button type="submit" class="btn btn-success user-approve-send-btn" data-bs-dismiss="modal" aria-label="Close">
                                Approve and send
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="adminApprovalStatusModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Approve and send for approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="adminForceActionForm">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="deal_id" value="{{$deal->id}}">
                            <input type="hidden" name="id" value="{{$admin_deal_approval->id}}">
                            <input type="hidden" name="approval_status" value="Approved">
                            <!-- put a animated success.json animated image file here -->



                        </div>
                        <div class="col-12 approval-send-users-list" >


                        </div>

                        <div class="modal-footer">
                            <div class="alert alert-warning" role="alert">
                                This approval action will mark this deal as <b>Approved</b> and will send the information to account department for further process.
                            </div>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
                            <button type="submit" class="btn btn-success admin-approve-send-btn">
                                Approve and send
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push("page-js")
    <script>
        const showAdminApprovalModal = (deal_id) => {
            $("#adminApprovalModal").modal("show");
            //getDealSplitHierarchy(deal_id);
        }
        const showAdminApprovalStatusModal = (deal_id) => {
            $("#adminApprovalStatusModal").modal("show");
          //  getDealSplitHierarchy(deal_id);
        }
        const getDealSplitHierarchy = (deal_id) => {
            $.ajax({
                url: "{{ route('deal.get.commission.users') }}?deal_id=" + deal_id,
                type: "GET",
                success: function (response) {
                    if (response.status == '1') {
                        let data = response.data;
                        let html = "";
                        let user_list = [];

                        //loop throught data and create html
                       // for (let i = 0; i < data.length; i++) {

                            let commission_users = response.data;
                            for (let j = 0; j < commission_users.length; j++) {
                                let commission_user = commission_users[j];
                                if (user_list.indexOf(commission_user.id) == -1) {
                                    user_list.push(commission_user.id);
                                    let actionBtn = "";
                                    let reject_reason = "";

                                    if(commission_user.deal_approval == null){
                                        actionBtn = `<h6 class="fs-14 mb-1">
                                    <div class="form-check form-switch   form-switch-lg form-switch-success" dir="ltr">
                                        <input type="checkbox" name="user_list[]" value="${commission_user.id}"  data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" aria-label="" class=" form-check-input deactivate-link remove-item-btn" checked="">
                                </div></h6>`;

                                    }else{

                                        if(commission_user.deal_approval.approval_status == 'Pending') {
                                            actionBtn = `<span class="badge bg-info">Pending</span>`;
                                        }else if(commission_user.deal_approval.approval_status == 'Approved') {
                                            actionBtn = `<span class="badge bg-success">Approved</span>`;
                                        }else {
                                            actionBtn = `<span class="badge bg-danger">Rejected</span>`;
                                            reject_reason = `<div class="offset-2 col-10 text-danger fs-11">
                                                            Rejected Reason: ${commission_user.deal_approval?.comment}
                                                            </div>`;
                                        }
                                    }

                                    html += `<div class="row finRow">
                  <div class="d-flex col-6">
                    <div @click="toggleAccordion('collapseExample45')" class="flex-shrink-0">
                      <div class="avatar-sm rounded">
                        <img src="${commission_user.thumb_url}" alt="" class="member-img img-fluid d-block rounded">
                      </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="fs-14 mb-1">${commission_user.full_name}</h6>
                      <p class="text-muted mb-0">${commission_user.position}</p>
                    </div>
                  </div>
                  <div class="col">
                    <div class="flex-shrink-0 text-left ">


                    </div>
                  </div>
                  <div class="col-sm-auto las-col">

                    <div class="flex-shrink-0 text-end">
                        ${actionBtn}

                    </div>
                  </div>
${reject_reason}
                </div>`;
                                } else {
                                    continue;
                                }
                            }

                        $(".approval-send-users-list").html(html);
                    } else {

                    }
                },
                error: function (xhr, status, error) {
                    console.log(xhr);
                }
            });
        }

        $(document).on('submit', '#sendApprovalList', function (event) {
            $(".user-approve-send-btn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{route('create.deal.approval')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $('#adminRejectModal').modal('hide'); //hide form modal
                        $(".user-approve-send-btn").prop('disabled', false);
                        $('#reject-form').trigger('reset'); //
                        location.reload();
                    } else if (result.status == '2') {
                        $(".user-approve-send-btn").prop('disabled', false);
                        $.each(result.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    } else {
                        $(".user-approve-send-btn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $(".user-approve-send-btn").prop('disabled', false);
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });
        $(document).on('submit', '#adminForceActionForm', function (event) {
            $(".admin-approve-send-btn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{route('update.deal.approval.admin')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $('#adminApprovalStatusModal').modal('hide'); //hide form modal
                        $(".admin-approve-send-btn").prop('disabled', false);
                        location.reload();
                    } else if (result.status == '2') {
                        $(".admin-approve-send-btn").prop('disabled', false);
                        $.each(result.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    } else {
                        $(".admin-approve-send-btn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $(".admin-approve-send-btn").prop('disabled', false);
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });

    </script>
@endpush
