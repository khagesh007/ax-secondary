<!DOCTYPE html>
<html lang="en">

<head>
    <title>Meta Datas</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}" />

</head>

<body>

    <div class="container mt-3">
        <h2>Meta Data</h2>
        <div class="row">
            <div class="col-12">
                <button id="deleteSelected" class="btn btn-danger">Delete Selected</button>

            </div>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Agent</th>
                    <th>Message</th>
                    <th>mobile</th>
                    <th>is Archive</th>

                    <th>Status</th>

                    <th>Archive</th>
                    <th>Delete
                        <input type="checkbox" id="checkAll" class="form-check-input">
                    </th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($leads as $meta)
                    <tr>
                        <td>{{ $meta->id }}</td>
                        <td>{{ $meta->agent_id }} ({{ $meta->agent?->full_name ?? '-' }})</td>
                        <td>{{ $meta->message }}</td>
                        <td>{{ $meta->mobile }}</td>
                        <td>{{ $meta->is_archive }}</td>

                        <td>{{ $meta->leadStatus->name }}</td>
                        <td><input data-id="{{ $meta->id }}" data-do="1" type="checkbox" data-key="is_archive"
                                name="is_archive" {{ $meta->is_archive == 1 ? 'checked' : '' }}
                                class="form-check-input changeStatus"></td>

                        <td><input data-id="{{ $meta->id }}" data-do="1" type="checkbox" data-key="delete"
                                    name="delete"
                                    class="form-check-input deleteLead">
                                </td>
                        <td>{{ date('Y M d, h:m', strtotime($meta->updated_at)) }}</td>
                    </tr>
                @endforeach


            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('.changeStatus').change(function() {
                let id = $(this).data('id');
                let action = $(this).data('do');
                let key = $(this).data('key');

                if ($(this).is(':checked')) {
                    action = 1;
                } else {
                    action = 0;
                }

                let formData = {
                    id: id,
                    action: action,
                    key: key
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    type: "POST",
                    url: "{{ route('admin.work.meta.updateLeadMessage') }}",
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '200') {
                            showToastify("Type Status updated successfully", "success")

                        }
                    },
                    error: function(data) {
                        console.log('Error:', data);
                    }
                });
            })

        // Check All functionality
        $('#checkAll').on('change', function() {
            $('.deleteLead').prop('checked', this.checked);
        });

        // Delete selected items
        $('#deleteSelected').on('click', function() {
            // Get all checked checkboxes
            const selectedIds = $('.deleteLead:checked').map(function() {
                return $(this).data('id');
            }).get();

            if (selectedIds.length === 0) {
                alert('No items selected.');
                return;
            }

            // Confirm deletion
            if (!confirm('Are you sure you want to delete selected items?')) {
                return;
            }

            // Perform AJAX request to delete items
            $.ajax({
                headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                url: "{{ route('admin.work.meta.deleteLeadMessage') }}", // Replace with your server endpoint
                type: 'POST',
                data: {
                    ids: selectedIds
                },
                success: function(response) {
                    alert('Selected items deleted successfully.');
                    location.reload(); // Reload the page to reflect changes
                },
                error: function(error) {
                    alert('An error occurred while deleting items.');
                    console.error(error);
                }
            });
        });
    });
</script>
</body>

</html>
