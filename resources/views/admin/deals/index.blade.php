@extends('admin.layouts.master')
@section('title')
    Deals
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
        .ax-grad-background {
            color: #5e5e5e !important;
            background: linear-gradient(180deg, rgb(211, 161, 136) 10%, rgb(255, 255, 255) 25%, rgb(211, 161, 136) 40%, rgb(211, 161, 136) 60%, rgb(211 161 135 / 66%) 75%, rgb(211, 161, 136) 93%);
        }
        .btn-outline-secondary {
            border-color: #c48667 !important;
            color: #c48667 !important;
        }

        .btn-check:active+.btn-outline-secondary,
        .btn-check:checked+.btn-outline-secondary,
        .btn-outline-secondary.active,
        .btn-outline-secondary.dropdown-toggle.show,
        .btn-outline-secondary:active {
            background-color: #c48667 !important;
            border-color: #c48667 !important;
            color: #fff !important;
        }

        .btn-outline-secondary:hover {
            background-color: #c48667 !important;
            border-color: #c48667 !important;
            color: #fff !important;
        }
    </style>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
<div class="row mb-3 pb-1">
    <div class="col-8"></div>
<div class="col-4 ">
    <!-- Radio Buttons -->
    <div class="btn-group float-end" role="group" aria-label="Basic radio toggle button group">
        <input type="radio" class="btn-check dashboardType" name="btnradio" id="btnradio1" autocomplete="off" value="ax" checked="">
        <a href="{{route('admin.work.deals.index')}}" class="btn btn-outline-secondary" for="btnradio1">AX CRM</a>

        <input type="radio" class="btn-check dashboardType" name="btnradio" id="btnradio2" value="bitrix" autocomplete="off">
        <a href="{{route('admin.deals_revamped.bitrix.index')}}" class="btn btn-outline-secondary" for="btnradio2">BITRIX</a>
    </div>
</div>
</div>
    <form class="row needs-validation" id="mainForm" novalidate>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header align-items-center d-flex">
                    <div class="flex-grow-1">
                        <h4 class="card-title mb-0 ">Deals</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Work</a></li>
                            <li class="breadcrumb-item active">Deals</li>
                        </ol>
                    </div>
                    <div class="flex-shrink-0">
                        <div class="form-check form-switch form-switch-right form-switch-md">
                            @can("Deals Create")


                                <button type="button" id="newBtnsss"  class="btn btn-danger d-none add-btn action-add"
                                data-bs-target="#dealCreateModal" data-bs-toggle="modal"
                                >
                                    <i class="ri-add-line align-bottom me-1"></i> Add New
                                </button>
                                <a href="{{ route('admin.deals_revamped.index') }}" class="btn btn-primary add-btn">Add New</a>
                            @endcan

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
    @includeIf("admin.deals.include.filterForm")

    @includeIf("admin.deals.include.createModal")
    <div class="row">
        <div class="col-xxl-12">
            <div class="card" id="contactList">
                <div class="card-body">

                    <table id="dealDataTable" class="table align-middle" style="width:100%">
                        <thead class="">
                            <tr class="table-light2 text-uppercase">
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                           data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Type</th>
                            <th style="max-width:width:300px;">Location</th>
                            <th>Deal Price</th>
                            <th>Agent</th>
                            <th>Updated By</th>
                            <th>Updated at</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>




@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.2/js/dataTables.buttons.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="{{asset('assets/libs/cleave.js/cleave.js.min.js')}}"></script>
        {{-- <script src=" "></script> --}}



    <script>
        var isUpdate = false;
        let dataObj = {};
        let viewUrl = "{{route('admin.deals_revamped.index')}}";
        let propertyView = "{{route('admin.listing.index')}}";
        let ax_log = "{{asset('assets/images/logo/logo.png')}}";
        function initPriceInput(price = "") {

            new Cleave('#deal_price', {
                numeral: true,
                numeralThousandsGroupStyle: 'thousand'
            });
        }
        initPriceInput()

        $("#property_id").select2({
            placeholder: "Select Property",
            allowClear: true,
            ajax: {
                url: "{{route('ajax.properties')}}",
                type: "GET",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        searchKeywords: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function (response) {
                    return {
                            results: $.map(response.data, function(item) {
                                return {
                                    text: `${item.ref_no} - ${item.title}`,
                                    id: item.id,
                                    'contact_id': item.owner_id,
                                    'agent_id': item.agent_id,
                                }
                            })
                        }
                },
                cache: true
            }
        });

        $("#deal_property_id").change(function(){
            let property = $(this).select2('data')[0];
           let property_id  = $(this).val();
            $.ajax({
                url: "{{route('admin.listing.properties.get')}}",
                type: 'GET',
                dataType: 'json',
                data: {id:property_id},
                success: function (result) {
                   let data = result.data;

                     $("#property_category").html(data?.property_category?.title);
                     $("#contact_name").html(data?.owner?.first_name+' '+data?.owner?.last_name);
                    $("#contact_mobile").html(data?.owner?.mobile);
                    $("#contact_email").html(data?.owner?.email);


                    let numberFormat = new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'AED',
                        minimumFractionDigits: 0,
                        //hide currency symbol
                        // currencyDisplay: 'none'
                    });
                    $("#property_price").html(numberFormat.format(data?.price?.price));
                    let dp = numberFormat.format(data?.price?.price);
                    let dealPrice =  dp.replace(/AED/g, '');
                    $("#deal_price").val(dealPrice);

                },
                error: function (result) {
                    $("#search_contacts").html(``)
                    console.log(result);
                }
            });

        });
        let transactionTypes = @json($transactionTypes);

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

        const loadSearchContacts = () => {
            $.ajax({
                url: "{{route('ajax.contacts')}}",
                type: 'GET',
                dataType: 'json',
                data: {},
                success: function (result) {
                    let options = "";
                    result?.data.map(item => {
                        options += `<option value="${item.id}">${item.first_name} ${item.last_name}</option>`
                    })
                    $("#search_contacts").html(options)
                },
                error: function (result) {
                    $("#search_contacts").html(``)
                    console.log(result);
                }
            });
        }

        const loadSearchLeads = () => {

            return '';
            $.ajax({
                url: "{{route('ajax.leads')}}",
                type: 'GET',
                dataType: 'json',
                data: {},
                success: function (result) {
                    let options = "";
                    result?.data.map(item => {
                        options += `<option value="${item.id}">${item.first_name} ${item.last_name}</option>`
                    })
                    $("#search_leads").html(options)
                },
                error: function (result) {
                    $("#search_leads").html(``)
                    console.log(result);
                }
            });
        }

        loadSearchContacts();
        loadSearchLeads();



        $(document).on("change", "#deal_status_id", function () {
            let deal_status_id = $(this).val();
            $("#deal_sub_status_id").val("").trigger("change");
            $("#deal_sub_status_id").html("");
            if (deal_status_id) {
                $.ajax({
                    url: "{{ route("ajax.deal-sub-statuses") }}",
                    type: 'GET',
                    dataType: 'json',
                    data: {deal_status_id},
                    success: function (result) {
                        let options = "";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.name}</option>`
                        })
                        $("#deal_sub_status_id").html(options)
                        $("#deal_sub_status_id").val(dataObj?.deal_sub_status_id).trigger("change")
                    },
                    error: function (result) {
                        console.log(result);
                    }
                });
            }

        });

        function getDealNotes() {
            @can("Deal Notes List")
            let deal_id = $("#id").val();
            $("#notes").html("");
            $.ajax({
                url: "{{route('ajax.deal-notes')}}",
                type: 'GET',
                dataType: 'json',
                data: {deal_id},
                success: function (result) {
                    let notes = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).format("DD/MM/YY HH:mm")
                        let imageUrl = `${item.created_by ? item.created_by.thumb_url : "{{ URL::asset('assets/images/users/avatar-8.jpg')}}"}`;
                        notes += `<div class="d-flex mb-4">
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

        $(document).on("click", "#addNoteButton", function () {
            @can("Deal Notes Create")
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
            formData.append("deal_id", id)
            formData.append("note", note)
            $.ajax({
                url: "{{route('admin.work.deals.add-note')}}",
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
                        getDealNotes()
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

        function toggleForm(show) {
            if (show == undefined) {
                $("#collapseContactForm").toggleClass("show")
                return
            }
            if (show) {
                $("#collapseContactForm").addClass("show");
            } else {
                $("#saveBtn, #closeBtn, #editBtn, #deleteBtn").addClass("d-none");
                $("#newBtn, #multiDeleteBtn").removeClass("d-none");
                $("#collapseContactForm").removeClass("show");
            }
        }

        $("#checkAll").change(function () {
            $(".selectable-checkbox").prop("checked", this.checked)
        })

        function getLeads(lead_type_id) {
            let agent_id = $("option:selected", "#agent_id").val();
            $("#lead_id").val("").trigger("change");
            $("#lead_id").html("");
            if (agent_id && lead_type_id){
                $.ajax({
                    url: "{{route('ajax.leads')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {lead_type_id, agent_id},
                    success: function (result) {
                        let options = "<option value='' disabled selected>Select</option>";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.first_name} ${item.last_name}</option>`
                        })
                        $("#lead_id").html(options)
                        $("#lead_id").val(dataObj?.lead_id || "").trigger("change")
                    },
                    error: function (result) {
                        $("#lead_id").html("")
                        console.log(result);
                    }
                });
            }
        }

        function getProperties(type) {
            $("#property_id").val("").trigger("change");
            $("#property_id").html("");
            $.ajax({
                url: "{{route('ajax.properties')}}",
                type: 'GET',
                dataType: 'json',
                data: {for: type},
                success: function (result) {
                    let options = "<option value='' disabled selected>Select</option>";
                    result?.data.map(item => {
                        options += `<option value="${item.id}">[${item.ref_no}]-(Beds: ${item.bed ? item.bed.name : ""})-(Unit: ${item.unit_no})</option>`
                    })
                    $("#property_id").html(options)
                    $("#property_id").val(dataObj?.property_id || "").trigger("change");
                },
                error: function (result) {
                    $("#property_id").html("")
                    console.log(result);
                }
            });

        }

        function getContacts(contact_id) {
            if (contact_id){
                $.ajax({
                    url: "{{route('ajax.contacts')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {contact_id},
                    success: function (result) {
                        let options = "<option value=''>Select</option>";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.first_name} ${item.last_name}</option>`
                        })
                        $("#contact_id").html(options)
                        $("#contact_id").val(dataObj?.contact_id || "").trigger("change");
                    },
                    error: function (result) {
                        $("#contact_id").html(`<option value="">Select</option>`)
                        console.log(result);
                    }
                });
            }
        }

        function transactionOptions(typeId){
            $("#transaction_type_id").html()
            let options = "";

            if (typeId == 1) {
                transactionTypes?.map(item => {
                    if(item.id == 3 || item.id == 4 || item.id == 5){
                        options += `<option value="${item.id}">${item.name}</option>`;
                    }
                })
            }else if (typeId == 2){
                transactionTypes?.map(item => {
                    if(item.id == 2 || item.id == 7){
                        options += `<option value="${item.id}">${item.name}</option>`;
                    }
                })
            }else{
                transactionTypes?.map(item => {
                    options += `<option value="${item.id}">${item.name}</option>`;
                })
            }

            $("#transaction_type_id").html(options)
            $("#transaction_type_id").val(dataObj?.transaction_type_id).trigger("change")
        }

        $(document).on("change", "#deal_type_id, #agent_id", function () {
            let type_id = $("option:selected", "#deal_type_id").val();
            let agent_id = $("option:selected", "#agent_id").val();
            getProperties();
            transactionOptions(type_id)
            if (!type_id || !agent_id){
                return;
            }

            // if (type_id == 1) {
            //     getLeads(1)
            //     getContacts(3)
            //     getProperties("RENT");
            //     $("#lead_id_area, #contact_id_area").removeClass("d-none")
            //     $("#lead_id, #contact_id").prop({
            //         required: true,
            //         disabled: false,
            //     });
            //     $("#lead_id_title").text("Tenant");
            //     $("#contact_id_title").text("Landlord");
            //     return
            // }

            if (type_id == 2) {
                getLeads(2)
                getContacts(3)
                getProperties("SALE");
                $("#lead_id_area, #contact_id_area").removeClass("d-none")
                $("#lead_id, #contact_id").prop({
                    required: true,
                    disabled: false,
                });
                $("#lead_id_title").text("Buyer");
                $("#contact_id_title").text("Seller");
                return
            }
            $("#lead_id, #contact_id, #property_id").html(`<option value="">Select</option>`)
            $("#lead_id, #contact_id").prop({
                required: false,
                disabled: true,
            });
            $("#lead_id_area, #contact_id_area").addClass("d-none");
        })


        function calculateCommissionVAT() {
            let value = parseFloat($("#gross_commission").val());
            if (value == undefined) {
                $("#gross_commission_vat_amount, #total_gross_commission_vat_amount").val("");
                return;
            }
            let grossCommissionVAT = (value * 0.05);
            $("#gross_commission_vat_amount").val(grossCommissionVAT.toFixed(2));
            $("#total_gross_commission_vat_amount").val((grossCommissionVAT + value).toFixed(2));
        }

        $(document).on("keyup", "#gross_commission", function () {
            calculateCommissionVAT()
            calculateAgentCommission();
        })

        $(document).on("blur", "#gross_commission", function () {
            calculateCommissionVAT();
            calculateAgentCommission();
        })

        function calculateAgentCommission() {
            let value = parseFloat($("#agent_commission").val());
            let grossCommission = parseFloat($("#gross_commission").val());
            if (isNaN(value) || isNaN(grossCommission)) {
                $("#agent_commission_amount").val(0)
                return;
            }
            let amount = (grossCommission * value) / 100;
            $("#agent_commission_amount").val(amount.toFixed(2))
        }

        $(document).on("keyup", "#agent_commission", function () {
            calculateAgentCommission()
        })

        $(document).on("blur", "#agent_commission", function () {
            calculateAgentCommission()
        })

        $(document).on("change", "#include_commission_vat", function () {
            if (this.checked) {
                $("#include_commission_area").removeClass("d-none");
            } else {
                $("#include_commission_area").addClass("d-none");
            }
        })

        $("#newBtn").click(function () {

            $('#callModal').modal('show');

        });

        $(document).on("click", "#editBtn", function () {
            @can("Deals Edit")
            $(".inputs").prop("disabled", false)
            editData($('#id').val());
            $("#saveBtn, #closeBtn").removeClass("d-none");
            $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            @endcan
        });

        $(document).on('click', '.edit-link', function (event) {
            @can("Deals Edit")
            $(".inputs").prop("disabled", false)
            let id = $(this).data('id');
            editData(id);
            @endcan
        });

        $(document).on("click", "#closeBtn", function () {
            toggleForm(false)
        })

        $(document).on('click', '#deleteBtn', function (event) {
            @can("Deals Delete")
            deleteData($('#id').val());
            @endcan
        });

        function editData(id) {
            @can("Deals Edit")
                isUpdate = true;
                toggleForm(true)
                getDeal(id);
                $("#saveBtn, #closeBtn").removeClass("d-none");
                $("#newBtn, #editBtn, #deleteBtn, #multiDeleteBtn").addClass("d-none");
            @endcan
        }
        $("#search_agents").change(function () {
            let agent_id = $(this).val();
            $("#search_leads").val("").trigger("change");
            $("#search_leads").html("");
            if (agent_id){
                $.ajax({
                    url: "{{route('ajax.leads')}}",
                    type: 'GET',
                    dataType: 'json',
                    data: {agent_id},
                    success: function (result) {
                        let options = "";
                        result?.data.map(item => {
                            options += `<option value="${item.id}">${item.first_name} ${item.last_name} ${item.mobile}</option>`
                        })
                        $("#search_leads").html(options)
                    },
                    error: function (result) {
                        $("#search_leads").html("")
                        console.log(result);
                    }
                });
            }
        })
        function getDeal(id) {
            $.ajax({
                url: "{{ route("admin.work.deals.get") }}",
                type: "get",
                data: {id: id},
                success: function (result) {
                    if (result.status == '1') {
                        dataObj = result.data
                        Object.keys(result.data).map((index) => {
                            $(`#${index}`).val(result.data[index]).trigger("change")
                        })
                        $("#include_commission_vat").prop("checked", result.data?.include_commission_vat)
                        if (result.data.include_commission_vat) {
                            $("#include_commission_area").removeClass("d-none");
                        } else {
                            $("#include_commission_area").addClass("d-none");
                        }
                        getDealNotes()
                        toggleForm(true)
                        scrollTop();
                        getActivityLog(id)
                    }
                }
            });
        }

        $(document).on('submit', '#mainForm', function (event) {
            $("#saveBtn").prop('disabled', true);
            event.preventDefault();
            var formData = new FormData($(this)[0]);
            $.ajax({
                url: isUpdate ? "{{route('admin.work.deals.update')}}" : "{{route('admin.work.deals.store')}}",
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
                        $("select.inputs").val("").trigger("change")
                        toggleForm(false)
                        dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                        isUpdate = false; // set isUpdate to false so that when we try to add new category it will be create request
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
                        showToastify(data.message, "success")
                    }
                },
            });
        });

        const statusColor = (status) =>{
            if(status == 'DRAFT') {
                return `<button type="button" class="btn btn-sm rounded-pill btn-soft-primary waves-effect waves-light">Draft</button>`;
            }else if(status == 'PENDING_APPROVAL') {
                return `<button type="button" class="btn btn-sm rounded-pill btn-soft-primary waves-effect waves-light">Pending Approval</button>`;
            }else if(status == 'REJECTED') {
                return `<button type="button" class="btn btn-sm rounded-pill btn-soft-danger waves-effect waves-light">Rejected</button>`;
            }else if(status == 'APPROVED') {
                return `<button type="button" class="btn btn-sm rounded-pill btn-soft-success waves-effect waves-light">Approved</button>`;
            }else if(status == 'COMPLETED') {
                return `<button type="button" class="btn btn-sm rounded-pill btn-soft-success waves-effect waves-light">Completed</button>`;
            }
        }

        let dataTable = $('#dealDataTable').DataTable({ //datatable initialization
            "order": [
                [0, "DESC"]
            ],
            dom: 'Bfrtip',
            buttons: [
                'colvis'
            ],
            language: {
                searchPlaceholder: "Search deals"
            },
            responsive: true,
            "processing": true,
            "serverSide": true,
            "ajax": {
                url: "{{route('admin.work.deals.dt')}}",
                type: "post",
                data: function (d) {
                    d.agents = filtersData?.search_agents;
                    d.deal_types = filtersData?.search_deal_types;
                    d.transaction_types = filtersData?.search_transaction_types;
                    d.leads = filtersData?.search_leads;
                    d.contacts = filtersData?.search_contacts;
                    d.deal_sources = filtersData?.search_deal_sources;
                    d.deal_statuses = filtersData?.search_deal_statuses;
                    d.statuses = filtersData?.search_deal_statuses;
                    d.deal_sub_statuses = filtersData?.search_deal_sub_statuses;
                    d.created_from = filtersData?.search_created_from;
                    d.created_to = filtersData?.search_created_to;
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
                $(`@includeIf("admin.deals.include.filterButtons")`).insertAfter(".dataTables_filter input")
            },

            "aoColumns": [
                {
                    data: null,
                    orderable: false,
                }, {
                    data: 'id',
                    name: 'id',
                }, {
                    data: 'deal_type_id',
                    name: 'deal_type_id',
                }, {
                    data: 'property_id',
                    name: 'property_id',
                    orderable: false,
                },
                {
                    data: null,
                    orderable: false,
                }, {
                    data: 'agent_id',
                    name: 'agent_id',
                    orderable: false,
                }, {
                    data: 'created_by_id',
                    name: 'created_by_id',
                    orderable: false,
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    orderable: false,
                },
                {
                    data: 'deal_status_id',
                    name: 'deal_status_id',
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
                    visible: false,
                    render: function (type, row, data, meta) {
                        return `<div class="form-check">
                                                <input class="form-check-input selectable-checkbox" type="checkbox" name="chk_child" value="` + data.id + `">
                                            </div>`;
                    }
                },
                {
                    "targets": 1,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return data.id;
                      //  return `<a href="javascript:void(0)" class="text-primary" onclick="viewData('` + data.id + `')">` + (data?.deal_type ? data.deal_type.name : "") + `</a>`;
                    }
                },{
                    "targets": 2,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        // return data?.property?.for;
                        return data?.type != '' ? data?.type  : '-';
                      //  return `<a href="javascript:void(0)" class="text-primary" onclick="viewData('` + data.id + `')">` + (data?.deal_type ? data.deal_type.name : "") + `</a>`;
                    }
                },
                {
                    "targets": 3,
                    orderable: true,
                    render: function (type, row, data, meta) {
                       if(data.property != null) {
                           let prop_image = ax_log;
                           if(data?.property?.first_image?.thumbnail != null) {
                               prop_image = data?.property?.first_image?.thumbnail;
                           }
                           console.log(data?.property?.ref_no+` `+prop_image)
                         return `<a href="`+propertyView+`?ref_number=${data?.property?.ref_no}" target="_blank"><div class="d-flex align-items-center">

                                        <div class="flex-grow-1">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <img src="${prop_image}" alt="" class="avatar-xs rounded bg-dark">
                                                </div>
                                                <div class=" ms-2">
                                                    <h6 class="fs-14 mb-0">${data?.property?.ref_no} - ${data?.property?.category}</h6>
                                                    <small class="text-muted">${data?.property?.location}</small>
                                                </div>
                                            </div>
                                        </div>



                                    </div></a>`;
                       }

                        return "-";


                    }
                },{
                    "targets": 4,
                    orderable: true,
                    render: function (type, row, data, meta) {
                        return numberFormatAX(data.deal_price);
                    }
                },
                {
                    targets: 5,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        if (!data.agent) return "-";
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.agent.id}" title="${data.agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.agent.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.agent.first_name}<p title="Created at" class="mb-0"><small>${moment(data.created_at).fromNow()}</small></p></div>
                                </div>`;


                    }
                }, {
                    targets: 6,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        if (!data.updated_by) return "-";
                        return `<div class="d-flex align-items-center userDetails" data-agent-id="${data.updated_by.id}"  title="${data.updated_by.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="${data.updated_by.thumb_url}" alt="" class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">${data.updated_by.first_name}<p title="Updated at" class="mb-0"><small>${moment(data.updated_at).fromNow()}</small></p></div>
                                </div>`;
                    }
                }, {
                    targets: 7,
                    orderable: false,
                    visible: false,
                    render: function (type, row, data, meta) {
                        if (data.updated_at == null) {
                            return '-';
                        } else {
                            return moment(data.updated_at).format('MM/DD/YYYY hh:mm:ss');
                        }
                    }
                }, {
                    targets: 8,
                    orderable: false,
                    visible: true,
                    render: function (type, row, data, meta) {
                        let status = statusColor(data.status);
                        return status;
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
                                        <li><a href="${viewUrl}?deal_id=`+data.id+`" data-id="` + data.id + `" class="dropdown-item"><i class="ri-eye-fill align-bottom me-2 text-muted"></i> View</a></li>
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

        function viewData(id) {
            $(".inputs").prop("disabled", true)
            $("#id").val(id)
            $("#saveBtn, #newBtn, #multiDeleteBtn").addClass("d-none");
            $("#editBtn, #closeBtn, #deleteBtn").removeClass("d-none");
            @can("Deals View")
            getDeal(id)
            scrollTop()
            @endcan
        }

        function deleteData(id) {
            @can("Deals Delete")
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to use this deal details!",
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
                .then((willDelete) => {
                    if (willDelete) { // on confirm delete the category
                        $.ajax({
                            url: "{{route('admin.work.deals.delete')}}",
                            type: 'POST',
                            dataType: 'json',
                            data: {id},
                            success: function (result) {
                                if (result.status == '1') { //if successfully deleted
                                    showToastify(result.msg, "success")
                                    dataTable.row().draw(); // redraw the datatable show we don't need to reload the page to see the changes
                                    toggleForm(false)
                                } else { // something went wrong on backend
                                    swal("Whoops", result.msg, "error");
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
            @can("Deals Delete")
            let ids = [];
            $(".selectable-checkbox").map((index, item) => {
                item.checked ? ids.push(item.value) : []
            })
            if (ids.length > 0) {
                swal({
                    title: "Attention!!",
                    text: "You are trying to delete multiple deals. Once deleted, you will not be able to use these deals!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                })
                    .then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{route('admin.work.deals.multi.delete')}}",
                                type: 'POST',
                                dataType: 'json',
                                data: {ids},
                                success: function (result) {
                                    if (result.status == '1') {
                                        showToastify(result.msg, "success")
                                        dataTable.row().draw();
                                        toggleForm(false)
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
            }
            @endcan
        }
       $("#dealSidebarBlock").addClass('collapse show');
        $("#sidebarDeals").addClass('active');
        $("#dealSidebarBlock").addClass('active');
    </script>

@endsection
