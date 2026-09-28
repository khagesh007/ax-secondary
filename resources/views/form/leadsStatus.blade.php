@extends('layouts.master-without-nav')
@section('title')
    Lead Form
@endsection
@section('meta_description', 'Lead Generation Form')
@section('css')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
        type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
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

    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/intl-tel-input/css/intlTelInput.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
        type="text/css" />
@endsection

@section('content')
    @include('admin.partials.loader')

    <nav class="navbar-menu d-none"></nav>
    <div class="auth-page-wrapper pt-2">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                    viewBox="0 0 1440 120">
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
                </svg>
            </div>
        </div>

        <!-- auth page content -->
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-2">
                            <img src="{{ asset('assets/images/logo/ax-short-logo.png') }}" alt="">
                        </div>

                    </div>
                </div>
                <!-- end row -->

                <div class="row  justify-content-center formDiv">
                    <div class="col-md-8">
                        <div class="card mt-4">

                            <div class="card-body p-4">
                                <div class="text-center mt-2">

                                </div>
                                <div class="p-2 mt-2 " id="formdiv">
                                    <form class="needs-validation" novalidate id="mainForm">

                                        <input type="hidden" name="lead" id="lead"
                                            value="{{ app()->request->input('lead') }}">
                                        <input type="hidden" name="user" id="user"
                                            value="{{ app()->request->input('user') }}">

                                        <input type="hidden" id="lead_status_id" name="lead_status_id" value="">
                                        <input type="hidden" id="lead_sub_status_id" name="lead_sub_status_id"
                                            value="">


                                        <div class="row">
                                            <div class="col-md-12 text-center text-primary">
                                                <h2>Update Your Lead</h2>
                                            <hr>
                                                <small>ID:{{$lead->id}}</small>
                                            </div>
                                        </div>












                                        <div class="row ">

                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">First Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control inputs updateItem"
                                                    data-name="first_name" value="{{ $lead->first_name=='New'?'':$lead->first_name }}" id="first_name"
                                                    name="first_name">
                                                <div class="invalid-feedback">
                                                    Please enter First Name.
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Last Name</label>
                                                <input type="text" class="form-control inputs updateItem"
                                                    data-name="last_name" value="{{ $lead->last_name }}" id="last_name"
                                                    name="last_name">
                                                <div class="invalid-feedback">
                                                    Please enter Last Name.
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Source <span class="text-danger">*</span></label>
                                                <select name="source_id" id="source_id"  {{ isset($lead->source_id) ? 'disabled' : '' }}
                                                    class="form-control inputs updateItem" data-name="source_id"
                                                    data-calc_value="0">
                                                    <option value="">- Select -</option>
                                                    @foreach ($sources as $source)

                                                        <option value="{{ $source->id }}" {{ $lead->source_id == $source->id ? 'selected' : '' }}> {{ $source->name }}</option>
                                                    @endforeach
                                                </select>

                                                <div class="invalid-feedback">
                                                    Please enter a Source.
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Medium <span class="text-danger">*</span></label>
                                                <select name="medium_id" id="medium_id"
                                                    class="form-control inputs updateItem" data-name="lead_medium_id"
                                                    data-calc_value="0">
                                                    <option value="">- Select -</option>
                                                    @foreach ($mediums as $medium)
                                                        <option value="{{ $medium->id }}"
                                                            {{ $lead->lead_medium_id == $medium->id ? 'selected' : '' }}>
                                                            {{ $medium->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please enter a Medium.
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Mobile</label>
                                                <input type="text" class="form-control inputs updateItem"
                                                    data-name="mobile" value="{{ $lead->mobile }}" id="mobile"
                                                    name="mobile">
                                                    <div class="text-danger mobileValidation">
                                                    </div>

                                                <div class="invalid-feedback">
                                                    Please enter a Mobile.
                                                </div>
                                            </div>

                                            <div class="col-md-6 mt-3">
                                                <label for="validationCustom02" class="form-label">Property <span class="text-danger">*</span></label>
                                                <select name="property_id" id="property_id" {{$lead->property_id?'disabled':''}}
                                                    class="form-control inputs updateItem" data-name="property_id"
                                                    data-calc_value="0">
                                                    <option value="">- Select -</option>
                                                    @foreach ($properties as $pro)
                                                        <option value="{{ $pro->id }}" data-beds="{{$pro->bed?->name}}"
                                                            data-first_image="{{$pro->first_image &&  $pro->first_image->path ? $pro->first_image->path : ''  }}"
                                                            data-ref_no="{{$pro->ref_no}}"
                                                            data-unit_no="{{$pro->unit_no}}"
                                                            data-type="{{$pro->property_category?->title || ''}}"
                                                            data-location="{{$pro->location}}"
                                                            {{ $lead->property_id == $pro->id ? 'selected' : '' }}>
                                                            {{ $pro->unit_no }} {{$pro->ref_no}}  {{$pro->location}} {{$pro->property_category?->title || ''}} {{$pro->bed?->name}}</option>
                                                    @endforeach
                                                </select>
                                                <div class="invalid-feedback">
                                                    Please enter a Property .
                                                </div>
                                            </div>

                                            <div class="col-md-12 mt-3">
                                                <label for="validationCustom02" class="form-label">Note</label>
                                                <textarea type="text" name="note" id="note" data-name="note" class="form-control inputs">{{ $lead->note}}</textarea>


                                                <div class="invalid-feedback" id="note-invalid-feedback">
                                                    Please enter a Note.
                                                </div>
                                            </div>

                                        </div>


                                        <div class="row mt-4">
                                            <div class="card ribbon-box border shadow-none mb-lg-0 leads"
                                                style="padding:0px!important;">
                                                <div class="card-header  OpenStatus StatusField">
                                                    <h6 class="card-title mb-0">Status</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        {{-- <div class="col-md-2 col-sm-12 mb-4" style="position: unset!important">
                                                            <div class="OpenStatus StatusField">Open
                                                            </div>
                                                            <hr>

                                                        </div> --}}
                                                        <div class="col-md-12 col-sm-12">
                                                            <div class="ribbon-content  text-muted text-end">
                                                                <ul class="nav nav-pills arrow-navtabs nav-success badge-nav"
                                                                    role="tablist">

                                                                    @foreach ($open_sub_statuses ?? [] as $sub_status)
                                                                        <li class="nav-item checkClass"
                                                                            role="presentation">
                                                                            <a class="nav-link rmActive open_sub_status"
                                                                                data-value="{{ $sub_status->name }}"
                                                                                data-sub_id="{{ $sub_status->id }}"
                                                                                data-status_id="{{ $sub_status->id }}"
                                                                                data-badge="{{ $sub_status->badge ?? 'badge-soft-info' }}"
                                                                                data-bs-toggle="tab" href="#arrow-profile"
                                                                                role="tab" aria-selected="false"
                                                                                tabindex="-1">
                                                                                <span
                                                                                    class="rmDark {{ $sub_status->badge }} badge  sub_{{ $sub_status->id }} randomClass font-14"><i
                                                                                        class="{{ $sub_status->icon }} me-2"></i>
                                                                                    {{ $sub_status->name }}</span>
                                                                            </a>
                                                                        </li>
                                                                    @endforeach

                                                                </ul>
                                                            </div>
                                                        </div>


                                                    </div>
                                                    {{-- <h5 class="fs-14 text-end">Open</h5> --}}


                                                </div>
                                            </div>


                                        </div>

                                        <div class="row mt-3 text-end">


                                            <hr>
                                            <div class="col-12">
                                                <button type="button" class="btn btn-primary action-archive"
                                                    id="archiveBtn"><i class="mdi mdi-delete-outline"></i>Archive
                                                    Lead</button>
                                            </div>



                                        </div>


                                    </form>



                                </div>

                                <div class="p-2 text-center d-none" id="leadNotFound">

                                        <h2>
                                            404 - Data Not Found
                                        </h2>

                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->



                    </div>
                </div>

                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>
                                    document.write(new Date().getFullYear())
                                </script> {{ getApp('name') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>




    <div class="modal zoomIn" id="archiveModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog  modal-md modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-soft-danger">
                    <h5 class="modal-title" id="archiveModalLabel">Archive Lead</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="close-modal"></button>
                </div>
                <div class="modal-body px-5   py-3">
                    <div class="row">
                        <div class="col-3">Reason*:</div>
                        <div class="col-9">
                            <input type="hidden" id="archive_lead_id">


                            <select name="" id="preFillOption" class="form-control">
                                <option value=""></option>
                                <option value="Internal Agent">Internal Agent</option>
                                <option value="External Agent">External Agent</option>
                                <option value="Family">Family</option>
                                <option value="Friend">Friend</option>
                                <option value="Promoter">Promoter</option>
                                <option value="Other">Other</option>
                            </select>

                            <textarea name="" rows="3" class="form-control d-none mt-3" placeholder="Please add a reason for Archive"
                                id="archive_note" required></textarea>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success action-archive" id="saveArchive">
                        Archive Lead
                    </button>
                    <button type="button" class="btn btn-light  action-close" data-bs-dismiss="modal">
                        Close
                    </button>

                </div>
            </div>
        </div>
    </div>

@endsection
@section('script')
    <script src="{{ URL::asset('assets/libs/particles.js/particles.js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/particles.app.js') }}"></script>
    <script src="{{ asset('assets/libs/intl-tel-input/js/intlTelInput.min.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $('#source_id').select2({
            placeholder: 'Select Source',
            allowClear: true
        });
        $('#medium_id').select2({
            placeholder: 'Select Medium',
            allowClear: true
        });
        $('#property_id').select2({
            placeholder: 'Select listing',
            allowClear: true,
            templateResult: formatProperty,
        });


        $('#status_id').select2({
            placeholder: 'Select Source',
            allowClear: true
        });
        $('#preFillOption').select2({
            placeholder: 'Select Archive Reason',
            allowClear: true
        });

        function formatProperty(e) {
            let f_unit_no = $(e.element).data('unit_no');
            let f_ref_no = $(e.element).data('ref_no');
            let f_location = $(e.element).data('location');
            let f_type = $(e.element).data('type');
            let f_beds = $(e.element).data('beds');
            let f_first_image = $(e.element).data('first_image') ? $(e.element).data('first_image') :
                'https://ui-avatars.com/api/?name=' + f_unit_no + '&color=7F9CF5&background=random';


            return e.id ? $('<span><img src="' + f_first_image + '" class="img-flag rounded-5" width="18" /> ' + f_ref_no +
                ' - ' + f_type + ' - ' + f_unit_no +
                "</span> <br> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-bed-double'></i>" + f_beds +
                "</span> <span class='small ml-5 d-inline-block'> <i class='mdi mdi-google-maps'></i>" + f_location +
                "</span> ") : e.text;


        }

        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })

        let mobileInput = initPhoneValidator()
        let phoneInput = initPhoneValidator("mobile", "full_mobile")

        $(document).on('submit', '#mainForm', function(event) {
            $("#saveBtn").prop('disabled', true);
            $('#screen_loader').removeClass('d-none');
            event.preventDefault();

            function save() {
                var formData = new FormData($("#mainForm")[0]);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: "{{ route('api.thirdparty.submit_lead') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    cache: false,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        $('#screen_loader').addClass('d-none');
                        if (result.status == '1') {
                            showToastify(result.msg, "success")
                            // $('.formDiv').addClass('d-none');
                            swal("Success!", result.msg, "success");

                            $("#saveBtn").prop('disabled', false); //enable submit button
                            $('#mainForm').trigger('reset'); //
                            $(".inputs").val("").trigger("change")
                            $(".inputs").removeClass("is-invalid")
                            $("select.inputs").val("").trigger("change")
                            viewData(result.id)
                            dataTable.row().order([10, 'DESC']).draw();
                            isUpdate =
                                true; // set isUpdate to false so that when we try to add new category it will be create request
                        } else if (result.status == '2') {
                            console.table(result);
                            $("#saveBtn").prop('disabled', false);
                            let error_message = '';

                            $.each(result.errors, function(key, value) {
                                error_message += '' + key + ': ' + value + ' | ';
                            });
                            showToastify(error_message, "error")
                        } else {
                            $("#saveBtn").prop('disabled', false);
                            swal("Oops!", result.msg, "error");
                            showToastify(result.msg, "error")
                        }

                    },
                    error: function(jqXhr, json, errorThrown) {
                        $("#saveBtn").prop('disabled', false);
                        let data = jqXhr.responseJSON;
                        console.table(data);
                        if (data.errors) {
                            $.each(data.errors, function(index, item) {
                                showToastify(item[0], "error")
                            })
                        }
                        if (jqXhr.status == 500 || jqXhr.status == 400) {
                            showToastify(data.message, "error")
                        }
                        $('#screen_loader').addClass('d-none');

                    },
                });
            }
        });

        $('.badge-nav .nav-link').on('click', function(e) {
            e.preventDefault();
            $('.rmActive').removeClass('active');
            $(this).parent().removeClass('active');
        });

        $('#archiveBtn').click(function() {
            $('#archiveModal').modal('show');
        });
        $(document).ready(function() {
            $('#preFillOption').change(function() {
                // get selected value of option
                var option = $(this).find('option:selected').val();
                if (option == 'Other') {
                    $('#archive_note').val('')
                    $('#archive_note').removeClass('d-none');
                    $('#archive_note').focus();
                } else {
                    $('#archive_note').val(option)
                    $('#archive_note').addClass('d-none');
                }
            });
        })



        $(document).on("click", "#saveArchive", function() {
            // $('#screen_loader').removeClass('d-none');
            let lead_id = $('#lead').val();
            let user_id = $('#user').val();
            let archive_note = $('#archive_note').val();

            if (archive_note == "") {
                showToastify("Please add a reason for Archive.", "error")
                return;
            } else {
                // alert(user_id)
                // alert(lead_id)
                // alert(archive_note)
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: "{{ route('api.thirdparty.archiveLead') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        user_id: user_id,
                        lead_id: lead_id,
                        archive_note: archive_note,
                    },
                    success: function(result) {

                        $('#leadNotFound').removeClass('d-none');
                        $('#formdiv').remove();

                        $("#archive_note").val("")
                        $('#archiveModal').modal('hide');

                        showToastify(result.msg, "success")
                    },
                    error: function(jqXhr, json, errorThrown) {
                        showToastify(result.msg, "error")
                        // $(buttons).prop("disabled", false);
                        // $('#screen_loader').addClass('d-none');

                        // let data = jqXhr.responseJSON;
                        // if (data.errors) {
                        //     $.each(data.errors, function(index, item) {
                        //         showToastify(item[0], "error")
                        //     })
                        // }
                        // if (jqXhr.status == 500 || jqXhr.status == 400) {
                        //     showToastify(data.message, "error")
                        // }
                        // $('#screen_loader').addClass('d-none');

                    },
                });
            }
        })


        $(document).ready(function() {
            $('.open_sub_status').click(function() {
                if ($(this).data('status_id') == 8){
                    if ($("#note").val() < 35){
                        $("#note").addClass("is-invalid");
                        $("#note-invalid-feedback").text("Minimum 35 characters required.")
                        return;
                    }else{
                        $("#note").removeClass("is-invalid");
                        $("#note-invalid-feedback").text("Please enter a Note.")
                    }
                }else{
                    $("#note").removeClass("is-invalid");
                    $("#note-invalid-feedback").text("Please enter a Note.")
                }
                $('.OpenStatus').removeClass('ribbon-soft-info');
                $('.OpenStatus').addClass('ribbon-info');
                $('.ClassStatus').addClass('ribbon-soft-primary');
                $('.ClassStatus').removeClass('ribbon-primary');
                $('#lead_status_id').val($(this).data('status_id'));
                let lead = $('#lead').val();
                let user = $('#user').val();
                let sub_id = $(this).data('sub_id');
                let status_id = $(this).data('status_id');
                let note = $("#note").val();
                $('.rmDark').addClass('randomClass');
                $(this).find('span').removeClass('randomClass');
                // console.log('lead = '+lead+',user ='+user+',key ='+key+',id ='+id);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: "{{ route('api.thirdparty.update.lead.status') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        sub_id: sub_id,
                        status_id: status_id,
                        lead: lead,
                        user: user,
                        note: note,
                    },
                    success: function(result) {
                        showToastify(result.msg, "success")
                    },
                    error: function(jqXhr, json, errorThrown) {
                        showToastify(jqXhr?.responseJSON?.msg || "", "error")
                    }
                })
            })

            $('.closed_sub_status').click(function() {
                $('.ClassStatus').removeClass('ribbon-soft-primary');
                $('.ClassStatus').addClass('ribbon-primary');
                $('.OpenStatus').addClass('ribbon-soft-info');
                $('.OpenStatus').removeClass('ribbon-info');
                $('#lead_status_id').val($(this).data('status_id'));
                let lead = $('#lead').val();
                let user = $('#user').val();
                let sub_id = $(this).data('sub_id');
                let status_id = 2;
                $('.rmDark').addClass('randomClass');
                $(this).find('span').removeClass('randomClass');
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: "{{ route('api.thirdparty.update.lead.status') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        sub_id: sub_id,
                        status_id: status_id,
                        lead: lead,
                        user: user
                    },
                    success: function(result) {
                        showToastify(result.msg, "success")
                    },
                    error: function(jqXhr, json, errorThrown) {
                        showToastify(result.msg, "error")
                    }
                })


            });
        });
        $(document).ready(function() {
            $('.updateItem').change(function() {
                // let value = $(this).val();
                let key = $(this).data('name');
                let id = $(this).val();
                let lead = $('#lead').val();
                let user = $('#user').val();
                // console.log(key + ' = ' + id + ' Lead =' + lead + ',user =' + user);
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    },
                    url: "{{ route('api.thirdparty.update.lead') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        key: key,
                        id: id,
                        lead: lead,
                        user: user
                    },
                    success: function(result) {
                        showToastify(result.msg, "success")
                        // swal("Success!", result.msg, "success");
                    },
                    error: function(jqXhr, json, errorThrown) {
                        showToastify(result.msg, "error")
                    }
                })
            });


            getLead("{{ request()->input('lead') }}")

            function getLead(id) {
                $.ajax({
                    url: "{{ route('getAjaxLead') }}",
                    type: "get",
                    data: {
                        id: id
                    },
                    success: function(result) {

                        if (result.status == '1') {

                            if(result.data.is_archive==1){
                                $('#formdiv').remove();
                                $('#leadNotFound').removeClass('d-none');
                            }

                            $('.open_sub_status').find('span').addClass('randomClass');
                            $('.closed_sub_status').find('span').addClass('randomClass');

                            if (result?.data?.lead_status?.badge) {
                                $('.sub_' + result.data.lead_status_id).addClass(result.data
                                    .lead_status.badge);
                                $('.sub_' + result.data.lead_status_id).removeClass('randomClass');
                            }


                            // console.table(result.data.lead_sub_status_id)
                            if (result.data.lead_status_id) {

                                $('.OpenStatus').removeClass('ribbon-soft-info');
                                $('.OpenStatus').addClass('ribbon-info');
                            }
                            dataObj = result.data;
                            // $(`#source_id`).val(result.data.source_id).trigger("change")
                            // $(`#lead_medium_id`).val(result.data.lead_medium_id).trigger("change")
                        }

                    },
                    error: function(jqXhr, json, errorThrown) {
                        showToastify('Something went wrong', "error");

                        console.log(jqXhr);
                        console.log(json);
                        console.log(errorThrown);
                    }
                });
            }


        });
    </script>
@endsection
