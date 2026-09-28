@extends('admin.layouts.master')
@section('title')
    Developers MetaData
@endsection
@section('style')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />

    <style>
        .center-tabs {
            display: flex;
            justify-content: center;
        }

        h2 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h5 {
            color: var(--primary-text);
            background: -webkit-linear-gradient(#b7846a, #f27a46);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        table tr td {
            padding: 0px !important
        }

        .blur {
            filter: blur(5px);
        }
    </style>
@endsection
@section('content')
    @include('admin.partials.loader')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0">Developers </h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a
                                    href="javascript: void(0);">{{ $developer->developer->full_name }}</a></li>
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ $developer->project_name }}</a>
                            </li>
                            <li class="breadcrumb-item active">Meta</li>
                        </ol>
                    </div>

                    <div class="flex-grow-2">

                        <button id="updateReraNumberBtn" class="btn btn-info">Update Rera Number</button>
                        <button id="publishPropertyBtn" class="btn btn-primary">Publish Draft Properties</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" value="{{ $developer->developer->full_name }}" id="developer_name">
    <input type="hidden" value="{{ $developer->project_name }}" id="project_name">
    <input type="hidden" name="id" value="{{ $developer->id }}" id="dev_id">

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class=" text-center">
                        <h3 class="">
                            {{ $developer->developer->full_name }}, {{ $developer->project_name }} Meta Data
                        </h3>
                        <hr>
                    </div>



                    <form action="{{ route('admin.developer.meta.update', $developer->id) }}" id="mainForm" method="POST">
                        @csrf
                        <div class="row">

                            <div class="col-md-4">
                                <select name="developer_id" id="developer_id" class="form-control">
                                    <option value="">Select Developer</option>
                                    @foreach ($developers as $dev)
                                        <option value="{{ $dev->id }}"
                                            {{ $dev->id == $developer->developer_id ? 'selected' : '' }}>
                                            {{ $dev->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Project Name" name="project_name"
                                    value="{{ $developer->project_name }}">
                            </div>
                            <div class="col-md-4">
                                <input type="text" class="form-control" placeholder="Project Rera Number"
                                    name="rera_permit" value="{{ $developer->rera_permit }}">
                            </div>

                        </div>
                        <div class="col-md-12 text-end mt-2">
                            <button type="submit" class=" submitBtn btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <h3 class="">
                            Title(s)
                        </h3>
                        <hr>
                    </div>
                    <form action="{{ route('admin.developer.meta.update', $developer->id) }}" id="mainForm"
                        method="POST">
                        @csrf
                        <div class="row" id="appendHere">
                            <div class="col-md-6">
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <input type="text" class="form-control" placeholder="Project Name"
                                            name="title">
                                    </div>
                                </div>
                            </div>

                            @include('admin.offplan.partials._titles')
                        </div>
                        <div class="col-md-12 text-end mt-2">
                            {{-- <button type="button" class="btn btn-primary" id="appendTitle">Add Title</button> --}}
                            <button type="button" class="btn btn-primary" id="genTitleWithChatGpt" data-bs-toggle="modal"
                                data-bs-target="#myModal">Generate Titles with AI</button>
                            <button type="button" data-type="title" class="updateBtn btn btn-primary">Apply Changes to
                                {{ $projectProperty }}</button>

                            <button type="submit" class=" submitBtn btn btn-primary">Save</button>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row sectionBlurShow">
        <div class="col-md-12 mb-3 sectionloaderShow">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <h3 class="">
                            Description
                        </h3>
                        <hr>
                    </div>
                    <form action="{{ route('admin.developer.meta.update', $developer->id) }}" id="mainForm"
                        method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-xxl-12">
                                <ul class="nav nav-tabs mb-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#projectbrifing"
                                            role="tab" aria-selected="false">
                                            Project briefing
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#amenities" role="tab"
                                            aria-selected="false">
                                            Amenities
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#paymentPlan" role="tab"
                                            aria-selected="false">
                                            Payment Plan
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#additional" role="tab"
                                            aria-selected="false">
                                            Additional Description
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#signature" role="tab"
                                            aria-selected="false">
                                            Company Signature
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link makePreview" data-bs-toggle="tab" href="#preview"
                                            role="tab" aria-selected="false">
                                            Preview
                                        </a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content  text-muted">
                                    <div class="tab-pane active" id="projectbrifing" role="tabpanel">
                                        <h6>Project</h6>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <textarea type="text" class="form-control" placeholder="Project description" name="description" value=""
                                                    rows="10" id="description">{{ $developer->description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="amenities" role="tabpanel">
                                        <h6>Amenities</h6>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <textarea type="text" class="form-control" placeholder="Amenities" name="amenities" rows="10"
                                                    id="project_amenities">{{ $developer->amenities }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="paymentPlan" role="tabpanel">
                                        <h6>Payment Plan</h6>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <textarea type="text" class="form-control" placeholder="Project description" name="payment_plans" rows="10"
                                                    id="project_payment_plan">{{ $developer->payment_plans }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="additional" role="tabpanel">
                                        <h6>Additional Description</h6>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <textarea type="text" class="form-control" placeholder="Additional Description" name="additional_description"
                                                    rows="10" id="additional_description">{{ $developer->additional_description }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="signature" role="tabpanel">
                                        <h6>Company Signature</h6>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <textarea type="text" class="form-control" placeholder="Company Signature" name="signature" rows="10"
                                                    id="company_signature">{{ $developer->signature }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane" id="preview" role="tabpanel">
                                        <h6>Preview</h6>
                                        <div class="row mt-2">
                                            <div class="col-md-12">
                                                <textarea type="text" class="form-control" placeholder="Preview" value="" rows="10" id="previewtab"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div><!--end col-->
                        </div>
                        <div class="col-md-12 text-end mt-2">
                            <button type="button" class="btn btn-primary" disabled id="generateWithAiDisabled">Generate
                                With AI</button>
                            <button type="button" class="btn btn-primary d-none" id="generateWithAi">Generate With
                                AI</button>
                            <button type="button" data-type="description" class="updateBtn btn btn-primary">Apply
                                Changes to {{ $projectProperty }}</button>

                            <button type="submit" class=" submitBtn btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <h3 class="">
                            Amenities
                        </h3>
                        <hr>
                    </div>
                    <div id="load-amenities"></div>

                    <div class="col-md-12 text-end mt-2">
                        <button type="button" data-type="amenities" class="updateBtn btn btn-primary">Apply Changes to
                            {{ $projectProperty }}</button>

                    </div>


                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <h3 class="">
                            Gallery
                        </h3>
                        <hr>
                    </div>
                    <form action="{{ route('admin.developer.meta.storeGallery', $developer->id) }}" id="mainForm"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row mt-2">
                                    <div class="col-md-12">
                                        <input type="file" class="form-control" placeholder="images" name="images[]"
                                            multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-end mt-2 mb-2">
                            <button type="button" data-type="images" class="updateBtn btn btn-primary">Apply Changes to
                                {{ $projectProperty }}</button>

                            <button type="submit" class="submitBtn btn btn-primary">Save</button>
                        </div>
                    </form>

                    <div class="row">
                        @foreach ($developer->images as $img)
                            <div class="col-md-2">
                                @foreach (json_decode($img->value) as $key => $title)
                                    @if ($key == 'original')
                                        <img src="{{ $title }}" alt="" class="img-fluid img-thumbnail">
                                    @endif
                                @endforeach
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="cloneInput" class="d-none">
        <div class="col-md-6">
            <input type="text" class="form-control mt-2" placeholder="Meta Title" name="titles[]">
        </div>
    </div>


    <!-- Default Modals -->
    <div id="myModal" class="modal fade" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true"
        style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="myModalLabel">Title Generate Using AI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"> </button>
                </div>
                <div class="modal-body">
                    <h5 class="fs-15">
                        NOTE:
                    </h5>
                    <p class="text-muted">Enter At least 5 words, Important Points</p>

                    <div>
                        <label for="basiInput" class="form-label">Keywords</label>
                        <input type="text" class="form-control" id="keywords">
                    </div>

                    <div class="mt-2">
                        <label for="basiInput" class="form-label">No. of titles you want to generate?</label>
                        <input type="number" class="form-control" id="total_titles" max="5" min="1">
                    </div>


                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="generateTitle">Generate Titles</button>
                </div>

            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
@endsection
@section('script')
    @include('admin.partials.datatable')
    <script src="https://cdn.lordicon.com/lordicon.js"></script>

    <script>
        descriptionChecker();
        loadAmenities();
        $(document).on('click', '.submitBtn', function(e) {
            $('#screen_loader').removeClass('d-none');
        });

        $(document).ready(function() {
            // on press of submit button add this
            $('#developersTable').DataTable({
                responsive: true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, 'All']
                ],


            });

            $("#appendTitle").click(function() {
                $("#appendHere").append($("#cloneInput").html());
            });



            $("#description").keyup(function() {
                descriptionChecker();
            });

            $(".makePreview").click(function() {
                previewMaker();
            });




            $("#generateTitle").click(function() {

                $.ajax({
                    url: "{{ route('ajax.generateTitleWithChatGpt') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        keywords: $('#keywords').val(),
                        total_titles: $('#total_titles').val(),
                        dev_id: $('#dev_id').val(),

                    },
                    success: function(response) {
                        if (response.status == 1) {
                            showToastify(response.msg, "success");
                            $('#myModal').modal('hide');
                        }
                    }
                })
            });



            $(document).on('click', '.updateBtn', function(e) {
                let type = $(this).data('type');
                $.ajax({
                    url: "{{ route('admin.developer.meta.updateExistingProperty') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        type: type,
                        id: $('#dev_id').val(),
                    },
                    dataType: 'json',
                    success: function(response) {

                    }
                })

            })

            $(document).on('click', '#updateReraNumberBtn', function(e) {

                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/muxyivho.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you sure to Update Rera Permit Number?</p></div></div>',
                        showCancelButton: !0,
                        confirmButtonClass: "btn btn-primary w-xs me-2 mb-1",
                        confirmButtonText: "Yes, Update Rera Permit.",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        buttonsStyling: !1,
                        showCloseButton: !0


                    })
                    .then(function(t) {

                        if (t.isConfirmed) { // on confirm delete the category
                            $.ajax({
                                url: "{{ route('admin.developer.meta.updateReraNumber') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: $('#dev_id').val(),
                                },
                                dataType: 'json',
                                success: function(response) {

                                }
                            })
                        }
                    })



            })


            $(document).on('click', '#publishPropertyBtn', function(e) {

                Swal.fire({

                        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/muxyivho.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">Are you sure to publish?</p></div></div>',
                        showCancelButton: !0,
                        confirmButtonClass: "btn btn-primary w-xs me-2 mb-1",
                        confirmButtonText: "Yes, Publish  Property(s).",
                        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
                        buttonsStyling: !1,
                        showCloseButton: !0


                    })
                    .then(function(t) {

                        if (t.isConfirmed) { // on confirm delete the category
                            $.ajax({
                                url: "{{ route('admin.developer.meta.publishOffplanImportedProperty') }}",
                                type: "POST",
                                data: {
                                    _token: "{{ csrf_token() }}",
                                    id: $('#dev_id').val(),
                                },
                                dataType: 'json',
                                success: function(response) {

                                }
                            })
                        }
                    })



            })


            $(document).on('click', '.saveAmenities', function(e) {
                saveAmenities();
            })
            $(document).on('click', '.remove-title', function(e) {
                let id = $(this).data('id');
                let type = $(this).data('type');

                $.ajax({
                    url: "{{ route('admin.developer.meta.delete') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: id,
                        type: type,
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status == 1) {
                            $('.removeClass-' + id).remove();
                            showToastify(response.msg, "success");
                        }
                    }
                })
            })



            $("#generateWithAi").click(function() {
                $("#generateWithAi").addClass('d-none');
                $("#generateWithAiDisabled").removeClass('d-none');
                $(".sectionloaderShow").addClass('blur');
                $(".sectionBlurShow").append('<div class="sectionloader"></div>');

                $.ajax({
                    url: "{{ route('ajax.generateDescriptionWithChatGpt') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        description: $('#description').val(),
                        developer_name: $('#developer_name').val(),
                        project_name: $('#project_name').val(),
                        dev_id: $('#dev_id').val(),
                    },
                    success: function(response) {
                        $("#description").val(response);
                        $("#generateWithAi").removeClass('d-none');
                        $("#generateWithAiDisabled").addClass('d-none');
                        $(".sectionloaderShow").removeClass('blur');
                        $(".sectionloader").remove();




                    }
                })
            });


        });

        function descriptionChecker() {
            var words = $('#description').val().split(' ').length;
            if (words >= 10) {

                $("#generateWithAi").removeAttr('disabled');
                $("#generateWithAiDisabled").addClass('d-none');
                $("#generateWithAi").removeClass('d-none');
            } else {
                $("#generateWithAi").attr('disabled', 'disabled');
            }
        }

        function loadAmenities() {

            $.ajax({
                url: "{{ route('ajax.loadAmenities') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    dev_id: $('#dev_id').val(),
                },
                dataType: 'html',
                success: function(response) {
                    if (response) {
                        $('#load-amenities').html(response);
                    }
                }
            })
        }

        function saveAmenities() {
            var amenities = [];
            $('.inputs').each(function() {
                if ($(this).is(":checked")) {
                    amenities.push($(this).val());
                }
            });

            $.ajax({
                url: "{{ route('ajax.saveAmenities') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    amenities: amenities,
                    dev_id: $('#dev_id').val(),
                },
                success: function(response) {
                    if (response.status == 1) {
                        // remove previus showToastify
                        $('.toastify').remove();
                        showToastify(response.msg, "success");
                    }
                }
            })
        }

        function previewMaker() {
            var description = $('#description').val();
            var project_amenities = $('#project_amenities').val();
            var project_payment_plan = $('#project_payment_plan').val();
            var additional_description = $('#additional_description').val();
            var company_signature = $('#company_signature').val();

            var preview = description + '\n\n' + project_amenities + '\n\n' + project_payment_plan + '\n\n' +
                additional_description + '\n\n' +
                company_signature;

            $("#previewtab").val(preview);
        }
    </script>
@endsection
