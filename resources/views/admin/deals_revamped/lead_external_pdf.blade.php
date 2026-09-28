@extends('layouts.master-without-nav')
@section('title')
    Update Details Form
@endsection
@section('meta_description', 'Lead Generation Form')
@section('css')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />


    {{-- <meta name="csrf-token" content="{{ csrf_token() }}"> --}}
    <link rel="stylesheet" href="{{ asset('assets/libs/intl-tel-input/css/intlTelInput.min.css') }}">

    <style>
        canvas {
            width: auto;
        }
    </style>
@endsection

@section('content')
    @include('admin.partials.loader')

    <div class="auth-page-wrapper pt-2" style="margin-bottom: 30px;">
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
                    <div class="col-md-12" id="leadDetails">
                        <div class="card mt-4">

                            <div class="card-body p-4">

                                <div>
                                    <div id="editLeadForm" class="">

                                        <form id="mainForm">
                                            <div class="row mt-3">



                                                <input type="hidden" value="{{ $invite->invite_code }}" name="invite_code">
                                                @if ($invite->is_signed != 1)
                                                    <div class="col-8 offset-2">
                                                        <div class="alert alert-danger mb-xl-0" role="alert">
                                                            <i class="ri-error-warning-line me-3 align-middle fs-16"></i>
                                                            Your KYC document is missing your signature. Please sign below
                                                            to proceed.
                                                        </div>
                                                    </div>


                                                <div class="col-12 mt-4">
                                                    <p class="text-center">You can complete the KYC document either by
                                                        downloading this unsigned document and sign it physcially and scan
                                                        it then upload it here. or you can sign it here directly see the
                                                        option below.</p>
                                                </div>
                                                @endif
                                                @if (!empty($invite))

                                                    @if ($invite->media != null)
                                                        <div class="row">
                                                            <div class="col-12">
                                                                <iframe src="{{ $invite?->media?->path }}" width="100%"
                                                                    height="600px" zoom="0.75" frameborder="0"></iframe>
                                                            </div>
                                                        </div>
                                                    @endif


                                                @endif




                                                <div class="row col-md-12 mt-3 mb-3">
                                                    <div class="col-md-12 ">
                                                        <a href="{{ $invite?->media?->path }}" class="btn btn-success"
                                                            download>Download PDF</a>
                                                        <span class="float-end">
                                                            @if ($invite->is_signed != 1)
                                                            <button type="button" class="btn btn-primary"
                                                                data-bs-toggle="modal"
                                                                data-bs-target=".bs-example-modal-center">Upload Signed
                                                                Copy</button>
                                                            - OR -
                                                            {{-- <button type="button" class="btn btn-primary"
                                                                data-bs-toggle="modal" data-bs-target=".sign-doc-modal">Sign
                                                                Document Online</button> --}}
                                                            <a href="https://account-d.docusign.com/oauth/auth?response_type=code&scope=signature&client_id=db2c9d62-4451-4780-bc57-094ee18dd914&state=a39fh23hnf23&redirect_uri=http%3A%2F%2Fsecondary.lan%2Fdocusign%2Fcallback" class="btn btn-primary"
                                                               >Sign
                                                                Document Online</a>
                                                                @endif
                                                        </span>
                                                    </div>

                                                </div>

                                            </div>
                                        </form>

                                    </div>
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
                                            <h4>Details Updated Successfully.</h4>

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

    <div class="modal fade bs-example-modal-center" tabindex="-1" aria-labelledby="mySmallModalLabel" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-12">
                        <h4>Upload Signed Copy</h4>
                    </div>
                </div>
                <div class="modal-body p-5">
                    <form id="signPdfForm">
                        <div class="row">

                            <div class="col-6">
                                <input type="hidden" name="invite_code" value="{{ $invite->invite_code }}">
                                <label class="form-label">Select signed PDF</label>
                            </div>
                            <div class="col-6">

                                <input type="file" class="form-control" name="pdf_file" accept="application/pdf"
                                    required>

                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" id="pdfUploadBtn" class="btn btn-primary float-end">Upload</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
    <div class="modal fade sign-doc-modal" tabindex="-1" aria-labelledby="mySmallModalLabel" aria-modal="true">
        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content">
                <div class="modal-header">
                    <div class="col-12">
                        <h4>Sign Online</h4>
                    </div>
                </div>
                <div class="modal-body p-5">
                    <form id="signedDocForm">
                        <div class="row">

                            <div class="col-6">
                                <label class="form-label">Sign Below</label>

                            </div>
                            <div class="col-12">

                                <input type="hidden" name="is_signature" id="is_signature" value="0">
                                <input type="hidden" name="invite_code" value="{{ $invite->invite_code }}">
                                <button type="button" onclick="clearSignature()" class="btn btn-light btn-sm float-end"
                                    id="clear">Clear</button>

                                <div id="signatureCanvas" style="border:1px solid #ced4da; border-radius: .25rem;"></div>
                                <span>* Please match your signature with your passport singnature.</span>
                            </div>

                            <div class="col-12 mt-3">
                                <button type="submit" id="signDocBtn" class="btn btn-primary float-end">Sign
                                    Document</button>
                            </div>

                        </div>
                    </form>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div>
@endsection
@section('script')

    <script src="{{ URL::asset('assets/libs/particles.js/particles.js.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/pages/particles.app.js') }}"></script>

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ URL::asset('/assets/js/app.min.js') }}"></script>


    <script src="https://cdn.jsdelivr.net/npm/lemonadejs/dist/lemonade.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@lemonadejs/signature/dist/index.min.js"></script>

    <script>
        // setDateMaxToday('lead-dob');
        // setDateMaxToday('passport_issue_date');


        // lead-mobile
        function showToastify(
    text = "",
    type = "success",
    duration = 3000,
    gravity = "bottom"
) {
    let style = {};
    if (type == "error") {
        style = {
            background: "linear-gradient(to right, #df2808, #df5c45)",
        };
    }
    Toastify({
        text,
        close: true,
        duration,
        gravity,
        style,
    }).showToast();
}

        const component = Signature(document.getElementById("signatureCanvas"), {
            width: 500,
            height: 200,
            value: [],
            onchange: (o) => {

                //  console.log(JSON.stringify(o.value));
                if (o.value.length > 0) {
                    $("#is_signature").val('1');
                } else {
                    $("#is_signature").val('0');
                }
            }
        });

        function clearSignature() {
            component.value = []; // Reset the signature value to an empty array
            // component.refresh();   // Refresh the component to apply changes
            $("#is_signature").val('0');
        }
    </script>
    <script>
        $(document).on("click", "#saveBtn", function() {
            $("#submitBtn").trigger("click")
        })
        $(document).ready(function() {



        });
        $(document).on('submit', '#mainForm', function(event) {
            $("#updateLeadBtn").prop('disabled', true);
            event.preventDefault();

            // function save() {
            var formData = new FormData($("#mainForm")[0]);
            let signature = component.getImage();
            formData.append('signature', signature);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('collab.deals.update.lead.external.user') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#screen_loader').addClass('d-none');
                    if (result.status == 'success') {
                        showToastify(result.message, "success")
                        $("#updateLeadBtn").prop('disabled', true);
                        $('#leadDetails').addClass('d-none');
                        $('#successMessage').removeClass('d-none');
                    } else {
                        showToastify(result.message, "error")
                        $("#updateLeadBtn").prop('disabled', false);
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
        $(document).on('submit', '#signPdfForm', function(event) {
            $("#pdfUploadBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($("#signPdfForm")[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('collab.deals.pdf.upload') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#screen_loader').addClass('d-none');
                    if (result.status == 'success') {
                        showToastify(result.message, "success")
                        $("#pdfUploadBtn").prop('disabled', true);
                        $('#leadDetails').addClass('d-none');
                        $('#successMessage').removeClass('d-none');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToastify(result.message, "error")
                        $("#pdfUploadBtn").prop('disabled', false);
                    }

                    if (result.status == 3) {
                        // $('#alreadyAssigned').removeClass('d-none');
                        // $('#leadDetails').addClass('d-none');
                        // $('#alreadyAssignedMsg').html(result.msg);
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#pdfUploadBtn").prop('disabled', false);
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
        $(document).on('submit', '#signedDocForm', function(event) {
            $("#pdfUploadBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($("#signedDocForm")[0]);
            let signature = component.getImage();
            formData.append('signature', signature);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                url: "{{ route('collab.deals.sign.document') }}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function(result) {
                    $('#screen_loader').addClass('d-none');
                    if (result.status == 'success') {
                        showToastify(result.message, "success")
                        $("#signDocBtn").prop('disabled', true);
                        $('#leadDetails').addClass('d-none');
                        $('#successMessage').removeClass('d-none');
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    } else {
                        showToastify(result.message, "error")
                        $("#signDocBtn").prop('disabled', false);
                    }

                    if (result.status == 3) {
                        // $('#alreadyAssigned').removeClass('d-none');
                        // $('#leadDetails').addClass('d-none');
                        // $('#alreadyAssignedMsg').html(result.msg);
                    }
                },
                error: function(jqXhr, json, errorThrown) {
                    $("#signDocBtn").prop('disabled', false);
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
