const showDeal = (id) => {
    $("#dealShowModal").modal("show");
    getBitrixDeal(id);
}
const getBitrixDeal = (id) => {
    $(".htmlData").html("")
    $("#modalLoader").removeClass("d-none")
    $("#modalData").addClass("d-none")
    $("#bitrixDealImportForm").trigger('reset')
    $.ajax({
        url: getBitrixDealUrl,
        type: "get",
        data: {id: id},
        success: function (result) {
            if (result.status == '1') {
                dataObj = result.data
                $("#modal_bitrix_deal_id").val(result.data.bitrix_deal.bitrix_deal_id)
                $("#modal_bitrix_dump_id").val(result.data.bitrix_deal.id)
                let deal_data_json = JSON.parse(result.data.bitrix_deal.deal_data);
                let deal_lead_data = JSON.parse(result.data.bitrix_lead);
                if(typeof deal_lead_data == 'string'){
                deal_lead_data = JSON.parse(deal_lead_data);
                }

                $("#modal_agent_id").val(result.data.bitrix_deal.agent_id)

                Object.keys(deal_data_json).map((index) => {
                    $(`#modal_bitrix_deal_${index}`).html(deal_data_json[index])
                    if(index == 'OPPORTUNITY'){
                        $("#modal_ax_deal_price").val(numberFormat(deal_data_json[index]));
                    }
                })
                if(deal_data_json.STAGE_ID != null){
                    $("#modal_bitrix_deal_STAGE_ID").html(deal_stages[deal_data_json.STAGE_ID] || '-');
                }
                if(result.data.lead !=null){

                    $("#modal_ax_deal_lead_selector").addClass('d-none')
                    $("#modal_bitrix_deal_ASSIGNED_BY_ID").html(result.data.lead.first_name+' '+result.data.lead.last_name);
                    $("#bitrixLeadData").html(result.data.bitrix_deal.deal_leads);
                    $("#modal_ax_deal_lead_id").val(result.data.lead.id);
                    let lead_data_table = `<table class="table table-bordered child-table">
                                        <tr>
                                            <td>Name</td>
                                            <td>${result.data.lead.first_name} ${result.data.lead.last_name || ''}</td>
                                        </tr>
                                        <tr>
                                            <td>Contact</td>
                                            <td>${result.data.lead.mobile}</td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>${result.data.lead.email || ''}</td>
                                        </tr>
                                         <tr>
                                            <td>Source</td>
                                            <td>${result.data?.lead?.source || ''}</td>
                                        </tr>
                                        <tr>
                                            <td>Created At</td>
                                            <td>${moment(result.data.lead.created_at).format('DD/MM/YYYY hh:mm A') || ''}</td>
                                        </tr>
                                    </table>`;
                    $("#modal_lead_more_info").html(lead_data_table);
                }else{
                    getLeads();
                }

              let dealDataHtml =  '';
                Object.keys(deal_data_json).map((index) => {
                    let val = typeof deal_data_json[index] == 'object' ? JSON.stringify(deal_data_json[index]) : deal_data_json[index];
                    dealDataHtml += `${index} - ${val}<br>`
                })
               $("#bitrixDealData").html(dealDataHtml);

               if(result.data.agent !=null){
                    $("#modal_ax_deal_agent").html(result.data.agent.first_name+' '+result.data.agent.last_name);
                    $("#modal_bitrix_deal_ASSIGNED_BY_ID").html(result.data.agent.first_name+' '+result.data.agent.last_name);
                }
                let lead_name = '';
                let lead_mobile = '';
                let lead_email = '';
               let leadDataHtml =  '';

            //    deal_lead_data =  result.data.bitrix_lead;

                Object.keys(deal_lead_data).map((index) => {
                    let val = typeof deal_lead_data[index] == 'object' ? JSON.stringify(deal_lead_data[index]) : deal_lead_data[index];
                    leadDataHtml += `${index} - ${val}<br>`
                    lead_name = deal_lead_data['NAME'];
                    lead_mobile = deal_lead_data['HAS_PHONE'] === 'Y' ? deal_lead_data['PHONE'][0]['VALUE'] : '';
                    lead_email = deal_lead_data['HAS_EMAIL'] === 'Y' ? deal_lead_data['EMAIL'][0]['VALUE'] : '';
                })
                let lead_data_table = `<table class="table table-bordered child-table">
                                        <tr>
                                            <td>Name</td>
                                            <td>${lead_name}</td>
                                        </tr>
                                        <tr>
                                            <td>Contact</td>
                                            <td>${lead_mobile}</td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>${lead_email}</td>
                                        </tr>
                                         <tr>
                                            <td>Source</td>
                                            <td>${deal_lead_data?.SOURCE_ID || ''}</td>
                                        </tr>
                                        <tr>
                                            <td>Created At</td>
                                            <td>${moment(deal_lead_data?.DATE_CREATE).format('DD/MM/YYYY hh:mm A') || ''}</td>
                                        </tr>
                                    </table>`;
                $("#modal_bitrix_lead_more_info").html(lead_data_table);
               $("#bitrixLeadData").html(leadDataHtml);
               let created_at = moment(deal_data_json.BEGINDATE).format('DD/MM/YYYY hh:mm A');
               let timestamps = `<table class="table table-bordered child-table"><tr>
                <td>Begin At: ${moment(deal_data_json.BEGINDATE).format('DD/MM/YYYY hh:mm A')}</td>
                 <td>Closed At: ${moment(deal_data_json.CLOSEDATE).format('DD/MM/YYYY hh:mm A')}</td>
                </tr><tr>
                <td>Created At: ${moment(deal_data_json.DATE_CREATE).format('DD/MM/YYYY hh:mm A')}</td>
                 <td>Modified At: ${moment(deal_data_json.DATE_MODIFY).format('DD/MM/YYYY hh:mm A')}</td>
                </tr></table>`;
                $("#modal_bitrix_deal_timestamps").html(timestamps);

            }
            $("#modal_ax_deal_listing_selector").removeClass("d-none");

            $('#model_listing_selectbox').propertySelect2('#dealShowModal', true, true, result.data.bitrix_deal.agent_id, 'Select property');
            $("#modalLoader").addClass("d-none")
            $("#modalData").removeClass("d-none")
            if(result.data.bitrix_deal.deal_id != null){
               $("#bitrixDealImportBtn").addClass("d-none")
            }else{
                $("#bitrixDealImportBtn").removeClass("d-none")
            }
        }
    });
}
const getLeads = (id = null) => {
    isSelect2Initialized($("#model_lead_selectbox")) ? $('#model_lead_selectbox').select2('destroy') : '';
    $('#model_lead_selectbox').select2({
        placeholder: 'Select lead',
        allowClear: true,
        dropdownParent: $('#dealShowModal'),
        templateResult: formatLeadSelect2,
        templateSelection: formatLeadSelect2,
        ajax: {
            url: route('ajax.leads.select2'),
            dataType: 'json',
            type: 'GET',
            data: function (params) {
                return {
                    search: params.term,
                    agent_id: $("#modal_agent_id").val(),
                }
            },
            processResults({data}) {
                return {
                    results: $.map(data, function (item) {

                        return {
                            text: `${item.first_name || "New"} ${item.last_name || ""}`,
                            id: item.id,
                            first_name: item.first_name,
                            last_name: item.last_name,
                            full_name: item.first_name + ' ' + item.last_name,
                            mobile: item.mobile,
                            email: item.email,
                            source: item.lead_source?.name,
                        }
                    })
                }
            }
        }
    });
    if (id != null) {
        $('#model_lead_selectbox').val(id).trigger('change');
    }
}
$("#model_lead_selectbox").on('select2:select', function (e) {
    let data = e.params.data;

   let lead_data_table = `<table class="table table-bordered child-table">
                                        <tr>
                                            <td>Name</td>
                                            <td>${data.first_name || ''} ${data.last_name || ''}</td>
                                        </tr>
                                        <tr>
                                            <td>Contact</td>
                                            <td>${data.mobile || ''}</td>
                                        </tr>
                                        <tr>
                                            <td>Email</td>
                                            <td>${data.email || ''}</td>
                                        </tr>
                                        <tr>
                                            <td>Source</td>
                                            <td>${data.source || ''}</td>
                                        </tr>
                                        <tr>
                                            <td>Created At</td>
                                            <td>${moment(data.created_at).format('DD/MM/YYYY hh:mm A') || ''}</td>
                                        </tr>
                                    </table>`;
                $("#modal_lead_more_info").html(lead_data_table);
});

$("#model_listing_selectbox").on('select2:select', function (e) {
    let data = e.params.data;
   let lead_data_table = `<div class="table-responsive" style="max-height: 250px">
                                                    <div data-simplebar="init" style="max-height: 160px;"><div class="simplebar-wrapper" style="margin: 0px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden;"><div class="simplebar-content" style="padding: 0px;">
                                                        <div data-simplebar="init" style="max-height: 215px;"><div class="simplebar-wrapper" style="margin: 0px;"><div class="simplebar-height-auto-observer-wrapper"><div class="simplebar-height-auto-observer"></div></div><div class="simplebar-mask"><div class="simplebar-offset" style="right: 0px; bottom: 0px;"><div class="simplebar-content-wrapper" tabindex="0" role="region" aria-label="scrollable content" style="height: auto; overflow: hidden;"><div class="simplebar-content" style="padding: 0px;">
                                                            <ul class="list-group" id="owner_properties_div"> <li class="list-group-item">
                           <a href="${propertyView}?ref_number=${data?.ref_no || ''}" target="_blank"><div class="d-flex align-items-center">

                                <div class="flex-grow-1">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <img src="${data?.first_image || ''}" alt="" class="avatar-xs rounded">
                                        </div>
                                        <div class=" ms-2">
                                            <h6 class="fs-14 mb-0">${data?.ref_no || ''} - ${data.type}</h6>
                                            <small class="text-muted">${data.location}</small>
                                        </div>
                                    </div>
                                </div>

                            </div></a>
                        </li></ul>
                                                        </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 63px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: hidden;"><div class="simplebar-scrollbar" style="height: 0px; display: none;"></div></div></div>

                                                    </div></div></div></div><div class="simplebar-placeholder" style="width: auto; height: 63px;"></div></div><div class="simplebar-track simplebar-horizontal" style="visibility: hidden;"><div class="simplebar-scrollbar" style="width: 0px; display: none;"></div></div><div class="simplebar-track simplebar-vertical" style="visibility: hidden;"><div class="simplebar-scrollbar" style="height: 0px; display: none;"></div></div></div>
                                                </div>`;
                $("#modal_property_more_info").html(lead_data_table);
});

const closeCollapseLeadInfo = () => {
    $("#collapseExample2").collapse('hide')
}
const closeCollapseDealInfo = () => {
    $("#collapseExample").collapse('hide')
}
const hideAccord = () =>{
$(".multi-collapse").collapse('hide')
}

$(document).on('submit', '#bitrixDealImportForm', function (event) {
    $("#saveBtn").prop('disabled', true);
    event.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
        url: bitrixDealFormUrl,
        type: 'POST',
        dataType: 'json',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.status == 'success') {
                showToastify(result.message, "success")
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
