@extends('admin.layouts.master')
@section('title')
    Devices
@endsection
@section('style')
    <style type="text/css">
        div.dataTables_wrapper div.dataTables_filter input {
            border-color: var(--vz-input-border);
            background-color: var(--vz-input-bg);
            color: var(--vz-body-color);
            line-height: 1.5;
            padding: 0.5rem 0.9rem 0.5rem 1.025rem;
            border-radius: 0.25rem;
            font-size: .8125rem;
        }
    </style>
    {{-- <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script> --}}
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0">Configuration</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Devices</a></li>
                            <li class="breadcrumb-item active">List</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- @includeIf('admin.device.includes._filterform') --}}


    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-4">
                        <table id="deviceDatatable" class="table align-middle" style="width:100%">

                            <thead class="">
                                <tr class="text-uppercase table-light2">

                                    <th>ID</th>
                                    <th>Agent</th>
                                    <th>Parent</th>
                                    <th>Device Name</th>
                                    <th>Device OS</th>
                                    <th>Device IP</th>
                                    <th>Last Login</th>
                                    <th>Coordinates</th>
                                    <th>Traccar Device ID</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="form-check-all">
                                @foreach ($devices as $device)
                                    <tr>
                                        <td><span class="mx-2">{{ $device->id }}</span></td>
                                        <td><span class="mx-2">[{{ $device->agent->id }}]
                                                {{ $device->agent->full_name }}</span></td>
                                                <td><span class="mx-2">[{{ $device?->agent?->parent?->id }}] {{ $device?->agent?->parent?->full_name }}</span></td>

                                        <td><span class="mx-2">{{ $device->device_name }}</span></td>
                                        <td><span class="mx-2">{{ $device->device_os }}</span></td>
                                        <td><span class="mx-2">{{ $device->login_ip }}</span></td>
                                        <td><span
                                                class="mx-2">{{ \Carbon\Carbon::parse($device->last_active_at)->diffForHumans() ?? '' }}</span>
                                        </td>
                                        <td><span class="mx-2">{{ $device->coordinates }}</span></td>
                                        <td><span class="mx-2">{{ $device->traccar_device_id }}</span></td>
                                        <td><span class="mx-2"><i class="mdi-delete-outline mdi text-danger deleteDevice"
                                                    data-id="{{ $device->id }}"></i></span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0">Mismatched Data</h4>

                    </div>


                </div>
            </div>
        </div>
    </div>

    <div class="row mt-2">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive mb-4">
                        <table id="deviceDatatablemissmatched" class="table align-middle" style="width:100%">

                            <thead class="">
                                <tr class="text-uppercase table-light2">

                                    <th>ID</th>
                                    <th>Agent</th>
                                    <th>Parent</th>
                                    <th>Device Name</th>
                                    <th>Device OS</th>
                                    <th>Device IP</th>
                                    <th>Last Login</th>
                                    <th>Coordinates</th>
                                    <th>Traccar Device ID</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="form-check-all">
                                @foreach ($missmatched as $device)
                                    <tr>
                                        <td><span class="mx-2">{{ $device->id }}</span></td>
                                        <td><span class="mx-2">[{{ $device->agent->id }}] {{ $device->agent->full_name }}</span></td>
                                        <td><span class="mx-2">[{{ $device?->agent?->parent?->id }}] {{ $device?->agent?->parent?->full_name }}</span></td>
                                        <td><span class="mx-2">{{ $device->device_name }}</span></td>
                                        <td><span class="mx-2">{{ $device->device_os }}</span></td>
                                        <td><span class="mx-2">{{ $device->login_ip }}</span></td>
                                        <td><span
                                                class="mx-2">{{ \Carbon\Carbon::parse($device->last_active_at)->diffForHumans() ?? '' }}</span>
                                        </td>
                                        <td><span class="mx-2">{{ $device->coordinates }}</span></td>
                                        <td><span class="mx-2">{{ $device->traccar_device_id }}</span></td>
                                        <td><span class="mx-2"><i class="mdi-delete-outline mdi text-danger deleteDevice"
                                                    data-id="{{ $device->id }}"></i></span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('script')
    @include('admin.partials.datatable')
    <script>
        $(document).ready(function() {
            let dataTable = $('#deviceDatatable').dataTable();
            // sort by id desc
            dataTable.fnSort([
                [0, 'desc']
            ]);
            let dataTable2 = $('#deviceDatatablemissmatched').dataTable();
            // sort by id desc
            dataTable2.fnSort([
                [0, 'desc']
            ]);
        })


        // send ajax request to delete device on click of .deleteDevice aske confirmation first
        $(document).on('click', '.deleteDevice', function() {
            let id = $(this).data('id');
            let url = "{{ route('admin.configuration.devices.delete') }}";
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to delete this device!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#2F8BE6',
                cancelButtonColor: '#F55252',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            id: id,
                            _token: "{{ csrf_token() }}",
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                $(`.deleteDevice[data-id=${id}]`).closest('tr').remove();
                                showToastify(response.message, "success")


                            }
                        }
                    });
                }
            })
        })








        $("#sidebarConfiguration").addClass('collapse show');
        $("#userDevicesList").addClass('active');



        // function toggleAdvanceSearch() {
        //     $("#collapseFilterForm").toggleClass("show")
        // }
    </script>
@endsection
