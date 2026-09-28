@extends('admin.layouts.master',['layout' => 'vertical'])
@section('title')
    Listings
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>

    <link rel="stylesheet" href="{{ URL::asset('assets/libs/lightbox2/css/lightbox.min.css') }}"/>
    <link href="{{ URL::asset('assets/libs/dropzone/dropzone.min.css') }}" rel="stylesheet">
@endsection
@section('content')

    <form id="mainForm" class="row needs-validation" novalidate>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Listings</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Property</a></li>
                            <li class="breadcrumb-item d-none" id="ref_no_area">
                                <div class="d-flex justify-content-start align-items-center">
                                    <span id="ref_no"></span>
                                    <a href="javascript:void(0)" onclick="copyToClipboard('#ref_no')" class="text-print"
                                       title="Copy to clipboard"> <i class="ri-clipboard-line mx-lg-2"
                                                                     style="font-size: 14px"></i> </a>
                                </div>
                            </li>
                        </ol>
                    </div>

                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can("Properties Create")
                                <button type="button" id="newBtn" class="btn btn-danger add-btn">
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                            @endcan
                            <button type="submit" class="btn btn-success d-none" id="saveBtn">Save</button>
                            @can("Properties Edit")
                                <button type="button" class="btn btn-primary d-none" id="editBtn">Edit</button>
                            @endcan
                            <button type="button" class="btn btn-light d-none" id="closeBtn">Close</button>
                            @can("Properties Delete")
                                <button type="button" class="btn btn-soft-danger" id="multiDeleteBtn"
                                        onClick="deleteMultiple()"><i
                                        class="ri-delete-bin-2-line"></i></button>
                            @endcan
                            @can("Properties Export")
                                <button type="button" class="btn btn-soft-success" id="exportBtn" title="Export to Excel"><i
                                        class="ri-file-excel-2-line"></i></button>
                            @endcan
                            <button type="button" id="dropdownMenuLink1" data-bs-toggle="dropdown" aria-expanded="false"
                                    class="btn btn-soft-info shadow-none d-none"><i class="ri-more-2-fill"></i></button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink1">
                                @can("Properties View")
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="preview-button"
                                           target="_blank">
                                            <i class="ri-window-2-line  align-bottom me-1"></i>Preview
                                        </a>
                                    </li>
                                @endcan
                                @if(auth()->user()->hasRole("Agent"))
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="publish-request-button">
                                            <i class="ri-checkbox-circle-line align-bottom me-1"></i>Request to publish
                                        </a>
                                    </li>
                                @endif
                                @if(auth()->user()->hasRole("Agent"))
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="unpublish-request-button">
                                            <i class="ri-close-circle-line align-bottom me-1"></i>Request to unpublish
                                        </a>
                                    </li>
                                @endif
                                @can("Properties Duplicate")
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="duplicate-button">
                                            <i class="ri-clipboard-line align-bottom me-1"></i>Duplicate
                                        </a>
                                    </li>
                                @endcan
                                @can("Properties Delete")
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="deleteBtn">
                                            <i class="ri-delete-bin-2-line align-bottom me-1"></i> Delete
                                        </a>
                                    </li>
                                @endcan
                                    @can("Properties Assign Agents")
                                    <li>
                                        <a class="dropdown-item" href="javascript:void(0)" id="bulk-assign-button">
                                            <i class="ri-user-add-line align-bottom me-1"></i> Assign to Agents
                                        </a>
                                    </li>
                                    @endcan
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            @include('admin.listing.includes.form')
        </div>
    </form>
    @includeIf("admin.listing.includes.filterForm")
    <div class="row d-none">
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Total Property</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                                            data-target="234">0</span>k</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0">
                                <i class="ri-arrow-up-line align-middle"></i> 17.32 %
                            </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-info rounded-circle fs-4">
                                    <i class="ri-ticket-2-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div> <!-- end card-->
        </div><!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Pending Verification</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                                            data-target="64.5">0</span>k</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0">
                                <i class="ri-arrow-down-line align-middle"></i> 0.87 %
                            </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-warning rounded-circle fs-4">
                                   <i class="mdi mdi-timer-sand"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div><!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Rented Property</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                                            data-target="116.21">0</span>K</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-danger mb-0">
                                <i class="ri-arrow-down-line align-middle"></i> 2.52 %
                            </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-success rounded-circle fs-4">
                                    <i class="ri-checkbox-circle-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div><!--end col-->
        <div class="col-xxl-3 col-sm-6">
            <div class="card card-animate">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="fw-medium text-muted mb-0">Expired Property</p>
                            <h2 class="mt-4 ff-secondary fw-semibold"><span class="counter-value"
                                                                            data-target="14.84">0</span>%</h2>
                            <p class="mb-0 text-muted"><span class="badge bg-light text-success mb-0">
                                <i class="ri-arrow-up-line align-middle"></i> 0.63 %
                            </span> vs. previous month</p>
                        </div>
                        <div>
                            <div class="avatar-sm flex-shrink-0">
                                <span class="avatar-title bg-danger rounded-circle fs-4">
                                    <i class="ri-delete-bin-line"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div><!-- end card body -->
            </div>
        </div><!--end col-->
    </div><!--end row-->

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="tasksList">
                <div class="card-body">
                    <div class="table-responsive mb-4">
                        @can("Properties List")
                            <table id="dataTable" class="table align-middle" style="width:100%">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 10px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                                   data-com.bitwarden.browser.user-edited="yes">
                                        </div>
                                    </th>
                                    <th>ID</th>
                                    <th>Ref</th>
                                    <th>Unit</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Beds</th>
                                    <th>Price</th>
                                    <th>Agent</th>
                                    <th>Updt By</th>
                                    <th>Updt At</th>
                                    <th width="10%">Portals</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                            </table>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>
    @includeIf("admin.listing.includes.modals")
@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>

    <script src="{{ URL::asset('assets/libs/dropzone/dropzone.min.js') }}"></script>
        {{-- <script src=" "></script> --}}

    <script src="{{ URL::asset('assets/libs/lightbox2/js/lightbox.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/jquery.priceformat/jquery.priceformat.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/cleave.js/cleave.js.min.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-sortablejs@latest/jquery-sortable.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key={{env("GOOGLE_MAP_KEY")}}&libraries=places&callback=Function.prototype"></script>
    <script>

        let galleryArr = [];
        let charLimit = 2000;
        let fullyLoaded = false;

        function initPriceInput(price = ""){
            $("#price").val(price).trigger("change");
            new Cleave('#price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        }

        initPriceInput()

        function initMap(lat, long, title) {
            const myLatLng = { lat:lat, lng: long };
            const map = new google.maps.Map(document.getElementById("google-map"), {
                zoom: 10,
                center: myLatLng,
            });

            new google.maps.Marker({
                position: myLatLng,
                map,
                title: title,
            });
            $("#mapModal").modal("show");
        }

        $(document).on("click", ".view-in-map", function (){
            let lat = $(this).data("lat");
            let long = $(this).data("long");
            let path = $(this).data("path");
            initMap(lat, long, path)
        })

        $(document).on("click", "#exportBtn", function (){
            $("#exportForm").removeClass("was-validated");
            $("#exportModal").modal("show")
        })

        $(document).on("change", "#select-all-export", function (){
            $(`.export-columns`).prop("checked", this.checked).trigger("change");
        })

        $(document).on("submit", "#exportForm", function (event) {
            event.preventDefault();
            let isSelected = $(".export-columns:checked").length > 0;
            if (!isSelected){
                showToastify("Please select at least one column.", "error");
                return;
            }
            let exportRoute = "{{ route('admin.listing.properties.export-excel') }}";
            let columns = $(this).serialize();

            window.open(`${exportRoute}?${columns}`);

            $("#exportForm").trigger("reset");
            $("#exportModal").modal("hide");
            return;
        })

        $(document).on("click", "#add-more-documents-btn", function (){
            let length = $(".other_documents").length;
            $("#more-documents").append(`<div class="col-lg-4 mt-2 other_documents-${length + 1}">
                                            <input type="text" id="other_documents-${length + 1}-title" name="other_documents[${length + 1}][title]"
                                                   class="form-control inputs" placeholder="Enter document titlte" required>
                                        </div>
                                        <div class="col-lg-8 mt-2 other_documents-${length + 1}">
                                            <input type="file" id="other_documents-${length + 1}-file" name="other_documents[${length + 1}][file]"
                                                   class="form-control inputs other_documents" required>
                                            <a href="javascript:void(0)" class="remove-other-documents" data-docid="other_documents-${length + 1}">Remove</a>
                                        </div>`)
        })
        $(document).on("click", ".remove-other-documents", function (){
            let id = $(this).data("docid");
            $(`.${id}`).remove()
        })

        $(document).ready(function (){

              


            $(function(){
                $(".summernote1").summernote({
                  height: 200,
                  toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['para', []],
                    ['view', ['codeview','fullscreen']]
                ],
                  placeholder:"Please enter description",
                  callbacks: {
                    onKeydown: function(e) {
                      var t = e.currentTarget.innerText;
                      if (t.length >= 2000) {
                        //delete key
                        if (e.keyCode != 8)
                          e.preventDefault();
                        // add other keys ...
                      }
                    },
                    onKeyup: function(e) {
                      var t = e.currentTarget.innerText;
                        $('#maxContentPost').text(t.length +"/"+ 2000);
                      
                    },
                    onPaste: function(e) {
                      var t = e.currentTarget.innerText;
                      var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
                      e.preventDefault();
                      var all = bufferText;
                      document.execCommand('insertText', false, all.trim().substring(0, 2000));
                      
                      $('#maxContentPost').text(t.length +"/"+ 2000);
                      
                    }
                  }
                });
              
            });
        })

        function getActivityLog(id){
            $("#activity-log-div").removeClass("d-none")
            $("#activity-log-div").find(".activity-timeline").html("");
            fullyLoaded = true;
            $.ajax({
                url: "{{route('ajax.activity-log')}}",
                type: 'GET',
                dataType: 'json',
                data: {subject_model: "Property", subject_id : id},
                success: function (result) {
                    let logs = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).fromNow()
                        let imageUrl = `${item.causer ? item.causer.thumb_url : "{{ URL::asset('assets/images/users/multi-user.jpg')}}"}`;
                        logs += `<div class="activity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="${imageUrl}" alt=""
                                             class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">${item.causer ? item.causer.name :"System"}</h6>
                                        <p class="text-muted mb-2">${item.description}</p>
                                        <small class="mb-0 text-muted">${date}</small>
                                    </div>
                                </div>`;
                    })
                    $("#activity-log-div").find(".activity-timeline").html(logs);
                    let element = document.getElementById("activities");
                    element.scrollTop = element.scrollHeight
                    fullyLoaded = true;
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }

        $(document).on("click", "#bulk-assign-button", function () {
            @can("Properties Assign Agents")
            $("#assignAgentsForm").removeClass("was-validated")
            $("#assignAgentsForm").trigger("reset");
            let agents = [];
            dataObj?.agents.map(item => {
                agents.push(item.id);
            })
            $("#assign_agents").val(agents).trigger("change")
            $("#assignAgentsModal").modal("show");
            @endcan
        })

        $(document).on("submit", "#assignAgentsForm", function (event) {
            event.preventDefault();
            let id = $("#id").val();
            var formData = new FormData($(this)[0]);
            formData.append("id", id);
            let buttons = $(this).find("button");
            $(buttons).prop("disabled", true);
            $.ajax({
                url: "{{route('admin.listing.properties.assign-agents')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    $(buttons).prop("disabled", false);
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        dataTable.row().order([10, 'DESC']).draw();
                        $("#assignAgentsModal").modal("hide");
                    } else if (result.status == '2') {
                        let error_message = '';
                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $(buttons).prop("disabled", false);
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
        })

        $(document).on("click", "#duplicate-button", function () {
            @can("Properties Duplicate")
            $("#duplicateForm").removeClass("was-validated")
            $("#duplicateForm").trigger("reset");
            $("#duplicate_for").val("").trigger("change")
            $("#duplicateModal").modal("show");
            @endcan
        })

        $(document).on("change", "#property_status_id", function (){
            let value = $("option:selected", this).val();
            if (["5", "7", "10"].indexOf(value) !== -1){
                $("#rented-area").removeClass("d-none")
            }else{
                $("#rented_at, #rented_until, #next_available, #remind").val("").trigger("change")
                $("#rented-area").addClass("d-none")
            }
        })
        $(document).on("click", "#publish-request-button", function (event) {
            $("#publishRequestForm").trigger("reset");
            $("#publishRequestForm").removeClass("was-validated");
            $("#publishRequestModal").modal("show");
            $("#request_consent_txt").html("I hereby mention that all the details filled and images, Title Deed, Owner's Passport copy and Signed Listing Form provided are 100% genuine and I hold the responsibility for the correctness of above-mentioned particulars.");
            $("#request_type").val("publish")
        })

        $(document).on("click", "#unpublish-request-button", function (event) {
            $("#publishRequestForm").trigger("reset");
            $("#publishRequestForm").removeClass("was-validated");
            $("#publishRequestModal").modal("show");
            $("#request_consent_txt").html("I hereby confirm that the listing no longer exists.");
            $("#request_type").val("unpublish")
        })

        $(document).on("submit", "#publishRequestForm", function (event) {
            event.preventDefault();
            let id = $("#id").val();
            let type = $("#request_type").val()
            var formData = new FormData($(this)[0]);
            formData.append("id", id);
            let buttons = $(this).find("button");
            $(buttons).prop("disabled", true);
            $.ajax({
                url: type == "publish" ? "{{route('admin.listing.properties.publish-request')}}" : "{{route('admin.listing.properties.unpublish-request')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    $(buttons).prop("disabled", false);
                    if (result.status == '1') {
                        showToastify(result.msg, "success");
                        dataTable.row().order([10, 'DESC']).draw();
                        $("#publishRequestModal").modal("hide");
                        viewProperty(id)
                    } else if (result.status == '2') {
                        let error_message = '';
                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $(buttons).prop("disabled", false);
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
        })

        $(document).on("submit", "#duplicateForm", function (event) {
            @can("Properties Duplicate")
            event.preventDefault();
            let buttons = $(this).find("button");
            $(buttons).prop('disabled', true);
            var formData = new FormData($(this)[0]);
            formData.append("id", $("#id").val());
            $.ajax({
                url: "{{route('admin.listing.properties.duplicate')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    $(buttons).prop('disabled', false);
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#duplicateModal").modal("hide")
                        dataTable.row().order([10, 'DESC']).draw();
                    } else if (result.status == '2') {
                        let error_message = '';
                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $(buttons).prop('disabled', false);
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
            @endcan
        })

        $('#photos-gallery').sortable({
            group: 'list',
            animation: 200,
            ghostClass: 'ghost',
            cursor: 'move',
            tolerance: 'pointer',
            onSort: function (event, ui) {
                let oldIndex = event.oldIndex;
                let newIndex = event.newIndex;
                let tmp = galleryArr[oldIndex]
                galleryArr[oldIndex] = galleryArr[newIndex]
                galleryArr[newIndex] = tmp
                reOrderPhotos()
            },
        });

        function reOrderPhotos() {
            let formData = new FormData();
            formData.append("ids", galleryArr);
            $.ajax({
                url: "{{route('admin.listing.properties.re-order-photos')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status != '1') {
                        getPhotos()
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
        }

        $('#title').maxlength({
            threshold: 15,
            placement: 'bottom-right',
            warningClass: "valid-tooltip",
            limitReachedClass: "invalid-tooltip",
        })

        function setRefNo(refNo = "") {
            $("#ref_no").text(refNo)
            if (refNo == "" || refNo == undefined) {
                $("#ref_no_area").addClass("d-none")
            } else {
                $("#ref_no_area").removeClass("d-none")
            }
        }

        $(document).ready(function () {
            if ($("#agent_id").hasClass("select2")){
                $('#agent_id').select2({
                    placeholder: 'Select agent',
                    allowClear: true
                });
            }

            $('#owner_id').select2({
                placeholder: 'Select owner',
                allowClear: true
            });

            $('#for').select2({
                placeholder: 'Select property category',
                allowClear: true
            });

            $('#property_category_id').select2({
                placeholder: 'Select property type',
                allowClear: true
            });

            $('#bed_id').select2({
                placeholder: 'Select beds',
                allowClear: true
            });

            $('#bath_id').select2({
                placeholder: 'Select baths',
                allowClear: true
            });

            // $('#location_id').select2({
            //     placeholder: 'Select location',
            //     allowClear: true
            // });

            $('#furnished').select2({
                placeholder: 'Select furnishing type',
                allowClear: true
            });

            $('#cheques').select2({
                placeholder: 'Select cheques',
                allowClear: true
            });

            $('#property_status_id').select2({
                placeholder: 'Select property status',
                allowClear: true
            });

            if ($("#contact_agent_id").hasClass("select2")){
                $('#contact_agent_id').select2({
                    placeholder: 'Select agent',
                    allowClear: true,
                    dropdownParent: $('#addContactModal')
                });
            }

            $('#gender').select2({
                placeholder: 'Select gender',
                allowClear: true,
                dropdownParent: $('#addContactModal')
            });

            $('#country_id').select2({
                placeholder: 'Select nationality',
                allowClear: true,
                dropdownParent: $('#addContactModal')
            });

            $('#contact_source_id').select2({
                placeholder: 'Select contact source',
                allowClear: true,
                dropdownParent: $('#addContactModal')
            });

            $('#duplicate_for').select2({
                placeholder: 'Select copy to',
                allowClear: true,
                dropdownParent: $('#duplicateModal')
            });

            $('#assign_agents').select2({
                placeholder: 'Select agents',
                allowClear: true,
                closeOnSelect: false,
                dropdownParent: $('#assignAgentsModal')
            });

            $('#search_agents').select2({
                placeholder: 'Select agents',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_owners').select2({
                placeholder: 'Select owners',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_categories').select2({
                placeholder: 'Select types',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_beds').select2({
                placeholder: 'Select beds',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_baths').select2({
                placeholder: 'Select baths',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_property_statuses').select2({
                placeholder: 'Select property statuses',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_for').select2({
                placeholder: 'Select categories',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_publish_statuses').select2({
                placeholder: 'Select publish statuses',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_portals').select2({
                placeholder: 'Select portals',
                allowClear: true,
                closeOnSelect: false,
            });

            $('#search_featured').select2({
                placeholder: 'Select featured',
                allowClear: true,
                closeOnSelect: false,
            });
            $('#search_communities').select2({
                placeholder: 'Select communities',
                allowClear: true,
                closeOnSelect: false,
            });

            $("#completion_status").change(function() {
                if($(this).val() == 'Off plan'){
                    $("#developerDiv").removeClass('d-none');
                }else{
                    $("#developerDiv").addClass('d-none');
                }
            })

            $('#search_locations').select2({
                placeholder: 'Select locations',
                allowClear: true,
                closeOnSelect: false,
                ajax: {
                    url: '{{ route('ajax.community-properties') }}',
                    dataType: 'json',
                    type: 'GET',
                    processResults({data}) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.path_name,
                                    id: item.id,
                                }
                            })
                        }
                    }
                }
            });

        });


        $(document).on("click", "#addContact", function () {
            @can("Contacts Create")
            $("#addContactForm select").val("").trigger("change")
            $("#addContactForm").trigger("reset")
            $("#addContactForm").removeClass("was-validated")
            let agent_id = $("#agent_id").val();
            $("#contact_agent_id").val(agent_id).trigger("change");
            $("#contact_type_id").val(3).trigger("change");
            $("#addContactModal").modal("show")
            @endcan
        })

        $(document).on("click", "#uploadPhotos", function () {
            $('div.dz-complete').remove();
            $("#uploadPhotosModal").modal("show")
        })

        let dataObj = {};
        let filtersData = [];

        let mobileInput = initPhoneValidator()
        let locations = locationSelect2("");
        let phoneInput = initPhoneValidator("phone", "full_phone")

        function checkContactExistence(value, column, model, id = "null") {
            let agent_id = $("#contact_agent_id").val();
            $(`#${column}-exists`).html("")
            $(`#${column}`).removeClass("is-invalid")
            $(`#${column}`).next(".invalid-feedback").text(`enter a valid ${column}.`);
            if (value.length > 0) {
                $.ajax({
                    url: "{{route('ajax.is-value-unique')}}",
                    type: "get",
                    data: {value, column, model, id, agent_id},
                    success: function (result) {
                        if (!result.data) {
                            $(`#${column}`).addClass("is-invalid")
                            $(`#${column}`).next(".invalid-feedback").text(`The ${column} has already been taken.`)
                            $(`#${column}-exists`).html(`This lead already exists <a href="javascript:void(0)" data-id="${result.id}" class="text-primary" id="request-access-${column}">request access</a>`)
                            showToastify(`The ${column} has already been taken.`, "error")
                        }
                    }
                });
            }
        }

        $(document).on("change", "#contact_agent_id", function () {
            checkContactExistence(mobileInput.getNumber(), "mobile", "Contact")
            checkContactExistence($("#email").val(), "email", "Contact")
        })

        $(document).on("change", "#email", function () {
            checkContactExistence($(this).val(), "email", "Contact")
        })

        $(document).on("change", "#mobile", function () {
            $("#whatsapp").val(mobileInput.getNumber()).trigger("change");
            checkContactExistence(mobileInput.getNumber(), "mobile", "Contact")
        })

        $(document).on("submit", "#searchForm", function (e) {
            e.preventDefault();
            filtersData = $("#searchForm").serialize();
            filtersData = urldecode(filtersData);
            filtersData = QueryStringToJSON(filtersData);
            dataTable.ajax.reload();
        })

        $(document).on('submit', '#addContactForm', function (event) {
            @can("Contacts Create")
            let buttons = $("#addContactForm button");
            $(buttons).prop('disabled', true);
            event.preventDefault();
            $("#mobile").val(mobileInput.getNumber())
            $("#phone").val(phoneInput.getNumber())
            @if(auth()->user()->hasRole("Agent"))
                let id = {{auth()->id()}};
                $("#agent_id_for_owner").val(id);
            @endif
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: "{{route('admin.work.contacts.store')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $(buttons).prop('disabled', false); //enable submit button
                        $("#addContactForm").find("select, input").val("").trigger("reset")
                        $("#addContactForm").trigger("reset")
                        $("#addContactModal").modal("hide")
                        let agent_id = "";
                        agent_id = $("#agent_id").val();
                        getOwners(agent_id, result.id)
                    } else if (result.status == '2') {
                        $(buttons).prop('disabled', false);
                        let error_message = '';
                        $.each(result.errors, function (key, value) {
                            error_message += '' + key + ': ' + value + ' | ';
                        });
                        showToastify(error_message, "error")
                    } else {
                        $(buttons).prop('disabled', false);
                        showToastify(result.msg, "error")
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    $(buttons).prop('disabled', false);
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
            @endcan
        });

        $(document).on("click", ".select-portal", function () {
            let id = $(this).data("id");
            let selected = [];
            $("#search_portals option:selected").map((ind, el) => {
                selected.push($(el).val())
            });
            const index = selected.indexOf(id.toString());
            if (index == -1) {
                selected.push(id);
            } else {
                selected.splice(index, 1);
            }
            $(this).toggleClass("filters-selected");
            $("#search_portals").val(selected).trigger("change")
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", ".select-publish-status", function () {
            let status = $(this).data("status");
            if (status == "PUBLISHED") {
                if ($(this).find("i").hasClass("ri-checkbox-circle-line")) {
                    $(this).find("i").removeClass("ri-checkbox-circle-line").addClass("ri-checkbox-circle-fill")
                } else {
                    $(this).find("i").removeClass("ri-checkbox-circle-fill").addClass("ri-checkbox-circle-line")
                }
            }

            if (status == "DRAFT") {
                if ($(this).find("i").hasClass("ri-edit-circle-line")) {
                    $(this).find("i").removeClass("ri-edit-circle-line").addClass("ri-edit-circle-fill")
                } else {
                    $(this).find("i").removeClass("ri-edit-circle-fill").addClass("ri-edit-circle-line")
                }
            }

            if (status == "UNPUBLISHED") {
                if ($(this).find("i").hasClass("ri-close-circle-line")) {
                    $(this).find("i").removeClass("ri-close-circle-line").addClass("ri-close-circle-fill")
                } else {
                    $(this).find("i").removeClass("ri-close-circle-fill").addClass("ri-close-circle-line")
                }
            }
            let selected = [];
            $("#search_publish_statuses option:selected").map((ind, el) => {
                selected.push($(el).val())
            })
            const index = selected.indexOf(status);
            if (index == -1) {
                selected.push(status);
            } else {
                selected.splice(index, 1);
            }
            $("#search_publish_statuses").val(selected).trigger("change")
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", "#clearSearchBtn", function () {
            $(".search-inputs").val("").trigger("change");
            $("input[type='checkbox'].search-inputs").prop("checked", false).trigger("change");
            $("#searchSearchBtn").trigger("click")
        })

        $(document).on("click", "#refreshTableButton", function () {
            $("#searchSearchBtn").trigger("click")
        })

        let dropzone = new Dropzone('#photos-dropzone', {
            url: '{{ route("admin.listing.properties.upload.photo") }}',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            },
            acceptedFiles: ".jpeg,.jpg,.png",
            addRemoveLinks: true,
            timeout: 50000,
            paramName: "file", // The name that will be used to transfer the file
            maxFilesize: 3, // MB
            dictDefaultMessage: 'Drag images here to upload, or click to select images',
            init: function () {
                this.on("success", function (file, response) {
                    let res = JSON.parse(response)
                    file.media_id = res.data.id;
                });
            },
            sending: function (file, xhr, formData) {
                formData.append('temp_id', $('#temp_id').val());
                formData.append('property_id', $('#id').val());
            },
            removedfile: function (file) {
                let media_id = file.media_id;
                removePhoto(media_id);
                var fileRef;
                return (fileRef = file.previewElement) != null ? fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            success: function (file, response) {
                $(file.previewElement).find('.dz-success-mark').css('opacity', '1');
            },
            error: function (file, response) {
                console.log("error: ", response);
                $(file.previewElement).find('.dz-error-mark').css('opacity', '1');
                $(file.previewElement).find('.dz-error-message').css('opacity', '1').css('display', 'block');
                $(file.previewElement).find('.dz-error-message').text(response);
                return false;
            }
        });

        $(document).on("click", ".delete-photo", function () {
            removePhoto($(this).data("id"), this);
        })


        function removePhoto(media_id, removeElement = null) {

            galleryArr = jQuery.grep(galleryArr, function(value) {
              return value != media_id;
            });
            const remove = () => {
                $(removeElement).closest(".photo-element").remove();
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
                type: 'POST',
                url: '{{ route("admin.listing.properties.remove.photo") }}',
                data: {media_id},
                success: function (data) {
                    if (removeElement != null) {
                        remove()
                    }
                    console.log("File has been successfully removed!!");
                },
                error: function (e) {
                    console.log(e);
                }
            });
        }

        var isUpdate = false;

        function toggleAdvanceSearch() {
            $("#collapseFilterForm").toggleClass("show")
        }

        function makeFirstOptionSelected() {
            $("select.inputs").each((index, item) => {
                let id = $(item).attr("id")
                if (id != "features") {
                    $("option:first", item).prop('selected', 'selected').trigger('change');
                }
            })
        }
        // function registerSummernote(element, placeholder, max, callbackMax, callbackMin, data) {
        //         $(element).summernote({
        //           height: 200,
        //           toolbar: [
        //             ['style', ['style']],
        //             ['font', ['bold', 'underline', 'clear']],
        //             ['para', ['ul', 'ol', 'paragraph']],
        //             ['view', ['codeview','fullscreen']]
        //         ],
        //           placeholder,
        //           callbacks: {
        //             onKeydown: function(e) {
        //               var t = e.currentTarget.innerText;
        //               if (t.length >= max) {
        //                 //delete key
        //                 if (e.keyCode != 8)
        //                   e.preventDefault();
        //                 // add other keys ...
        //               }
        //             },
        //             onKeyup: function(e) {
        //               var t = e.currentTarget.innerText;
        //               if (typeof callbackMax == 'function') {
        //                 callbackMax(t.length +"/"+ max);
        //               }
        //             },
        //             onPaste: function(e) {
        //               var t = e.currentTarget.innerText;
        //               var bufferText = ((e.originalEvent || e).clipboardData || window.clipboardData).getData('Text');
        //               e.preventDefault();
        //               var all = t + bufferText;
        //               document.execCommand('insertText', false, all.trim().substring(0, 2000));
        //               if (typeof callbackMax == 'function') {
        //                  callbackMax(t.length +"/"+ max);
        //               }
        //             }
        //           }
        //         },data);
        //       }

        $(document).on("click", "#newBtn", function () {
            @can("Properties Create")
                $("#addContact").removeClass("d-none")
            setRefNo()
            fullyLoaded = false;
            $('#mainForm').removeClass('was-validated');
            // registerSummernote('.summernote1', 'Please enter description', 2000,function(max) {
            //     $('#maxContentPost').text(max) 
            //   },"");
            $(".summernote1").summernote("code", "");
            $(".summernote1").summernote("enable");
            $('#maxContentPost').text(0 +"/"+ 2000)
            $("#location_address").html("")
            $("#location_type").val("")
            makeFirstOptionSelected();
            $("#temp_id").val(Date.now() + '-' + (Date.now().toString(36) + Math.random().toString(36)).toUpperCase());
            $("#id").val("")
            isUpdate = false;
            dataObj = {};
            $(".inputs").prop("disabled", false)
            $("#mainForm").trigger("reset")
            resetDocumentLinks();
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn, #dropdownMenuLink1, #view-edit-mode").addClass("d-none");
            toggleForm(true)
            $("#activity-log-div").addClass("d-none")
            $("#more-documents").html("")
            @endcan
        });

        $(document).on("click", "#editBtn", function () {
            @can("Properties Edit")
                fullyLoaded = false;
            $("#temp_id").val("");
            $(".inputs").prop("disabled", false)
            $('#mainForm').removeClass('was-validated');
            $("#price_on_application").prop("disabled", false)
            editData($('#id').val());
            $('.summernote1').summernote('enable');
            
            // $("#saveBtn, #closeBtn").removeClass("d-none");
            // $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn, #dropdownMenuLink1").addClass("d-none");
            toggleForm(true)
            @endcan
        });

        $(document).on("click", "#closeBtn", function () {
            toggleForm(false)
        })

        $(document).on('click', '.edit-link', function (event) { //edit link click event it will fetch single cateogry details and put it on form
            @can("Properties Edit")
            $(".inputs").prop("disabled", false)
            let id = $(this).data('id');
            editData(id);
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn, #dropdownMenuLink1").addClass("d-none");
            toggleForm(true)
            @endcan
        });

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        $("#for").on("change", function (){
            $("#cheques").val("").trigger("change");
            let isRent = ["Residential for rent", "Commercial for rent"].indexOf($(this).val()) != -1;
            if (isRent){
                $("#cheques-section").removeClass("d-none");
                $('#hideprice-section').addClass("d-none");
            }else{
                $("#cheques-section").addClass("d-none");
                $('#hideprice-section').removeClass("d-none");
            }
        })

        $(document).on("change", "#location_id", function (){
            $("#location_address").html("")
            $("#location_type").val("")
           // let selectedData = $(this).select2('data')[0];
            let lat = $('#location_id option:selected').attr('data-lat') 
            let long = $('#location_id option:selected').attr('data-long')
            let path = $('#location_id option:selected').attr('data-path')
            let type = $('#location_id option:selected').attr('data-type')
            //lat = lat == 'undefined' ? selectedData.lat : '';
          // selectedData = JSON.parse(selectedData);
           // console.log(selectedData)
            if (lat && long){
                $("#location_address").html(`<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}</a>`);
                $("#location_type").val(type)
            }
        })

        $('#location_id').on('select2:select', function (e) {
            $("#location_address").html("")
            $("#location_type").val("")
    var data = e.params.data;
   // console.log(data.long);
        let lat = data.lat;
            let long = data.long;
            let path = data.path;
            let type = data.type;
            if(path!=null){
     $("#location_address").html(`<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}</a>`);
            }           
     $("#location_type").val(type)
});

       function locationSelect2(id) {
              $("#location_address").html("")
            $("#location_type").val("")
             $('#location_id').select2({
                placeholder: 'Tower, community, locality',
                ajax: {
                    url: '{{route("ajax.sub.community-properties.select2")}}?id='+id,
                    dataType: 'json',
                    type: 'GET',
                    processResults({data}) {
                        let options = '';
                        return {
                            results: $.map(data, function (item) {

                                return  {
                                                text: `${item.text}`,
                                                id: item.id,
                                                'lat' : item.lat,
                                                'long' : item.long,
                                                'path' : item.path,
                                                'type' : item.type
                                            }

                                
                                
                            })
                        }
                    }
                }
            }); 
        }
  

        $(document).on("change", "#agent_id", function () {
            let user_id = $(this).val();
           
            $("#location_id").val("").trigger("change");
            $("#location_id").html("<option value='' selected disabled>Select location</option>");
            let property_id = dataObj.id;
            if (user_id) {
                $.ajax({
                    url: "{{route('ajax.user-sub-community-properties')}}?property_id="+property_id,
                    type: 'GET',
                    dataType: 'json',
                    data: {user_id : user_id},
                    success: function (result) {
                        let locationSelected = dataObj?.community_property_id || dataObj?.sub_community_id;
                        locationSelect2(dataObj.id);
                        let options = "";
                        result?.data.map(item => {
                            if (item.community_properties.length > 0){
                                item.community_properties?.map(item1 => {
                                    options += `<option value="${item1.id}" data-type="community_property" data-lat="${item1.latitude}" data-long="${item1.longitude}" data-path="${item1.path_name}">${item1.path_name}</option>`
                                })
                            }else {
                                options += `<option value="${item.id}" data-type="sub_community" data-lat="${item.latitude}" data-long="${item.longitude}" data-path="${item.path_name}">${item.path_name}</option>`
                            }
                        })
                      
                        $("#location_id").append(options)
                     
                        $("#location_id").val(dataObj?.community_property_id || dataObj?.sub_community_id).trigger("change")
                  //  $("#location_address").html(`<a class="view-in-map" href="javascript:void(0)" data-path="${path}" data-lat="${lat}" data-long="${long}">${path}</a>`);
                  //  $("#location_type").val(type)
                   
                       // locationSelect2();
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
        })

        $(document).on("click", "#addNoteButton", function () {
            @can("Property Notes Create")
            let id = $("#id").val();

            if (id == "") {
                showToastify("Please save record before adding a note.", "error")
                return;
            }

            let note = $("#note").val();
            if (note == "") {
                return;
            }

            var formData = new FormData();
            formData.append("property_id", id)
            formData.append("note", note)
            $.ajax({
                url: "{{route('admin.listing.properties.add.note')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#note").val("").trigger("change");
                        getPropertyNotes()
                    }
                },
                error: function (jqXhr, json, errorThrown) {
                    let data = jqXhr.responseJSON;
                    if (data.errors) {
                        $.each(data.errors, function (index, item) {
                            showToastify(item[0], "error")
                        })
                    }
                    if (jqXhr.status == 500 || jqXhr.status == 400) {
                        showToastify(data.msg, "error")
                    }
                },
            });
            @endcan
        })

        function getOwners(agent_id, owner_id = "") {
            $("#owner_id").val("").trigger("change");
            $("#owner_id").html(`<option value='' selected disabled>Select owner</option>`);
            if (agent_id) {
                $.ajax({
                    url: "{{route('ajax.owners')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {agent_id},
                    success: function (result) {
                        let options = "<option value='' selected disabled>Select owner</option>";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.first_name} ${item.last_name}<${item.mobile}></option>`
                        })
                        $("#owner_id").html(options)
                        $("#owner_id").val(owner_id || dataObj?.owner_id).trigger("change");
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
        }

        function getPropertyNotes() {
            @can("Property Notes List")
            let property_id = $("#id").val();
            $("#notes").html("");
            $.ajax({
                url: "{{route('ajax.property-notes')}}",
                type: 'GET',
                dataType: 'json',
                data: {property_id},
                success: function (result) {
                    let notes = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).format("DD/MM/YY HH:mm")
                        let imageUrl = `${item.created_by ? item.created_by.thumb_url : "{{ URL::asset('assets/images/users/avatar-8.jpg')}}"}`;
                        notes += `<div class="d-flex mb-0">
                                        <div class="flex-shrink-0">
                                            <img src="${imageUrl}" alt=""
                                                 class="avatar-xs rounded-circle"/>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-13"><a href="javascript:void(0)">${item.created_by ? item.created_by.name : "System"}</a> <small
                                                    class="text-muted">${date}</small></h5>
                                            <p class="text-muted">${item.note}</p>
                                        </div>
                                    </div>`
                    })
                    $("#notes").html(notes)
                    let element = document.getElementById("notes");
                    element.scrollTop = element.scrollHeight
                },
                error: function (result) {
                    console.log(result);
                }
            });
            @endcan
        }

        function getAgentPortalCounts(agent_id) {
            $(".agent-portals-counts").removeClass("text-danger").html("");
            if (agent_id) {
                $.ajax({
                    url: "{{route('ajax.agent-allocations')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {agent_id},
                    success: function (result) {
                        result.data.map(item => {
                            if (!isUpdate) {
                                if (item.allocation > item.count) {
                                    $(`#portal-switch-${item.id}`).prop("disabled", false);
                                } else {
                                    $(`#portal-switch-${item.id}`).prop("disabled", true);
                                }
                            } else {
                                const checked = $(`#portal-switch-${item.id}`).is(':checked');
                                if (item.allocation > item.count || checked) {
                                    $(`#portal-switch-${item.id}`).prop("disabled", false);
                                } else {
                                    $(`#portal-switch-${item.id}`).prop("disabled", true);
                                }
                            }
                            $(`#agent-portal-${item.id}-count`).html(`${item.count}/${item.allocation}`)
                        })
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
        }

        $(document).on("change", "#agent_id", function () {
            let agent_id = $(this).val();
            getOwners(agent_id)
            getAgentPortalCounts(agent_id)
        })

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseUserForm").toggleClass("show")
                return
            }
            if (show) {
                $("#collapseUserForm").addClass("show");
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #deleteBtn, #dropdownMenuLink1").addClass("d-none");
                $("#newBtn, #multiDeleteBtn").removeClass("d-none");
                $("#collapseUserForm").removeClass("show");
                $("#photos-gallery").html("")
                setRefNo()
            }
        }

        $(document).on('submit', '#mainForm', function (event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{route('admin.listing.properties.update')}}" : "{{route('admin.listing.properties.store')}}",
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
                        $("select.inputs").val("").trigger("change")
                        viewProperty(result.id)
                        dataTable.row().order([10, 'DESC']).draw(); // redraw the datatable show we don't need to reload the page to see the changes
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

        let dataTable = $('#dataTable').DataTable({ //datatable initialization
            "order": [[10, "DESC"]],
            dom: 'Bfrtip',
            buttons: [
                'colvis'
            ],
            language: {
                searchPlaceholder: "Search listing"
            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{route('admin.listing.properties.dt')}}",
                type: "post",
                data: function (d) {
                    d.agents = filtersData?.search_agents;
                    d.owners = filtersData?.search_owners;
                    d.categories = filtersData?.search_categories;
                    d.beds = filtersData?.search_beds;
                    d.baths = filtersData?.search_baths;
                    d.type = filtersData?.search_type;
                    d.unit_no = filtersData?.search_unit_no;
                    d.communities = filtersData?.search_communities;
                    d.locations = filtersData?.search_locations;
                    d.property_statuses = filtersData?.search_property_statuses;
                    d.price = filtersData?.search_price;
                    d.publish_statuses = filtersData?.search_publish_statuses;
                    d.portals = filtersData?.search_portals;
                    d.featured = filtersData?.search_featured;
                    d.for = filtersData?.search_for;
                    d.created_from = filtersData?.search_created_from;
                    d.created_to = filtersData?.search_created_to;
                    d.hot = filtersData?.search_hot;
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
                $(`@includeIf("admin.listing.includes.filterButtons")`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: 'id',
                    name: 'id',
                    visible: false
                },
                {
                    data: 'ref_no',
                    name: 'ref_no',

                }, {
                    data: "unit_no",
                    name: "unit_no",
                },
                {
                    data: "property_category_id",
                    name: "property_category_id",
                },
                {
                    data: "sub_community_id",
                    name: "sub_community_id",
                },
                {
                    data: "bed_id",
                    name: "bed_id",
                },
                {
                    data: null,//price
                    orderable: false,
                },
                {
                    data: 'agent_id',
                    name: 'agent_id',
                },
                {
                    data: 'updated_by_id',
                    name: 'updated_by_id',
                },
                {
                    data: 'updated_at',
                    name: 'updated_at',
                },
                {
                    data: null,
                    orderable: false,
                },
                {
                    data: null,
                    orderable: false,
                }
            ],
            "columnDefs": [
                {
                    "targets": 0,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return `<div class="form-check"><input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="${data.id}"></div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return data.id;
                    }
                },

                {
                    "targets": 2,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return `<a href="javascript:void(0)" class="text-primary" onclick="viewProperty(` + data.id + `)">` + data.ref_no + `</a>`;
                    }
                },
                {
                    "targets": 4,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return data.property_category ? data.property_category.title : "-";
                    }
                },
                {
                    "targets": 5,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        if (data.community_property){
                            return data.community_property.path_name;
                        }
                        if (data.sub_community){
                            return data.sub_community.path_name;
                        }
                        if (data.community){
                            return data.community.path_name;
                        }
                        return "-";
                    }
                }, {
                    "targets": 6,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return data.bed ? data.bed.name : "-";
                    }
                },
                {
                    "targets": 7,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return data.price && data.price.price ? data.price.price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",") : "-";
                    }
                },
                {
                    "targets": 8,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        if (!data.agent) return "-";
                        return `<div class="d-flex align-items-center" title="${data.agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.agent.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.agent.first_name}</div>
                                </div>`;
                    }
                },
                {
                    "targets": 9,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        if (!data.updated_by) return "-";
                        return `<div class="d-flex align-items-center"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}</div>
                                </div>`;
                    }
                }, {
                    "targets": 10,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        if (data.updated_at == null) {
                            return '-';
                        } else {
                            return moment(data.updated_at).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }
                }, {
                    "targets": 11, //portals
                    orderable: true,
                    render: function (type, row, data, meta) {
                        let portals = "";
                        data?.portals.map(item => {
                            if (item.portal) {
                                portals += `<a href="javascript:void(0)" class="remove-portal-button" data-id="${data.id}" data-portal="${item.portal_id}" title="${item.portal.name}">
                                        <img src="${item.portal.thumbnail}" alt="" style="height: 20px; width: 20px">
                                    </a>`;
                            }
                        })
                        return portals;
                    }
                },
                {
                    targets: 12,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        let publishStatus = `<span class="badge badge-soft-${data.publish_status == "PUBLISHED" ? "success" : (data.publish_status == "DRAFT" ? "primary" : "danger")}">${data.publish_status}</span>`
                        let propertyStatus = data.property_status ? data.property_status.title : "";
                        return `<div class="d-flex justify-content-between gap-2 align-items-center">
                                    <div class="text-center">${publishStatus}<br>
                                        <span>${propertyStatus}</span>
                                    </div>
                                </div>`;
                    }
                }
            ]
        });

        $(document).on("click", ".remove-portal-button", function () {
            return;
            @can("Properties Edit")
            let property_id = $(this).data("id");
            let portal_id = $(this).data("portal");

            $.ajax({
                url: "{{route('admin.listing.properties.remove.portal')}}",
                type: 'POST',
                dataType: 'json',
                data: {property_id, portal_id},
                success: function (result) {
                    dataTable.row().draw();
                },
                error: function (result) {
                    console.log(result);
                }
            });
            @endcan
        })


        $(document).on("click", ".toggle-hot-button", function () {
            @can("Properties Edit")
            let property_id = $(this).data("id");
            $.ajax({
                url: "{{route('admin.listing.properties.toggle.hot')}}",
                type: 'POST',
                dataType: 'json',
                data: {property_id},
                success: function (result) {
                    dataTable.row().order([10, 'DESC']).draw();
                },
                error: function (result) {
                    console.log(result);
                }
            });
            @endcan
        })

        function getPropertyDocuments() {
            resetDocumentLinks();

            let temp_id = $("#temp_id").val();
            let property_id = $("#id").val();
            galleryArr = [];

            if (property_id != "") {
                $.ajax({
                    url: "{{route('ajax.property-documents')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {temp_id, property_id},
                    success: function (result) {
                        let data = result.data;
                        if (data.floor_plan) {
                            $("#floor_plan_file").html(`<img src="${data.floor_plan.thumbnail}" />`)
                        }

                        if (data.title_deed) {
                            $("#title_deed_file").html(`<a href="${data.title_deed.path}" target="_blank">View Document</a>`)
                        }

                        if (data.owner_passport_copy) {
                            $("#owner_passport_copy_file").html(`<a href="${data.owner_passport_copy.path}" target="_blank">View Document</a>`)
                        }

                        if (data.signed_listing_form) {
                            $("#signed_listing_form_file").html(`<a href="${data.signed_listing_form.path}" target="_blank">View Document</a>`)
                        }
                        let otherDocuments = "";
                        let documentLength = $(".other_documents").length;
                        data.other_documents?.map(item => {
                            documentLength++;
                            otherDocuments += `<div class="col-lg-4 mt-2">
                                            <input type="text" id="other_documents-${documentLength}-title" name="other_documents[${documentLength}][title]"
                                                   class="form-control inputs doc-disabled" placeholder="Enter document titlte" value="${item.title}">
                                        </div>
                                        <div class="col-lg-8 mt-2 other_documents-${documentLength}">
                                            <input type="file" id="other_documents-${documentLength}-file" name="other_documents[${documentLength}][file]"
                                                   class="form-control inputs other_documents doc-disabled">
                                            <div><a href="${item.path}" target="_blank">View Document</a></div>
                                        </div>`
                        })
                        $("#more-documents").html(otherDocuments)
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
        }

         function resetDocumentLinks(){
            $("#title_deed_file").html("");
            $("#owner_passport_copy_file").html("");
            $("#signed_listing_form_file").html("");
            $("#floor_plan_file").html("");
        }

        function renderCreatedUpdated(data) {
            $("#view-edit-mode").removeClass("d-none");
            $("#view-edit-mode>.card-body").html("");
            let div = ""
            if (data.created_by) {
                div = `<div class="d-flex justify-content-between align-items-center">
                      <div>
                          <strong>Created By</strong>
                          <div class="d-flex align-items-center">
                              <div class="flex-shrink-0 me-2">
                                  <img src="${data.created_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                              </div>
                              <div class="flex-grow-1">${data.created_by.name}</div>
                          </div>
                      </div>
                      <div>
                          <strong>Created At</strong>
                          <br>
                          <span>${moment(data.created_at).format('MM/DD/YYYY hh:mm:ss')}</span>
                      </div>
                  </div>`
            }
            if (data.updated_by) {
                div += `<hr><div class="d-flex justify-content-between align-items-center">
                      <div>
                          <strong>Updated By</strong>
                          <div class="d-flex align-items-center">
                              <div class="flex-shrink-0 me-2">
                                  <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                              </div>
                              <div class="flex-grow-1">${data.updated_by.name}</div>
                          </div>
                      </div>
                      <div>
                          <strong>Updated At</strong>
                          <br>
                          <span>${moment(data.updated_at).format('MM/DD/YYYY hh:mm:ss')}</span>
                      </div>
                  </div>`
            }
            $("#view-edit-mode>.card-body").html(div);
        }

        function getProperty(id) {
            fullyLoaded = false;
            makeFirstOptionSelected();
            $("#more-documents").html("")
            $.ajax({
                url: "{{route('admin.listing.properties.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id},
                success: function (result) {
                    if (result.status == '1') {
                        $('#id').val(result.data.id);
                        if (result.data) {
                            Object.keys(result.data).map((index) => {
                                $(`#${index}`).val(result.data[index]).trigger("change")
                            })
                            $(".form-check-input.inputs").prop("checked", false)
                            result.data?.portals.map(item => {
                                $(`#portal-switch-${item.portal_id}`).prop("checked", true)
                            })
                            if(result.data.price && result.data.price.price_on_application){
                                $(`#price_on_application`).prop("checked", true)
                            }else{
                                $(`#price_on_application`).prop("checked", false)
                            }

                            result.data?.amenities.map(item => {
                                $(`#feature-${item.property_amenity_id}`).prop("checked", true).trigger("change")
                            })
                            //property description needed to add on ckeditor
                            $('.summernote1').summernote("code", result.data.description);
                            $('#maxContentPost').text(result.data.description ? result.data.description.length+"/"+ 2000 : 0 +"/"+ 2000)
                            initPriceInput(result.data?.price?.price)
                            $("#property_status_id").val(result.data.property_status_id).trigger("change");

                            let previewUrl = "{{ route("admin.listing.properties.view", ["property" => 1]) }}";
                            previewUrl = previewUrl.slice(0, -1);
                            $("#preview-button").attr("href", `${previewUrl}${result.data.ref_no}`)
                            dataObj = result.data;
                            $("#agent_id").trigger("change");
                            setRefNo(result.data.ref_no)
                            renderCreatedUpdated(result.data)
                            getActivityLog(id)
                            toggleForm(true)
                            scrollTop()
                            if (dataObj.publish_status == "DRAFT" || dataObj.publish_status == "UNPUBLISHED"){
                                $("#publish-request-button").closest("li").removeClass("d-none")
                            }
                            if (dataObj.publish_status == "PUBLISHED"){
                                $("#unpublish-request-button").closest("li").removeClass("d-none")
                            }
                        }
                    } else {
                        showToastify(result.msg, "error")
                    }
                },
                error: function (result) {
                    console.log(result);
                }
            });
        }

        function viewProperty(id) {
            $("#publish-request-button").closest("li").addClass("d-none")
            $(this).closest("tr").addClass("table-active")
            $("#unpublish-request-button").closest("li").addClass("d-none")
            $('#price_on_application').prop("disabled", true)
            @can("Properties View")
                isUpdate = true;
            fullyLoaded = false;
                $("#addContact").addClass("d-none")
            getProperty(id)
            $(".inputs").prop("disabled", true)
            $("#id").val(id)
            $("#temp_id").val("")
            getPhotos();
            getPropertyDocuments()
            getPropertyNotes()
            $('.summernote1').summernote("disable");
            $("#saveBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
            let timeInterval = setInterval(() => {
                if (fullyLoaded){
                    $("#editBtn, #closeBtn, #deleteBtn, #dropdownMenuLink1").removeClass("d-none");
                    clearInterval(timeInterval);
                }
            }, 500);
            @endcan
        }

        function editData(id) {
            @can("Properties Edit")
            $("#temp_id").val("");
            $("#addContact").removeClass("d-none")
            isUpdate = true;
            fullyLoaded = false;
            getProperty(id);
            getPhotos();
            getPropertyDocuments()
            getPropertyNotes()
            @can('Properties Status Change')
            $("#property_status_id").prop("disabled", false)
            @else
             $("#property_status_id").prop("disabled", true)
            @endcan
             @can('Properties Publish Status Change')
            $("#publish_status").prop("disabled", false)
            @else
             $("#publish_status").prop("disabled", true)
            @endcan
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn, #dropdownMenuLink1").addClass("d-none");
            let timeInterval = setInterval(() => {
                if (fullyLoaded){
                    $("#saveBtn, #closeBtn").removeClass("d-none");
                    clearInterval(timeInterval);
                }
            }, 500);
            @endcan
        }

        $(document).on('click', '#deleteBtn', function (event) {
            @can("Properties Delete")
            deleteData($('#id').val());
            @endcan
        });

        $(document).on('click', '.delete-link', function (event) {
            @can("Properties Delete")
            let id = $(this).data('id');
            deleteData(id);
            @endcan
        });

        function deleteData(id) {
            @can("Properties Delete")
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this property!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{route('admin.listing.properties.delete')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {id},
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify(result.msg, "success")
                                    //dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    dataTable.row().order([10, 'DESC']).draw();
                                    toggleForm(false)
                                } else { // something went wrong on backend
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

        $('#uploadPhotosModal').on('hidden.bs.modal', function (e) {
            getPhotos()
        })

        function getPhotos() {
            let temp_id = $("#temp_id").val();
            let property_id = $("#id").val();
            galleryArr = [];

            if (temp_id != "" || property_id != "") {
                $.ajax({
                    url: "{{route('ajax.property-photos')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {temp_id, property_id},
                    success: function (result) {
                        let photos = "";
                        result.data?.map((item) => {
                            galleryArr.push(item.id)
                            photos += `<div class="photo-element" data-id="${item.id}">
                                        <div class="text-center">
                                            <div>
                                                <a href="${item.path}" data-lightbox="listing-gallery">
                                                    <img height="100%" width="100%" src="${item.thumbnail}" alt="">
                                                </a>
                                                <br>
                                                <a href="javascript:void(0)" type="button" data-id="${item.id}" class="mt-1 delete-photo">Delete</a>
                                            </div>
                                        </div>
                                    </div>`
                        })
                        $("#photos-gallery").html(photos)
                    },
                    error: function (result) {
                        console.log(result);
                        $("#photos-gallery").html()
                    }
                });
            }
        }

        function deleteMultiple() {
            @can("Properties Delete")
            let ids = [];
            $(".selectable-checkbox").map((index, item) => {
                item.checked ? ids.push(item.value) : []
            })
            if (ids.length > 0) {
                swal({
                    title: "Attention!!",
                    text: "You are trying to delete multiple properties. Once deleted, you will not be able to use these property!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{route('admin.listing.properties.multi.delete')}}",
                                type: 'POST',
                                dataType: 'json',
                                data: {ids},
                                success: function (result) {
                                    if (result.status == '1') {
                                        showToastify(result.msg, "success")
                                        dataTable.row().draw();
                                    } else {
                                        showToastify(result.msg, "error")
                                    }
                                },
                                error: function (result) {
                                    console.log(result);
                                }
                            });
                            document.getElementById("checkAll").checked = !1
                        }
                    });
            } else {
                showToastify("Please select at least one checkbox", "error")
            }
            @endcan
        }

        $(document).on("change", "#search_agents", function () {
            let agents = $(this).val();
            $("#search_owners").val("").trigger("change");
            if (agents) {
                $.ajax({
                    url: "{{route('ajax.owners')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {agents},
                    success: function (result) {
                        let options = "";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.first_name} ${item.first_name}<${item.mobile}></option>`
                        })
                        $("#search_owners").html(options)
                        $("#search_owners").val("").trigger("change");
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }
        })


        $("#sidebarListings").addClass('active');
    </script>
@endsection
