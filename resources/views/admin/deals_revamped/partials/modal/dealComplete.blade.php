<div class="modal fade" id="dealCompleteModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmModalLabel">Complete this deal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="completedForm">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="deal_id" value="{{$deal->id}}">
                            <input type="hidden" name="status" value="COMPLETED">

                            <!-- put a animated success.json animated image file here -->
                            <div class="alert alert-danger" role="alert">
                                <h4 class="alert-heading">Are you sure?</h4>
                                <p>Do you really want to mark this deal as <b>Completed</b> ? This process cannot be undone.</p>

                            </div>


                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal" aria-label="Close">
                                Close
                            </button>
                            <button type="submit" class="btn btn-success admin-approve-send-btn" data-bs-dismiss="modal" aria-label="Close">
                                Complete Deal
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
        const showAdminCompleteModal = (deal_id,approval_id) => {
            $("#dealCompleteModal").modal("show");
        }

        $(document).on('submit', '#completedForm', function (event) {
            $(".admin-approve-send-btn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{route('updateDealStatus')}}",
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
