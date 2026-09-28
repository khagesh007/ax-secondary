@extends('admin.layouts.master')
@section('title')
    General Groups
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
                            <li class="breadcrumb-item active">Groups</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <button type="button" class="btn btn-primary" id="reset-groups">Reset Groups</button>
                        <button type="button" class="btn btn-primary " data-bs-toggle="modal" data-bs-target="#myModal">Add
                            new</button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row d-none">
        <div class="col-lg-12">

            <div class="card">
                <div class="card-body">
                    <table id="sourceDatatable" class="table align-middle" style="width-100%">
                        <thead class="">
                        <tr class="text-uppercase table-light2">
                            <th scope="col" style="width-50px;"> ID </th>
                            <th>Name</th>
                            <th>Group Admin</th>
                            <th>Parent Group</th>
                            <th>Count</th>

                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody class="form-check-all">
                        @foreach ($data_filtered as $i)
                            <tr>
                                <td>{{ $i->id }}</td>
                                <td>{{ $i->name }}</td>
                                <td>
                                    <div class="d-flex align-items-center userDetails"
                                         data-agent-id="{{ $i->user->id }}" title="{{ $i->user->first_name }}">
                                        <div class="flex-shrink-0 me-2">
                                            <img src="{{ $i->user->thumb_url }}" alt=""
                                                 class="avatar-xs rounded-circle">
                                        </div>
                                        <div class="flex-grow-1">{{ $i->user->first_name }}<p title="Created at"
                                                                                              class="mb-0"></p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $i->parent ? $i->parent->name : '--' }}</td>
                                <td>{{ $i->child_count ? $i->child_count : '--' }}</td>
                                <td>Edit</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div><!-- end card -->

            </div>
        </div>
    </div>

    @if (count($errors) > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            @foreach ($errors->all() as $error)
                <strong>Oh! Shit, </strong>
                {{ $error }}
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>
    @endif

    @foreach ($data_filtered as $group)
        <div class="row">
            <div class="col-xl-5">
                <div class="card" style="border:2px solid {{$group->color}}">
                    <div class="card-header">
                        <h6 class="card-title mb-0"><a
                                href="{{ route('admin.settings.group.edit', $group->id) }}">{{ $group->name }} ({{$group->child_count ? $group->child_count : '--'}})</a></h6>

                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="mx-auto avatar-md mb-3">
                            <img src="{{ $group->user->thumb_url }}" alt="" class="img-fluid rounded-circle">
                        </div>
                        <h5 class="card-title mb-1">{{ $group->user->first_name }}</h5>
                        <p class="text-muted mb-0">{{ countDirectChildren($group->user->id) }}</p>
                    </div>
                    {{-- <div class="card-footer text-center d-none">
                        <ul class="list-inline mb-0">
                            @foreach ($group->children as $child)
                                <li class="list-inline-item">
                                    <a href="javascript:void(0);" class="lh-1 align-middle link-secondary"><i
                                            class="ri-facebook-fill"></i> {{ $child->name }}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div> --}}
                </div>
            </div>
            @if ($group->children->count() > 0)


                <div class="col-xl-7">
                    <div class="row">
                        @foreach ($group->children as $child)
                            <div class="col-xl-4 delete-group-{{ $child->id }}">
                                <div class="card" style="border:2px dotted {{$group->color}}">
                                    <div class="card-header">
                                        <button type="button" data-id="{{ $child->id }}" id="delete-group-{{ $child->id }}"
                                                class="btn-close float-end fs-11 delete-group"></button>
                                        <h6 class="card-title mb-0"><a
                                                href="{{ route('admin.settings.group.edit', $child->id) }}">{{ $child->name }}</a>
                                        </h6>
                                    </div>
                                    <div class="card-body p-4 text-center">
                                        <div class="mx-auto avatar-md mb-3">
                                            <img src="{{ $child?->user?->thumb_url }}" alt=""
                                                 class="img-fluid rounded-circle">
                                        </div>
                                        <h5 class="card-title mb-1">{{ $child?->user?->first_name }}</h5>
                                        <p class="text-muted mb-0">{{ countDirectChildren($child?->user?->id) }}</p>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        <hr>
    @endforeach




    <!-- Default Modals -->
    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
         style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Create Group</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <form action="{{ route('admin.settings.group.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <label for="">Group Name</label>
                                <input type="text" name="group_name" id="group_name" class="form-control group_name"
                                       placeholder="Group Name" required>
                            </div>

                            <div class="col-12 col-md-12 mt-2">
                                <label for="">Group Admin</label>

                                <select name="user_id" id="" required class="select2 form-control languageSelect2">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"> {{ $user->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-12 mt-2">
                                <label for="">Parent Group</label>
                                <select name="parent_id" id="parent_id" class="select2 form-control languageSelect2">
                                    <option value="">Select Parent Group</option>
                                    @foreach ($groups as $group)
                                        <option value="{{ $group->id }}"> {{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-12 mt-2">
                                <label for="">Group Color</label>
                                <input type="color" name="group_color" id="group_color" class="form-control group_color"
                                       placeholder="Group Name" required>
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary ">Save</button>
                    </div>
                </form>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('script')
    @include('admin.partials.datatable')

    <script>
        let isUpdate = false;

        $(document).ready(function() {
            $('.select2').select2();
        });


        $(document).on('click', '.delete-group', function() {
            let id = $(this).data('id');
            Swal.fire({
                html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to delete this group?</p></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                confirmButtonText: "Yes, I am sure.",
                cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                buttonsStyling: !1,
                showCloseButton: !0
            })
                .then(function(t) {
                    if (t.isConfirmed) {
                        $.ajax({
                            url: "{{route('admin.settings.group.deleteGroup')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                id:id
                            },
                            success: function(result) {
                                if (result.status == '1') {
                                    $('.delete-group-' + id).remove();
                                    showToastify(result.message, "success")
                                } else {
                                    showToastify(result.message, "error")
                                }
                            },
                            error: function(result) {
                                console.table(result);
                            }
                        });
                    }
                })
        })


        $(document).on('click', '#reset-groups', function() {
            let id = $(this).data('id');
            Swal.fire({
                html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you sure?</h4><p class="text-muted mx-4 mb-0">Are you sure you want to delete all groups?</p></div></div>',
                showCancelButton: !0,
                confirmButtonClass: "btn btn-success w-xs me-2 mb-1",
                confirmButtonText: "Yes, I am sure.",
                cancelButtonClass: "btn btn-soft-danger w-xs mb-1",
                buttonsStyling: !1,
                showCloseButton: !0
            })
                .then(function(t) {
                    if (t.isConfirmed) {
                        $.ajax({
                            url: "{{route('admin.settings.group.deleteGroup.all')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                flag:'resetGroups'
                            },
                            success: function(result) {
                                alert(result.message)
                                location.reload();
                            },
                            error: function(result) {
                                console.table(result);
                            }
                        });
                    }
                })
        })



        $(document).on('click', '.btn-edit', function() {
            let id = $(this).data('id');
            let url = "{{ route('admin.settings.group.edit', ':id') }}";
            url = url.replace(':id', id);
            // Redirect to the generated URL
            window.location.href = url;
        });

        function generateRandomRGBColor() {
            const r = Math.floor(Math.random() * 256); // Generate a random value between 0 and 255 for red
            const g = Math.floor(Math.random() * 256); // Generate a random value between 0 and 255 for green
            const b = Math.floor(Math.random() * 256); // Generate a random value between 0 and 255 for blue

            // Create the RGB color string
            const rgbColor = `rgb(${r},${g},${b})`;

            return rgbColor;
        }
    </script>
@endsection
