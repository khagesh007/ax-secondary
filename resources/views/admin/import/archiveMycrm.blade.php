@extends('admin.layouts.master')
@section('title')
    Search and import archive properties
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
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
        .telegram-icon{
            color: #2a99cb;
            font-size: 20px;
        }
        .email-icon{
            color: #c61b1b;
            font-size: 20px;
        }
        .web-icon{
            color: #4f4040;
            font-size: 20px;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="{{ asset("assets/libs/tagify/tagify.css") }}" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <form class="row needs-validation" id="mainForm" novalidate>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Archive search</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Import</a></li>
                            <li class="breadcrumb-item active">MyCrm Archive Search</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="collapse slideIn show" id="collapseContactForm">
                <div class="card">
                    <div class="card-body">
                        <div>
                            <div class="row">



                                <div class="row g-3">
                                    <div class="col-xl-8 offset-1">

                                        <div class="search-box">
                                            <input type="text" class="form-control search" required name="keyword" placeholder="Enter Reference Number..">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                        <div class="col-sm mt-3">
                                            <div>
                                                <h5 class="fs-14 mb-0 d-none">1 result found</h5>
                                            </div>
                                        </div>
                                        <div class="col-sm-auto">

                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-xl-2">

                                                <div>
                                                    <button type="submit" class="btn btn-primary w-100">
                                                        <i class="ri-search me-2 align-bottom"></i>Search</button>
                                                </div>

                                    </div>
                                </div>


                            </div>


                            <div class="row d-none" id="searchFailed">
                                <div class="tab-pane active"  role="tabpanel">
                                    <div class="py-4 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                        </lord-icon>
                                        <h5 class="mt-4">Sorry! No Result Found</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="row d-none" id="searching">
                                <div class="tab-pane active"  role="tabpanel">
                                    <div class="py-4 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                        </lord-icon>
                                        <h5 class="mt-4">Searching please wait ......</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row m-1" id="searchResult">

                </div>
            </div>
        </div>
    </form>

@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{ asset("assets/libs/tagify/tagify.min.js") }}"></script>
        {{-- <script src=" "></script> --}}


    <script>









        $(document).on('submit', '#mainForm', function (event) {
            $("#searching").removeClass('d-none');
            $("#searchFailed").addClass('d-none');
            $("#searchResult").html('');
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);


            $.ajax({
                url: "{{route('admin.import.archive.search.mycrm')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {

                    $("#saveBtn").prop('disabled', false); //enable submit button
                    $("#searching").addClass('d-none');
                    if (result.status == '1') {

                        showToastify(result.msg, "success")
                        if(result.data != ''){

                           let html = `<div class="card product">
                                    <div class="card-body">
                                        <div class="row gy-3">
                                            <div class="col-sm-auto">
                                                <div class="avatar-lg bg-light rounded p-1">
                                                    <img src="${result.data?.images[0]?.file}" alt="" class="img-fluid d-block">
                                                </div>
                                            </div>
                                            <div class="col-sm">
                                                <h5 class="fs-14 text-truncate"><p class="text-dark">${result.data.reference} ${result.data.languages[0].title}</p></h5>
                                                <ul class="list-inline text-muted">
                                                    <li class="list-inline">
                                                Location : <span class="fw-medium">${result.data.location?.tower}, ${result.data.location?.sub_community}, ${result.data.location?.community}, ${result.data.location?.city}</span>
                                                    </li>
                                                    <li class="list-inline-item">Title Deed : <span class="fw-medium">${result.data.title_deed}</span>
                                                    </li>
                                                    <li class="list-inline-item">Unit no. : <span class="fw-medium">${result.data.unit_number}</span>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-sm-auto">
                                                <div class="text-lg-end" id="importBtnParent">
                                                    <button type="button" onclick="importProperty('`+result.data.id+`')" class="btn btn-success w-100">
                                                        <i class="ri-download-2-fill me-2 align-bottom"></i>Import</button>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>`;
                           $("#searchResult").html(html);
                        }
                    } else if (result.status == '2') {
                        $("#searchResult").html('');
                        $("#searchFailed").removeClass('d-none');
                        $("#saveBtn").prop('disabled', false);
                        let error_message = '';
                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#searchResult").html('');
                        $("#searchFailed").removeClass('d-none');
                        showToastify(result.msg, "error")

                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $("#saveBtn").prop('disabled', false);
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")

                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.message, "error")
                    }
                },
            });
        });







function importProperty(id){
    swal({
        title: "Attention!!",
        text: "Are you sure you want to import this property from mycrm to our system?",
        icon: "warning",
        buttons: true,
        dangerMode: true,
    })
        .then((willDelete) => {
            if (willDelete) {
                $("#importBtnParent").html(`<button type="button" class="btn btn-success btn-load">
                                        <span class="d-flex align-items-center">
                                            <span class="spinner-border flex-shrink-0" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </span>
                                            <span class="flex-grow-1 ms-2">
                                                Processing...
                                            </span>
                                        </span>
                                    </button>`)
                $.ajax({
                    url: "{{route('admin.import.archive.mycrm.import')}}",
                    type: 'POST',
                    dataType: 'json',
                    data: {id},
                    success: function (result) {
                        if (result.status == '1') {
                            $("#importBtnParent").html(`<button type="button" class="btn btn-success btn-label waves-effect waves-light"><i class="ri-check-double-line label-icon align-middle fs-16 me-2"></i> Success</button>`);
                            showToastify(result.msg, "success")

                        } else {
                            showToastify(result.msg, "error")
                        }
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
        });


}

        $("#sidebarConfiguration").addClass('collapse show');
        $("#configureNotificationSidebar").addClass('active');
    </script>
@endsection
