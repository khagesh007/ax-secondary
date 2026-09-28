<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Approve this deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="approvalForm">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="deal_id" value="{{$deal->id}}">
                            <input type="hidden" name="approval_status" value="Approved">
                            <input type="hidden" name="id" value="" id="parent_approval_id">
                            <!-- put a animated success.json animated image file here -->
                            <div class="alert alert-warning" role="alert">
                               After your approval it will be send to admin for processing.
                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
                            <button type="submit" class="btn btn-success admin-approve-send-btn" data-bs-dismiss="modal" aria-label="Close">
                                Approve
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
        const showApprovalModal = (deal_id,approval_id) => {
            $("#approvalModal").modal("show");
            $("#parent_approval_id").val(approval_id);
        }

        $(document).on('submit', '#approvalForm', function (event) {
            $(".admin-approve-send-btn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{route('update.deal.approval')}}",
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
                        $(".admin-approve-send-btn").prop('disabled', false);
                        $('#reject-form').trigger('reset'); //
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
