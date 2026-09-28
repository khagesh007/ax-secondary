@extends('layouts.master-without-nav')
@section('title')
    Assign Listing Lead Form
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
        <div class="auth-page-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center mt-2">
                            <img src="{{ asset('assets/images/logo/ax-short-logo.png') }}" alt="">
                        </div>

                    </div>
                </div>
                <div class="row  justify-content-center formDiv">
                    <div class="col-md-8" id="leadDetails">
                        <div class="card mt-4">

                            <div class="card-body p-4">

                                <div class="  ">

                                    <div class="row">
                                        <div class="col-md-12 text-center text-primary">
                                            <h2 class="mb-1">Assign Listing Lead</h2>
                                        </div>
                                    </div>
                                    @include('form.components._listingLead', ['listing' => $lead])

                                    <form class="needs-validation" novalidate id="mainForm">

                                        <input type="hidden" name="listing_lead_id" value="{{ $lead->id }}">
                                        <input type="hidden" name="manager_id" value="{{ $manager->id }}">

                                        {{-- <div class="row mt-3">
                                            <div class="col-md-12 ">

                                                <label for="agent_id" class="form-label required">Agent<span>*</span></label>
                                                <select name='agent_id' id="agent_id" class="form-control select2 inputs" required>
                                                    <option></option>
                                                    @foreach ($users ?? [] as $user)

                                                            <option value="{{ $user->id }}">{{ $user->full_name }}</option>

                                                    @endforeach
                                                </select>
                                                <div class="valid-feedback">
                                                    Please Select any Agent.
                                                </div>
                                            </div>
                                        </div> --}}

                                        @include('admin.templates._agents_select2', ['agents' => $users])






                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" id="saveBtn"
                                                type="button">Submit</button>
                                            <button type="submit" class="d-none" id="submitBtn"></button>
                                        </div>
                                    </form>



                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->



                    </div>

                    <div class="col-md-8 mt-5  d-none" id="successMessage">
                        <div class="card ">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                </div>
                                <div class="p-2 mt-4 ">
                                    <div class="row text-center">

                                        <div class="col-md-12">
                                            <h4>Lead Transferred Successfully.</h4>

                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                    </div>

                    <div class="col-md-8 mt-5  d-none" id="alreadyAssigned">
                        <div class="card ">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                </div>
                                <div class="p-2 mt-4 ">
                                    <div class="row text-center">

                                        <div class="col-md-12">
                                            <h4 id="alreadyAssignedMsg"></h4>

                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
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
        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })
        $(document).on('submit', '#mainForm', function(event) {
            $("#saveBtn").prop('disabled', true);
            $('#screen_loader').removeClass('d-none');
            event.preventDefault();
            var formData = new FormData($("#mainForm")[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('api.thirdparty.listingLeadAssignmentPost') }}",
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
                        $("#saveBtn").prop('disabled', true);
                        $('#leadDetails').addClass('d-none');
                        $('#successMessage').removeClass('d-none');
                    }
                    if (result.status == 3) {
                        $('#alreadyAssigned').removeClass('d-none');
                        $('#leadDetails').addClass('d-none');
                        $('#alreadyAssignedMsg').html(result.msg);
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

                    if (data.message == "CSRF token mismatch.") {
                        showToastify('CSRF Token Mismatch. Please refresh the page.', 'error');
                    }
                    $('#screen_loader').addClass('d-none');

                },
            });




        });
    </script>
@endsection
