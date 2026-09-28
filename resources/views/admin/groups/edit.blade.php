@extends('admin.layouts.master')
@section('title')
    General Settings
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .center-tabs {
            display: flex;
            justify-content: center;
        }
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">General Groups</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                            <li class="breadcrumb-item active"><a
                                    href="{{ route('admin.settings.group.index') }}">Groups</a></li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">



                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-lg-12">

            <div class="card">
                <form action="{{ route('admin.settings.group.update', $group->id) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            <div class="col-6 col-md-6">
                                <label for="">Group Name</label>
                                <input type="text" name="group_name" id="group_name"
                                    class="form-control group_name"placeholder="Group Name" value="{{ $group->name }}">
                            </div>

                            <div class="col-6 col-md-6 mt-2">
                                <label for="">Group Admin </label>

                                <select name="user_id" id="user_id" class="select2 form-control languageSelect2">
                                    @foreach ($users as $user)
                                        <option
                                            value="{{ $user->id }}"{{ $user->group_id == $group->id ? 'selected' : '' }}>
                                            {{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6 col-md-6 mt-2">
                                <label for="">Group Order</label>

                                <input type="number" name="group_order" id="group_order" value="{{ $group->order }}"
                                    class="form-control group_order" placeholder="Group Order " required>
                            </div>
                            <div class="col-6 col-md-6 mt-2">
                                <label for="">Badge</label>
                                <input type="text" name="group_badge" id="group_badge" value="{{ $group->badge }}"
                                    class="form-control group_badge" placeholder="Group Badge" required>
                            </div>
                            <div class="col-6 col-md-6 mt-2">
                                <label for="">Icon</label>
                                <input type="text" name="group_icon" id="" value="{{ $group->icon }}"
                                    class="form-control" placeholder="Group Icon" required>
                            </div>
                            <div class="col-6 col-md-6 mt-2">
                                <label for="">Group Color</label>
                                <input type="color" name="group_color" id="group_color" value="{{ $group->color }}"
                                    class="form-control group_color" placeholder="Group Name" required>
                            </div>
                            <div class="col-6 col-md-6 mt-2">
                                <label for="">Parent Group</label>
                                <select name="parent_id" id="parent_id" class="select2 form-control languageSelect2">
                                    <option value="">Select Parent Group</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}" {{ $group->group_id == $group->id ? 'selected' : '' }}> {{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>





                            <div class="col-6 col-md-6 mt-2">
                                <label for="">Thumbnail</label>
                                <input type="file" name="group_thumbnail" id="group_thumbnail"
                                    class="form-control thumbnail">
                            </div>

                            <div class="col-6 col-md-6 mt-2">
                                <label for="">Image</label>
                                <input type="file" name="group_image" id="group_image" class="form-control image">
                            </div>


                        </div>
                    </div><!-- end card -->
                    <div class="modal-footer">
                        <a href="{{ route('admin.settings.group.index') }}" class="btn btn-primary ">All Groups</a>
                        <button type="submit" class="btn btn-success ">Save</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <div class="row">
        <div id="append"></div>
    </div>


    <!-- Default Modals -->
    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">User Management</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-12 col-md-12">
                            <label for="">Group Name</label>
                            <input type="text" name="group_name" id="group_name" class="form-control group_name"
                                placeholder="Group Name" value="{{ $group->name }}">
                        </div>

                        <div class="col-12 col-md-12 mt-2">
                            <input type="hidden" name="group_id" id="group_id" value="{{ $group->id }}">
                            <label for="">Group Admin</label>

                            <select name="user_id[]" id="user_i" class="select2 form-control languageSelect2"
                                multiple>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}"> {{ $user->full_name }}</option>
                                @endforeach
                            </select>
                        </div>


                    </div>



                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveUser">Save Changes</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('script')
    @include('admin.partials.datatable')


    <script>
        $(document).ready(function() {
            getGroupUsers();

            $('#saveUser').click(function() {
                var group_id = $('#group_id').val();
                var user_id = $('#user_id').val();
                $.ajax({
                    url: "{{ route('admin.settings.group.userGroupsSave') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        group_id: group_id,
                        user_id: user_id,
                    },
                    success: function(response) {
                        getGroupUsers();
                    }
                });
            })


            $('#user_id').change(function() {
                getGroupUsers();
            })


        });

        function getGroupUsers() {
            var user_id = $('#user_id').val();
            $.ajax({
                url: "{{ route('admin.settings.group.getGroupUsers') }}",
                type: "POST",
                data: {
                    user_id: user_id,
                    _token: "{{ csrf_token() }}",
                },
                success: function(response) {
                    $('#append').html(response);

                    $('#UserTable').dataTable()
                }
            });
        }
    </script>
@endsection
