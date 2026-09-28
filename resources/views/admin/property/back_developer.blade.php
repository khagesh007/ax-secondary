@extends('admin.layouts.master')
@section('title')
    Contacts | Developer
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css"/>
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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Developers</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Work</a></li>
                            <li class="breadcrumb-item active">Developers</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can("Developer Create")
                                <button type="button" id="newBtn" class="btn btn-danger add-btn action-add">
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                            @endcan
                            <button type="submit" class="btn btn-success d-none action-save" id="saveBtn">Save</button>
                            @can("Developer Edit")
                                <button type="button" id="editBtn" class="btn btn-info d-none action-edit">Edit</button>
                            @endcan
                         
                            <button type="button" class="btn btn-light d-none action-close" id="closeBtn">Close</button>
                            @can("Developer Delete")
                                <button class="btn btn-soft-danger action-delete d-none" onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="collapse slideIn" id="collapseContactForm">
                <div class="">
                    <div class="">
                        <div>
                            <form class="row needs-validation" id="mainForm" novalidate>
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-4">
                                                    <div class="row">
                                                       <input type="hidden" name="id" id="id">
                                                        <div class="col-4 mt-2">Short Name*</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs" name="short_name"
                                                                   id="short_name"
                                                                   placeholder="Enter short name" required>
                                                            <div class="invalid-feedback">
                                                                Please enter short name.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Full Name*</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs" name="full_name"
                                                                   id="full_name"
                                                                   placeholder="Enter full name" required>
                                                            <div class="invalid-feedback">
                                                                Please enter full name.
                                                            </div>
                                                        </div>
                                                        
                                                        
                                                       <div class="col-4 mt-2">Logo</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="file" class="form-control inputs" name="logo" id="logo"
                                                                   title="select a logo image">
                                                            <div class="invalid-feedback">
                                                                Please select a valid image.
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="col-4 mt-2">Website</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs" name="website" id="website"
                                                                   placeholder="Enter website">
                                                            <div class="invalid-feedback">
                                                                Please enter website.
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                </div>

                                                <div class="col-5">
                                                    <div class="row">
                                                      
                                                        <div class="col-4 mt-2">Contact Person</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs" name="contact_person" id="contact_person"
                                                                   placeholder="Enter contact person name">
                                                            <div class="invalid-feedback">
                                                                Please enter a valid name.
                                                            </div>
                                                        </div>
                                                      
                                                        <div class="col-4 mt-2">Contact Number</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="text" class="form-control inputs" name="phone" id="phone"
                                                                   placeholder="Enter phone number">
                                                            <div class="invalid-feedback">
                                                                Please enter a valid phone number.
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mt-2">Email</div>
                                                        <div class="col-8 mt-2">
                                                            <input type="email" class="form-control inputs" name="email" id="email"
                                                                   placeholder="Enter email address">
                                                            <div class="invalid-feedback">
                                                                Please enter a valid email address.
                                                            </div>
                                                        </div>
                                                       
                                                       
                                                    </div>
                                                </div>
                                                <div class="col-2 d-none" id="properties">
                                                  
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="d-none" id="submitBtn"></button>
                            </form>
                         
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">

                    <table id="datatable" class="table align-middle" style="width:100%">
                        <thead>
                        <tr>
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Short Name</th>
                            <th>Full Name</th>
                            <th>Email ID</th>
                            <th>Contact</th>
                            <th>Website</th>
                            <th>Types</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

   <!-- Grids in modals -->
   <div class="modal zoomIn" id="addModal" aria-labelledby="addModalLabel" data-bs-backdrop="static"
   data-bs-keyboard="false" tabindex="-1" aria-modal="true">
   <div class="modal-dialog">
       <div class="modal-content">
           <div class="modal-header card-header pb-2">
               <h5 class="modal-title" id="addModalLabel"><span id="modal_title">Add </span> Property Status</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
           </div>
           <div class="modal-body">
               <form id="mainForm" class="needs-validation" novalidate>
                   <div class="row g-3">
                       <div class="col-4">
                           <label for="title" class="form-label">Status Title*</label>
                       </div>
                       <div class="col-8">
                           <input type="hidden" name="id" id="id" value="">
                           <input type="text" class="form-control" id="title" name="title"
                               placeholder="Enter title" required>
                           <div class="invalid-feedback">
                               Please enter the status name.
                           </div>
                       </div>
                       <div class="col-4">

                           <label for="title" class="form-label">Icon</label>
                       </div>
                       <div class="col-8">
                           <input type="text" class="form-control" id="icon" name="icon"
                               placeholder="ri line">
                           <div class="invalid-feedback">
                               Please enter the icon class.
                           </div>
                       </div>
                       <div class="col-4">
                           <label for="order" class="form-label"> Order</label>
                       </div>
                       <div class="col-8">
                           <input type="number" class="form-control" id="order" name="order"
                               placeholder="Enter order" required>
                           <div class="invalid-feedback">
                               Please enter the source order.
                           </div>
                       </div>
                       <div class="col-4">
                           <label for="badge" class="form-label"> Badge</label>
                       </div>
                       <div class="col-8">
                           <select id="statusBadge" class="form-select form-select-transparent" name="status_badge"
                               data-placeholder="...">
                               @include('admin.partials.badgeoptions')
                           </select>
                           <div class="invalid-feedback">
                               Please enter the status Badge.
                           </div>
                       </div>
                       <div class="col-4">
                           <label for="thumbnail" class="form-label">Thumbnail</label>
                       </div>
                       <div class="col-8">


                           <div class="mb-2  d-none" id="thumbnaildiv">
                               <img src="" alt="Image" id="thumb-img"
                                   class="img-thumbnail img-responsive" style="max-width:50px">
                               <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete" data-id=""
                                   id="thumbnail_id" data-key="" data-model="App\Models\PropertyStatus"></i>
                           </div>
                           <input type="file" class="form-control" id="thumbnail" name="thumbnail">
                           <div class="invalid-feedback">
                               Please enter the Amenity Thumbnail.
                           </div>
                       </div>
                       <div class="col-4">
                           <label for="thumbnail" class="form-label">Image</label>
                       </div>
                       <div class="col-8">
                           <div class="mb-2  d-none" id="imagediv">
                               <img src="" alt="Image" id="image-img"
                                   class="img-thumbnail img-responsive" style="max-width:50px">
                               <i class="ri-delete-bin-5-fill text-danger ms-2 generalDelete" data-id=""
                                   data-model="App\Models\PropertyStatus" data-key="" id="image_id"></i>
                           </div>

                           <input type="file" class="form-control" id="image" name="image">
                           <div class="invalid-feedback">
                               Please enter the Amenity image.
                           </div>
                       </div>
                       <div class="col-4">
                           <label class="form-check-label" for="statusCheckbox">Status</label>
                       </div>
                       <div class="col-8">
                           <div class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                               <input type="checkbox" name="status" class="form-check-input" id="statusCheckbox"
                                   checked>
                           </div>
                       </div>
                       <div class="col-lg-12">
                           <div class="hstack gap-2 justify-content-end">
                               <button type="button" class="btn btn-light action-close"
                                   data-bs-dismiss="modal">Close</button>
                               <button id="submitBtn" type="submit"
                                   class="btn btn-primary action-save">Submit</button>
                           </div>
                       </div>
                   </div>
               </form>
           </div>
       </div>
   </div>
</div>
{{-- @can('Property Statuses View') --}}
   <div class="modal zoomIn" id="viewModal" tabindex="-1" aria-labelledby="addModalLabel" data-bs-backdrop="static"
       data-bs-keyboard="false" tabindex="-1" aria-modal="true">
       <div class="modal-dialog">
           <div class="modal-content">
               <div class="modal-header card-header pb-2">
                   <h5 class="modal-title" id="addModalLabel">View Property Status</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
               </div>
               <div class="modal-body">
                   <div class="row g-3">
                       <div class="col-4">
                           <label for="view-title" class="form-label">Status Title:</label>
                       </div>
                       <div class="col-8" id="view-title"></div>
                       <div class="col-4">
                           <label class="form-check-label" for="statusCheckbox">Order:</label>
                       </div>
                       <div class="col-8" id="view-order">
                       </div>

                       <div class="col-4">
                           <label class="form-check-label" for="statusCheckbox">Icon class:</label>
                       </div>
                       <div class="col-8" id="view-icon">
                       </div>

                       <div class="col-4">
                           <label class="form-check-label" for="badge">badge:</label>
                       </div>
                       <div class="col-8" id="view-badge">
                       </div>
                       <div class="col-4 view_thumbnail">
                           <label class="form-check-label" for="thumbnailCheckbox">Thumbnail:</label>
                       </div>
                       <div class="col-8 view_thumbnail" id="view-thumbnail">
                       </div>

                       <div class="col-4 view_image">
                           <label class="form-check-label" for="view_image">Image:</label>
                       </div>
                       <div class="col-8 view_image" id="view_image">
                       </div>
                       <div class="col-4">
                           <label class="form-check-label" for="statusCheckbox">Status:</label>
                       </div>
                       <div class="col-8" id="view-status"></div>
                       <div class="col-lg-12">
                           <div class="hstack gap-2 justify-content-end">
                               <button id="deleteBtn" class="btn btn-danger action-delete">Delete</button>
                               <button id="editBtn" class="btn btn-info action-edit">Edit</button>
                               <button type="button" class="btn btn-light action-close"
                                   data-bs-dismiss="modal">Close</button>
                           </div>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
{{-- @endcan --}}
    {{-- @include('admin.contact.include.modal') --}}
@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        {{-- <script src=" "></script> --}}


    <script>
        var isUpdate = false;
        let dataObj = {};
        let defaultStatusId = 1;
        let defaultSubStatusId = 4;
        let filtersData = [];
        $(document).on("submit", "#searchForm", function (e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            dataTable.ajax.reload();
        })

        $(document).on("click", "#clearSearchBtn", function () {
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", "#refreshTableButton", function () {
            $("#searchSearchBtn").trigger("click")
        })

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        $(document).on("click", "#saveBtn", function (){
            $("#submitBtn").trigger("click")
        })



    

      

        function scrollAtBottom($el) {
            let element = document.getElementById($el);
            element.scrollTop = element.scrollHeight;
        }

      
       

      

       

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseContactForm").toggleClass("show")
                $('html, body').animate({scrollTop: 0}, 'slow');
                return
            }
            if (show) {
                $("#collapseContactForm").addClass("show");
                $('html, body').animate({scrollTop: 0}, 'slow');
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #bulkAssignBtn").addClass("d-none");
                $("#newBtn").removeClass("d-none");
                $("#collapseContactForm").removeClass("show");
            }
        }

        $("#newBtn").click(function () {
            @can("Contacts Create")
                isUpdate = false;
                $("#properties-table>tbody").html("");
                $("#properties").addClass("d-none");
            $('#mainForm').trigger('reset');
            $('#mainForm').removeClass('was-validated');
            $(".inputs").prop("disabled", false)
            $(".inputs").val("").trigger("change")
            $(".inputs").removeClass("is-invalid")
            $("#id").val("")
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #followUpMenusTab, #bulkAssignBtn").addClass("d-none");
            $("#addOnAgentDiv, .addHide").addClass("d-none");
            toggleForm(true)
            $("#col-logs").addClass("d-none");
            $("#start-call-button").removeClass("d-none");
            $("#calling-button").addClass("d-none");
            @endcan
        });

        $(document).on("click", "#closeBtn", function () {
            toggleForm(false)
        })

        $(document).on('submit', '#mainForm', function (event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{route('admin.property.developers.update')}}" : "{{route('admin.property.developers.store')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#saveBtn").prop('disabled', false); //enable submit button
                        $('#mainForm').trigger('reset'); //
                        $(".inputs").val("").trigger("change")
                        $(".inputs").removeClass("is-invalid")
                        viewContact(result.id)
                        dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate = true; // set isUpdate to false so that when we try to add new category it will be create request
                    } else if (result.status == '2') {
                        $("#saveBtn").prop('disabled', false);
                        let error_message = '';

                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $("#saveBtn").prop('disabled', false);
                        swal("Oops!", result.msg, "error");
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

        let dataTable = $('#datatable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            dom: 'Bfrtip',
            buttons: [{
                extend: 'colvis',
                titleAttr: 'Show/Hide',
                text: '<i class="colvis" style="font-size:09px">Show / Hide</i>',

            }],
            language: {
                searchPlaceholder: "Search Developers"
            },
            createdRow: function(row, data, index) {
                $('#datatable tbody').css('cursor', 'pointer');

            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{route('admin.property.developers.dataTable')}}",
                type: "post",
                data: function (d) {
                    d.deleted = filtersData?.search_deleted;
                },
                error: function () {
                    $(".users-error").html("");
                    $("#dataTable").append('<tbody class="users-error"><tr><th colspan="3">No data found in the server</th></tr></tbody>');
                    $("#dataTable_processing").css("display", "none");
                }
            },
            fnInitComplete: function (oSettings, json) {
                let first = $("#dataTable_wrapper .row div:first");
                first.addClass("col-md-5").removeClass("col-md-6")
                $(first).next().addClass("col-md-7").removeClass("col-md-6")
                $(".dataTables_filter label").addClass("d-flex justify-between-between align-items-center gap-2")
                $(`@includeIf("admin.contact.include.filterButtons")`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [
                {
                    data: null,
                    orderable: false,
                }, {
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'short_name',
                    name: 'short_name',
                }, {
                    data: 'full_name',
                    name: 'full_name',
                    orderable: false,
                }, {
                    data: 'email',
                    name: 'email',
                    orderable: false,
                }, {
                    data: 'phone',
                    name: 'phone',
                    orderable: false,
                }, {
                    data: 'website',
                    name: 'website',
                    orderable: false,
                }, {
                    data: null,
                    name: null,
                    orderable: false,
                }, {
                    data: null,
                    orderable: false,
                }, {
                    data: null,
                    orderable: false,
                }
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return `<div class="form-check">
                                                <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` + data.id + `">
                                            </div>`;
                    }
                },
                {
                    "targets": 2,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewContact('` + data.id + `')">` + data.short_name + `</a>`;
                    }
                },
                {
                    "targets": 7,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        
                        return '';
                    }
                },
                {
                    targets: 8,
                    orderable: false,
                    visible: true,
                    /*This will hide particular column*/
                    render: function (type, row, data, meta) {
                        let disabled = true;
                        @can("Contacts Edit")
                            disabled = false
                        @endcan

                        if (data.status == '1') {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success" dir="ltr">
                                        <input ${disabled ? "disabled" : ""} type="checkbox" data-id="${data.id}" data-do="0" name="status" class="form-check-input status-change" checked></div>`;
                        } else {
                            return `<div  class="form-check form-switch form-switch-lg form-switch-success " dir="ltr">
                                        <input ${disabled ? "disabled" : ""} data-id="${data.id}" data-do="1" type="checkbox" name="status" class="form-check-input status-change"></div>`;
                        }
                    }
                },
                {
                    targets: 9,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        return `<div class="dropdown d-inline-block">
                                    <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ri-more-fill align-middle"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a href="javascript:void(0)" data-id="` + data.id + `" class="dropdown-item edit-item-btn edit-link"><i class="ri-pencil-fill align-bottom me-2 text-muted"></i> Edit</a></li>
                                        <li>
                                            <a href="javascript:void(0)" onclick="deleteData(` + data.id + `)" data-id="` + data.id + `" class="dropdown-item remove-item-btn delete-link">
                                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i> Delete
                                            </a>
                                        </li>
                                    </ul>
                                </div>`
                    }
                }
            ]
        });

        $('#datatable tbody').on('change', 'input.status-change', function () {
            @can("Contacts Edit")
            let id = $(this).data('id');
            let action = $(this).data('do');
            let formData = {
                id: id,
                action: action
            }
            $.ajax({
                type: "POST",
                url: "{{route('admin.property.developers.update.status')}}",
                data: formData,
                dataType: 'json',
                success: function (data) {
                    if (data.status == '1') {
                        Toastify({

                            text: "Developer status updated successfully",

                            duration: 3000

                        }).showToast();
                        dataTable.row().draw();
                    }
                },
                error: function (data) {
                    console.log('Error:', data);
                }
            });
            @endcan
        });

        $('#datatable tbody').on('click', '.edit-link', function () {
            @can("Contacts Edit")
            let id = $(this).data('id');
            viewContact(id);
            editData(id);
            @endcan
        });
        
        $(document).on('click', '#editBtn', function (event) {
            @can("Contacts Edit")
            editData($('#id').val());
            @endcan
        });

        function viewContact(id) {
            @can("Contacts View")
            $("#addOnAgentDiv, #newBtn, #closeBtn, #followUpMenusTab, .addHide").removeClass("d-none");
            $("#saveBtn").addClass("d-none");
            $("#collapseContactForm").addClass("show");
            $("#editBtn, #bulkAssignBtn").removeClass("d-none");
            $(".inputs").prop("disabled", true);
            $("#mainForm").trigger("reset")
            $("#id").val(id)
            $(".inputs").val("").trigger("change")
            $(".inputs").removeClass("is-invalid")
            $("#meeting_contact_id, #task_contact_id").val(id)
            isUpdate = true; //set isUpdate to true
            getContact(id)
            $("#col-logs").removeClass("d-none")
            $("#start-call-button").removeClass("d-none");
            $("#calling-button").addClass("d-none");
            @endcan
        }

        function getContact(id){
            $.ajax({
                url: "{{route('admin.property.developers.get.data')}}",
                type: "get",
                data: {id: id},
                success: function (result) {
                    if (result.status == '1') {
                        dataObj = result.data;
                        Object.keys(result.data).map((index) => {
                            $(`#${index}`).val(result.data[index]).trigger("change")
                        })
                        if (result.data?.criteria != null) {
                            Object.keys(result.data?.criteria).map((index) => {
                                if (index != "id") {
                                    $(`#${index}`).val(result.data?.criteria[index]).trigger("change")
                                }
                            });
                        }
                        let contactTypes = [];
                        result?.data?.contact_types.map(item => {
                            contactTypes.push(item.id);
                        })
                        $(`#contact_types`).val(contactTypes).trigger("change")
                        toggleForm(true)
                        getContactNotes()
                        topFunction()
                        viewAgentList(id)
                        getCallLog(id)
                        getMeetingLog(id)
                        getTask(id)
                        getActivityLog(id)
                        getProperties(id)
                    }
                }
            });
        }

        function editData(id) {
            @can("Contacts Edit")
                isUpdate = true;
            $("#col-logs").addClass("d-none")
            $(".inputs").prop("disabled", false)
            $(".inputs").removeClass("is-invalid")
            $('#mainForm').removeClass('was-validated');
            $("#saveBtn, #closeBtn").removeClass("d-none")
            $("#editBtn, #newBtn, #deleteBtn, #bulkAssignBtn").addClass("d-none")
            getContact(id);
            @endcan
        }

        function deleteData(id) {
            @can("Contacts Delete")
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this developer details!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{route('admin.property.developers.delete')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {id},
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify("Developer has been deleted successfully", "success")
                                    dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    $('#viewModal').modal('hide');
                                } else { // something went wrong on backend
                                    swal("Whoops", result.msg, "error");
                                    showToastify(result.msg, "error")
                                }
                            },
                            error: function (result) {
                                console.log(result);
                            }
                        });
                    }
                });
            @endcan
        }

        function deleteMultiple() {
            @can("Contacts Delete")
            if (ids_array = [], document.getElementsByName("chk_child").forEach((function (e) {
                if (1 == e.checked) {
                    var t = e.value;
                    ids_array.push(t)
                }
            })), "undefined" != typeof ids_array && ids_array.length > 0) {

                swal({
                    title: "Attention!!",
                    text: "You are about to delete multiple contact. Once deleted, you will not be able to use them!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{route('admin.property.developers.delete.multiple')}}",
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    ids: ids_array
                                },
                                success: function (result) {
                                    if (result.status == '1') {
                                        showToastify(result.msg, "success");
                                        dataTable.row().draw();
                                    } else {
                                        showToastify(result.msg, "error");
                                    }
                                },
                                error: function (result) {
                                    console.log(result);
                                }
                            });
                            document.getElementById("checkAll").checked = !1
                        }
                    });

            } else{
                showToastify("Please select at least one checkbox", "error")
            }
            @endcan
        }

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })
        function highlightRow(row) {
            row.removeClass('selected');
            dataTable.$('tr.selected').removeClass('selected');
            row.addClass('selected');
        } // highlightRow

        dataTable.on('click', 'tbody tr', function(e) {
            var $cell = $(e.target).closest('td'),
                msg;
            //console.log('Index ==========='+$cell.index());
            if ($cell.index() != 0) {
                var data = dataTable.row(this).data();
                //
                if ($cell.index() === 8 || $cell.index() === 9) {
                    // console.log($cell);
                } else {
                    viewContact(data.id);
                    highlightRow($(this));

                }
            } // end if
        }); // table row click

        function updateMultiOption() {
            var countCheckedCheckboxes = $('input[name="chk_child"]:checked').length;
            console.log(countCheckedCheckboxes);
            $('#bulk_leads_count').html(countCheckedCheckboxes);
            $('#count_selected').html(countCheckedCheckboxes);
            if (countCheckedCheckboxes >= 1) {
                if ($('.multi_options').length === 0) {
                    $(`<div class="multi_options" style="display:inline-block; margin-left:10px;">
                          <div class="btn-group">
                            <button class="btn btn-primary btn-sm dropdown-toggle action-bulk" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Bulk Actions
                            </button>
                            <div class="dropdown-menu">
                      
                    @can('Developer Delete')
                    <a class="dropdown-item" href="javascript:;" onclick="deleteMultiple()">Delete</a>
                    @endcan
                    </div>
                </div>
           </div>`).insertAfter('.dt-buttons');
                }
            } else {
                $('.multi_options').remove();

            }
        }
        $("#checkAll").change(function() {
            $(".selectable-checkbox").prop("checked", this.checked)
            updateMultiOption();
        })
        $(document).on('change', 'tbody tr .selectable-checkbox', function() {

            $(this).parents('tr').toggleClass('selected');
            var checked = $(this).is(':checked');
            if (!checked) {
                $(this).prop('checked', false);
            } else {
                $(this).prop('checked', true);
            }
            updateMultiOption();
        });
        dataTable.on('draw.dt', function() {
            $(".chk_child").prop("checked", false);
            $('.multi_options').remove();
            $('#checkAll').prop("checked", false);
        });
        $("#sidebarParameters").addClass('collapse show');
        $("#sidebarDevelopers").addClass('active');  
        $("#propertyManagementSidebar").addClass('active');

    </script>
  
@endsection
