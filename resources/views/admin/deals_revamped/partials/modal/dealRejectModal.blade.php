<div class="modal fade" id="adminRejectModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Reject Deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reject-form">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="deal_id" value="{{$deal->id}}">
                            <input type="hidden" name="approval_id" value="{{$deal_approval->id}}">
                            <input type="hidden" name="approval_status" value="Rejected">
                            <!-- put a animated success.json animated image file here -->
                            <div class="alert alert-warning" role="alert">
                                Agent will be notified about the rejection. Where they can make changes and resubmit for approval.
                            </div>


                        </div>
                        <div class="col-12">
                            <label for="exampleFormControlTextarea1" class="form-label">Reason for rejection</label>
                            <textarea class="form-control" name="comment" id="exampleFormControlTextarea1" rows="3" required></textarea>


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">Close</button>
                            <button type="submit" class="btn btn-danger reject-deal-btn"  >Reject</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push("page-js")
    <script>

        $(document).on('submit', '#reject-form', function (event) {
            console.log("reject form submitted")

            $(".reject-deal-btn").prop('disabled', true);
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
                        $(".reject-deal-btn").prop('disabled', false);
                        $('#reject-form').trigger('reset'); //
                        location.reload();
                    } else if (result.status == '2') {
                        $(".reject-deal-btn").prop('disabled', false);
                        $.each(result.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    } else {
                        $(".reject-deal-btn").prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    $(".reject-deal-btn").prop('disabled', false);
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
