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

        /* .selectionTabs .active {
                                                        background: linear-gradient( 90deg, rgb(211, 161, 136) 25%, rgb(255, 255, 255) 35%, rgb(211, 161, 136) 40%, rgb(211, 161, 136) 60%, rgb(255, 255, 255) 75%, rgb(211, 161, 136) 93% ) !important;
                                                        color: #5e5e5e !important;
                                                    } */
    </style>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">General Settings</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                            <li class="breadcrumb-item active">General Settings</li>
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
                <div class="card-body">
                    <div class="row">
                        <div class="col-xxl-12">


                            <div class="row">
                                <div class="col-md-2">
                                    <div class="nav flex-column nav-pills text-center" id="v-pills-tab" role="tablist"
                                        aria-orientation="vertical">
                                        @foreach (App\Models\GeneralSetting::MODULES as $key => $value)
                                            <a class="nav-link mb-2 {{ $loop->first ? 'active' : '' }}"
                                                onclick="getSettings(`{{ $value }}`)" id="v_{{ $key }}"
                                                data-bs-toggle="pill" href="#vv_{{ $key }}" role="tab"
                                                aria-controls="vv_{{ $key }}"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}">{{ $value }}</a>
                                        @endforeach
                                    </div>
                                </div><!-- end col -->

                                <div class="col-md-10">
                                    <div class="tab-content text-muted mt-4 mt-md-0" id="v-pills-tabContent">

                                    </div>

                                </div><!--  end col -->

                            </div><!--end col-->

                        </div>

                    </div><!-- end card-body -->
                </div><!-- end card -->

            </div>
        </div>
    @endsection
    @section('script')
        <script>
            $("#sidebarSettings").addClass('collapse show');
            $("#sidebarGeneralSetting").addClass('active');
            $("#portalSettingsActive").addClass('active');
            getSettings('Property');
            function getSettings(module) {
                $.ajax({
                    url: "{{ route('admin.settings.general.getSettings') }}",
                    type: "POST",
                    dataType: 'html',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        module: module
                    },
                    success: function(response) {
                        if (response) {
                            $('#v-pills-tabContent').html(response);
                        } else {
                            console.log(response)
                        }
                    }
                })
            }


            $(document).on('click', ".submit_form", function() {
                var getInfo = $(this).closest('.getInfo');
                var key = getInfo.find('.key').val();
                var value = getInfo.find('.value').val();
                var slug = getInfo.find('.slug').val();
                var module = getInfo.find('.module').val();
                // send ajax request
                $.ajax({
                    url: "{{ route('admin.settings.general.store') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        key: key,
                        value: value,
                        slug: slug,
                        module: module
                    },
                    success: function(response) {
                        getSettings(module);
                    }
                })

            });
            $(document).on('click', ".delete_form", function() {
                var id = $(this).data('id');
                var getInfo = $(this).closest('.getInfo');
                var module = getInfo.find('.module').val();
                $.ajax({
                    url: "{{ route('admin.settings.general.delete') }}",
                    type: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        id: id
                    },
                    success: function(response) {
                       if(response.status == '1'){
                           getSettings(response.module);
                       }else{

                       }
                    }
                })

            });
        </script>
    @endsection
