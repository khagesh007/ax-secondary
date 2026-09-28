@extends('layouts.master-without-nav')
@section('title')
    Deal Commission Details
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



    <meta name="csrf-token" content="{{ csrf_token() }}">
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
                    <div class="col-md-9" id="leadDetails">
                        <div class="card mt-4">

                            <div class="card-body p-4">

                                <div class="  ">
                                    <div class="row">
                                        <div class="col-md-12 text-center text-primary">
                                            <h2 class="mb-1">Deal Details</h2>
                                        </div>
                                    </div>

                                    <form class="needs-validation" novalidate id="mainForm">

                                        <div class=" row">

                                            <div class="col-12">
                                                <h5 class="m-2">Property Details</h5>
                                                <div class="card d-flex  flex-row p-3 m-1">
                                                    <div class="d-flex flex-column  align-self-center border">
                                                        <img src="{{$property->first_image->thumbnail ?? ''}}"
                                                            style="width: 50px; height: 50px" alt="" srcset="">

                                                    </div>
                                                    <div
                                                        class="card-body d-flex flex-column m-1 align-self-center  justify-content-between">
                                                        <h5 class="card-title font-bold text-black">
                                                            <b>{{ $property->ref_no }}</b> - {{ $property->category }}
                                                        </h5>
                                                        <p class="card-text"> {{ $property->location }}</p>
                                                    </div>
                                                    <div
                                                        class=" d-flex flex-column justify-content-between align-items-end">
                                                        <h4><span
                                                                class="badge border  {{ $property->publish_status === 'Approved' ? 'text-success' : 'text-danger' }}">
                                                                {{ $property->publish_status }}
                                                            </span></h4>
                                                        <h4><span class="badge text-warning">
                                                                AED {{ $property->price->price }}
                                                            </span></h4>



                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <h5 class="m-2">Commission Details</h5>
                                                <div class="card mt-3  p-4 m-2">
                                                    <div class="row ">
                                                        <div class="col-md-12">
                                                            <table class="table table-bordered">
                                                                <tbody>
                                                                    <tr>
                                                                        <td width="25%"><b>Deal Price</b></td>
                                                                        <td width="25%"><span>
                                                                            AED {{ $deal->deal_price }}</span></td>
                                                                    </tr>

                                                                    <tr>
                                                                        <td width="25%"><b>Net Commission </b></td>
                                                                        <td width="25%"><span>
                                                                            AED {{ $deal->net_commission }}</span></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                        @php
                                                            $total_commission = 0;
                                                            $total_commission_percetange = 0;
                                                        @endphp
                                                        @foreach ($deal_split_commissions as $deal_split_commission)
                                                            @php
                                                                $total_commission += $deal_split_commission->commission;
                                                                $total_commission_percetange += $deal_split_commission->commission_value;
                                                            @endphp
                                                        @endforeach

                                                        <div class="col-md-12 mt-2">
                                                            <h2 class="text-center">
                                                                <b>Your Commission :  AED {{ $total_commission }}
                                                                    {{-- ({{ $total_commission_percetange }}%) --}}
                                                                </b>
                                                            </h2>

                                                        </div>



                                                    </div>

                                                </div>

                                            </div>

                                            <div class="col-12">


                                                <div class="accordion m-2 card" id="split-details-accordion">
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header"
                                                            id="split-details-accorion-required-header">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#split-details-accorion-required-collabpse"
                                                                aria-expanded="false"
                                                                aria-controls="split-details-accorion-required-collabpse">
                                                                Split Details
                                                            </button>
                                                        </h2>
                                                        <div id="split-details-accorion-required-collabpse"
                                                            class="accordion-collapse collapse"
                                                            aria-labelledby="split-details-accorion-required-header"
                                                            data-bs-parent="#split-details-accordion">
                                                            <div class="accordion-body">
                                                                @foreach ($deal_split_commissions as $deal_split_commission)
                                                                    <h4 class="text-center">
                                                                        AED {{ $deal_split_commission->split->split_amount }}

                                                                        ({{ $deal_split_commission->split->commission_source }})
                                                                    </h4>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>

                                        @if ($deal_approval->approval_status != "Approved" && $deal_approval->approval_status != "Rejected" )

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" id="approveBtn"
                                            type="button">Approve</button>
                                            <button class="btn btn-danger mt-2 w-100" id="rejectBtn"
                                            type="button">Reject</button>
                                            <button type="submit" class="d-none" id="submitBtn"></button>
                                        </div>
                                        @else
                                            <h2 class="text-center mt-4 text-{{$deal_approval->approval_status == "Approved" ? "success" : "danger"}}">{{ $deal_approval->approval_status }}</h2>
                                            <p class="text-center text-danger">{{ $deal_approval->approval_status == "Rejected" ? "Reason: ".$deal_approval->comment : "" }}</p>
                                        @endif


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
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>
    {{-- <script src="https://unpkg.com/sweetalert2/dist/sweetalert2.min.js"></script> --}}
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/js/helpers.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>

        const uuid = "{{ $deal_approval->uuid }}";

        $(document).on("click", "#approveBtn", function() {
            Swal.fire({
                html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to approve this deal ?</p></div></div>',
                icon: "warning",
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonClass: 'btn btn-warning action-close w-xs',
                showCloseButton: true,
                confirmButtonText: 'Confirm',
                confirmButtonClass: 'btn btn-success w-xs me-2',
                buttonsStyling: false,
            }).then(function(result) {
                if (result.isConfirmed) {

                    $.ajax({
                        url: "{{ route('update.deal.approval.external') }}",
                        type: "POST",
                        data: {
                            uuid: uuid,
                            approval_status: "Approved",
                            comment: null,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });


                }
            });
        })
        $(document).on("click", "#rejectBtn", function() {
            Swal.fire({
                html: '<div class="mt-3"><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to reject this deal?</p></div></div>',
                icon: "error",
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                cancelButtonClass: 'btn btn-warning action-close w-xs',
                showCloseButton: false,
                confirmButtonText: 'Reject',
                confirmButtonClass: 'btn  btn-success w-xs me-2',
                buttonsStyling: false,
                input: 'text', // Add input field for the note
                inputPlaceholder: 'Add Note (required)',
                preConfirm: (note) => {
                    if (!note.trim()) {
                        Swal.showValidationMessage('Note is required');
                    }
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    // Check if a note is added
                    const note = Swal.getInput().value;
                    if (note.trim() !== "") {
                        $.ajax({
                        url: "{{ route('update.deal.approval.external') }}",
                        type: "POST",
                        data: {

                            uuid: uuid,
                            approval_status: "Rejected",
                            comment: note,
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            location.reload();
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                    }
                }
            });
        })
    </script>

@endsection





{{-- <div class="card-body">
                                                <div class="row row-cols-1 ms-1 me-1">
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <h5>Property Details</h5>
                                                            <div class="row mt-4">
                                                                <div class="col-md-6">
                                                                    <table class="table table-bordered">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td width="30%"><b>Reference #</b></td>
                                                                                <td width="70%"><span>{{ $property->ref_no }}</span></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td width="30%"><b>Type </b></td>
                                                                                <td width="70%"><span>{{ $deal->type }}</span></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td width="30%"><b>Title </b></td>
                                                                                <td width="70%"><span>{{ $property->title }}</span></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td width="30%"><b>Bed</b></td>
                                                                                <td width="70%"><span>{{ $property->bed->name ?? "" }}</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td><b>Bath</b></td>
                                                                                <td width="70%"><span>{{ $property->bath->name ?? "" }}</span></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td width="30%"><b>Location</b></td>
                                                                                <td><span>{{ $property->location }}</span></td>
                                                                            </tr>


                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <table class="table table-bordered">

                                                                        <tbody>

                                                                            <tr>
                                                                                <td width="30%"><b>Publish Status</b></td>
                                                                                <td width="70%"><span>{{ $property->publish_status }}</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="30%"><b>Category</b></td>
                                                                                <td width="70%"><span>{{ $property->category }}</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="30%"><b>Price</b></td>
                                                                                <td width="70%"><span>{{ $property->price->price ?? ""}}</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="30%"><b>Built Up Area</b></td>
                                                                                <td width="70%"><span>{{ $property->built_up_area }}</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="30%"><b>Completion Status</b></td>
                                                                                <td width="70%"><span>{{ $property->completion_status }}</span></td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="30%"><b>Responsible Agent</b></td>
                                                                                <td width="70%"><span>{{ $deal->agent->name ?? "" }}</span></td>
                                                                            </tr>

                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <h5>Commission Details</h5>
                                                            <div class="row mt-4">
                                                                <div class="col-md-6">
                                                                    <table class="table table-bordered">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td width="30%"><b>Deal Price</b></td>
                                                                                <td width="70%"><span>AED - {{ $deal->deal_price }}</span></td>
                                                                            </tr>

                                                                            <tr>
                                                                                <td width="30%"><b>Net Commission </b></td>
                                                                                <td width="70%"><span>AED - {{ $deal->net_commission }}</span></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <table class="table table-bordered">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td width="30%"><b>Commission (AED)</b></td>
                                                                                <td width="70%"><span></span>AED - 355</td>
                                                                            </tr>
                                                                            <tr>
                                                                                <td width="30%"><b>Commission (%)</b></td>
                                                                                <td width="70%"><span>AED - 5%</span></td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="text-end">
                                                    <button class="btn btn-success" id="approve-button">Approve</button>
                                                    <button class="btn btn-danger" id="reject-button">Reject</button>
                                                </div>
                                            </div> --}}
