function initCleaveInput(selector, price = "") {
    //$(selector).val(price).trigger("change");

    $(selector).each(function () {
        new Cleave(this, {
            numeral: true,
            numeralThousandsGroupStyle: "thousand",
        });
    });
}

initCleaveInput(".cleave");

const clearNumber = (number) => {
    let num = String(number).replace(/,/g, "");
    return num.replace(/[^0-9.-]+/g, "");
};

const toggleAccordion = (element) => {
    if ($("#" + element).hasClass("show")) {
        $("#" + element).collapse("toggle");
    } else {
        $("#" + element).collapse("toggle");
    }
};
const getFinancialData = async (id) => {
    $.ajax({
        url: getFinancialDataUrl,
        type: "POST",
        dataType: "json",
        data: { deal_id: id },
        success: function (result) {
            if (result.status == "1") {
                let deal_commission_source = result.data.deal_commission_source;
                let dealProgression = result.data.dealProgression;
                if (result.data.dealCommission != null) {

                    let commission_source = result.data.deal_commission_source;
                    $("#finance-commission_amount").val(
                        commission_source?.commission_amount
                    );

                    $(".netIncomeLabel").html(
                        numberFormat(commission_source?.commission_amount || "")
                    );
                    $("#commission_percentage").val(
                        commission_source?.commission_percentage
                    );

                }
                $("#finance-progression_amount").val(
                    numberFormat(dealProgression?.progression_amount || "")
                );

                if (result.data.dealProgression != null) {
                    let dealProgression = result.data.dealProgression;
                    $("#finance-transaction_type")
                        .val(dealProgression?.transaction_type)
                        .trigger("change");
                    $("#finance-sales_progression").val(
                        dealProgression?.conveyancer_id
                    );
                }
            } else if (result.status == "2") {
            } else {
            }
        },
        error: function (jqXhr, json, errorThrown) {
            let data = jqXhr.responseJSON;
            $("#splitCommissionFormSubmitBtn").prop("disabled", false);
            if (data.errors) {
                $.each(data.errors, function (index, item) {
                    showToastify(item[0], "error");
                });
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(data.message, "error");
            }
        },
    });
};

const calculateCommissionByPercentageV2 = () => {
    console.log("calculateCommissionByPercentageV2");
    let deal_price = $("#finance-deal_price").val();
    let commission_percentage = $("#commission_percentage").val();
    deal_price = clearNumber(deal_price);
    commission_percentage = clearNumber(commission_percentage);
    let total =
        (parseFloat(deal_price) * parseFloat(commission_percentage)) / 100;
        if(deal_price < total){
            showToastify("Commission can't be greater than deal price", "error");
            $("#commission_percentage").val("");
            $("#finance-commission_amount").val("");
            return false;
        }
    $("#finance-commission_amount").val(numberFormat(total || ""));
    $(".netIncomeLabel").text(numberFormat(total || ""));
    calculateCommission();
};
const calculateCommissionByNumbersV2 = () => {
    let deal_price = $("#finance-deal_price").val();
    let commission_amt = $("#finance-commission_amount").val();

    deal_price = clearNumber(deal_price);
    commission_amt = clearNumber(commission_amt);
    $(".netIncomeLabel").text(numberFormat(commission_amt || ""));
    let commission_percentage =
        (parseFloat(commission_amt) / parseFloat(deal_price)) * 100;
    commission_percentage = isNaN(commission_percentage)
        ? 0
        : commission_percentage;

    $("#commission_percentage").val(numberFormat(commission_percentage) || "");
    calculateCommission();
};
$("#commission_percentage").on("change", function () {
    let percentage = $(this).val();
    let listing_price = $("#listing_price").val();
    let total = (parseFloat(listing_price) * parseFloat(percentage)) / 100;
    $("#commission").val(total);
    $("#commission").trigger("change");
});
const sumUpUserCommissions = (commission) => {

    if (userCommissions.length > 0) {
        let userIndex = userCommissions.findIndex(
            (x) => x.user_id == commission.user_id
        );
        if (userIndex != -1) {
            userCommissions[userIndex].commission += parseFloat(
                commission.commission
            );
        } else {
            let user = {
                user_id: commission.user_id,
                commission: parseFloat(commission.commission),
                commission_category: commission.commission_category,
                commission_type: commission.commission_type,
                commission_value: commission.commission_value,
                full_name: commission.user.full_name,
                thumbnail: commission.user.image_url,
                role: commission.user.role_list[0],
            };
            userCommissions.push(user);
        }
    } else {
        let user = {
            user_id: commission.user_id,
            commission: parseFloat(commission.commission),
            commission_category: commission.commission_category,
            commission_type: commission.commission_type,
            commission_value: commission.commission_value,
            full_name: commission.user.full_name,
            thumbnail: commission.user.image_url,
            role: commission.user.role_list[0],
        };
        userCommissions.push(user);
    }

    drawUserCommissions(userCommissions);
};
const drawUserCommissions = (userCommissions) => {

    let total_net_commission = $("#net_commission").val();
    total_net_commission = clearNumber(total_net_commission);
    let html = "";
    $.each(userCommissions, function (index, user_commission) {
       // let userCommissionPercentage = (user_commission.commission / total_net_commission) * 100;
       // userCommissionPercentage = isNaN(userCommissionPercentage) ? 0 : userCommissionPercentage.toFixed(2);
        html += `<div class="row finRow user-total-amount">
          <div class="d-flex col-6">
            <div class="flex-shrink-0">
              <div class="avatar-sm rounded">
                <img src="${user_commission.thumbnail}" alt="" class="member-img img-fluid d-block rounded">
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="fs-14 mb-1">${user_commission.full_name}</h6>
              <p class="text-muted mb-0">${user_commission.role}</p>
            </div>
          </div>
          <div class="col">
            <div class="flex-shrink-0 text-left ">

              <p class="fs-12 mb-0">
                                 <span  class="ms-1 fs-14 cursor-pointer">&nbsp;</span>
              </p>
            </div>
          </div>
          <div class="col-sm-auto las-col">
            <div class="d-flex flex-wrap gap-2 li-action-button align-items-center mx-auto my-3 my-lg-0 edit-action ">
              <a href="javascript:void(0)" style="padding: 4px !important;
height: 30px; display:none;" onclick="editSingleCommission('215');   event.stopPropagation();" class="hide btn btn-ghost-info btn-icon  custom-toggle edit-commission-button">
                <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
              <a href="#!" style="padding: 4px !important;
height: 30px;display:none;" onclick="removeSplitCommission('215')" class="hide btn btn-ghost-danger btn-icon custom-toggle d-none remove-split-commission">
                <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

            </div>
            <div class="flex-shrink-0 text-end">
              <h6 class="fs-14 mb-1">AED ${numberFormat(user_commission.commission)}</h6>

            </div>
          </div>
        </div>`;

        $("#userCommissionList").html(html);
    });
    html = "";
    total_net_commission = $("#net_commission").val();
        total_net_commission = clearNumber(total_net_commission);
        // let totalCompanyEar = clearNumber(totalCompanyEarning);
        // let companyTotalPercentage = (totalCompanyEar / total_net_commission) * 100;
    html += `<div v-if="split" class="row finRow">
          <div class="col-6 d-flex">

            <div class="flex-shrink-0">
              <div class="avatar-sm rounded bg-dark">
                <img src="/assets/images/logo/logo.png" style="height: 80%" alt="" class="member-img img-fluid d-block rounded p-2">
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="fs-14 mb-1">AX CAPITAL</h6>
              <p class="text-muted mb-0">Real Estate Agency</p>
            </div>

          </div>
          <div class="col">&nbsp;</div>
          <div class="col-sm-auto">

            <div class="flex-shrink-0 text-end">
              <h6 class="fs-14 mb-1">AED &nbsp;<span class="total_company_earnings">${totalCompanyEarning}</span></h6>

            </div>
          </div>

        </div>`;
    $("#userCommissionList").append(html);
};
const splitCompanyRow = (commission) => {
    if (commission == undefined || commission == null || commission == "") {
        return "";
    }
    if (can_see_company_earning) {
        totalCompanyEarning = parseFloat(totalCompanyEarning) + parseFloat(commission.amount);

        let html = ` <div v-if="split" class="row finRow">
          <div class="col-6 d-flex">

            <div class="flex-shrink-0">
              <div class="avatar-sm rounded bg-dark">
                <img src="/assets/images/logo/logo.png" style="height: 80%" alt=""
                     class="member-img img-fluid d-block rounded p-2">
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="fs-14 mb-1">AX CAPITAL</h6>
              <p class="text-muted mb-0">Real Estate Agency</p>
            </div>

          </div>
          <div class="col"></div>
          <div class="col-sm-auto">

            <div class="flex-shrink-0 text-end">
              <h6 class="fs-14 mb-1">AED ${numberFormat(
                  commission?.amount
              )}</h6>

            </div>
          </div>

        </div>`;


        $(".total_company_earnings").html(numberFormat(totalCompanyEarning));
        return html;
    } else {
        return "";
    }
};
const splitRow = (commission) => {
    if (commission?.user == undefined) {
        return "";
    }
    // let showEditBtn =
    let html = `
  <!-- ... Commission HTML structure ... -->

 <div class="row finRow" v-if="index2 == 0">
          <div class="d-flex col-6">
            <div @click="toggleAccordion('collapseExample45')" class="flex-shrink-0">
              <div class="avatar-sm rounded">
                <img src="${commission.user.thumbnail}" alt=""
                     class="member-img img-fluid d-block rounded">
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="fs-14 mb-1">${commission.user.full_name}</h6>
              <p class="text-muted mb-0">${commission.user.position}</p>
            </div>
          </div>
          <div class="col">
            <div class="flex-shrink-0 text-left ">

              <p class="fs-12 mb-0">
                                 <span onclick="editSplit('${commission.deal_split_id}')   event.stopPropagation();"
                                     class="ms-1 fs-14 cursor-pointer">${
                                         commission.commission_value || "0"
                                     }${
        commission.commission_type == "Percentage" ? "%" : "/-"
    } </span>
              </p>
            </div>
          </div>
          <div class="col-sm-auto las-col">
            <div
                class="d-flex flex-wrap gap-2 li-action-button align-items-center mx-auto my-3 my-lg-0 edit-action ">
              <a href="javascript:void(0)" style="padding: 4px !important;
height: 30px;" onclick="editSplit('${commission.deal_split_id}')"
                 class="hide btn btn-ghost-info btn-icon  custom-toggle edit-commission-button">
                <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
              <a href="#!" style="padding: 4px !important;
height: 30px;" onclick="removeSplitCommission('${commission.id}')"
                 class="hide btn btn-ghost-danger btn-icon custom-toggle d-none remove-split-commission">
                <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

            </div>
            <div class="flex-shrink-0 text-end">
              <h6 class="fs-14 mb-1">AED ${numberFormat(
                  commission.commission
              )}</h6>

            </div>
          </div>
        </div>
`;
    return html;
};

const splitFirstRow = (commission, index) => {
    if (commission?.user == undefined) {
        return "";
    }
    let html = `  <div class="">
        <div class="row finRow" onclick="toggleAccordion('collapseExample${index}')">
          <div class="d-flex col-6">
            <div @click="toggleAccordion('collapseExample45')" class="flex-shrink-0">
              <div class="avatar-sm rounded">
                <img src="${commission?.user?.thumbnail}" alt=""
                     class="member-img img-fluid d-block rounded">
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="fs-14 mb-1">${commission.user.full_name}</h6>
              <p class="text-muted mb-0">${commission.user.position}</p>
            </div>
          </div>
          <div class="col">
            <div class="flex-shrink-0 text-left ">

              <p :class="commission.commission_category === 'INDIRECT' ? 'text-danger fs-12 mb-0' : 'text-success fs-12 mb-0'">
                                 <span
                                     class="ms-1 fs-14">${
                                         commission.commission_value || "0"
                                     }${
        commission.commission_type == "Percentage" ? "%" : "/-"
    } </span>
              </p>
            </div>
          </div>
          <div class="col-sm-auto las-col">
            <div
                class="d-flex flex-wrap gap-2 li-action-button align-items-center mx-auto my-3 my-lg-0 edit-action">
              <a href="javascript:void(0)" style="padding: 4px !important;
height: 30px;" onclick="editSplit('${commission.deal_split_id}');  event.stopPropagation();"
                 class="hide btn btn-ghost-info btn-icon custom-toggle edit-commission-button">
                <span class="icon-on"><i class="ri-pencil-line align-bottom"></i></span> </a>
              <a href="#!" style="padding: 4px !important;
height: 30px;display:none;" onclick="deleteDealCommission('${commission.id}','${
        commission.deal_id
    }')"
                 class="hide btn btn-ghost-danger btn-icon custom-toggle delete-deal-commission">
                <span class="icon-on"><i class="ri-delete-bin-2-line align-bottom"></i></span> </a>

            </div>
            <div class="flex-shrink-0 text-end">
              <h6 class="fs-14 mb-1">AED ${numberFormat(
                  commission.commission
              )}</h6>

            </div>
          </div>
        </div>
      </div>`;
    return html;
};

const drawSplits = (splits) => {

    let total_company_earning = 0;
    $("#splitList").html("");
    let splitCommissions = "";
    let splitCount = 1;
    $.each(splits, function (index, split) {

        let uuid_index = uuidv4();
        let is_primary_agent =
            deal?.value?.agent_id == split?.user_id ? true : false;

        let actionBtns = `<button type="button" onclick="editSplit('${split.id}')" class="btn btn-sm btn-outline-info edit-split-button edit-action"><i
                            class="ri-pencil-fill"></i> </button>
`;
        if(deal.value.external_agent_id === split.user_id){

        }else if (index != 0) {
            actionBtns += `<button type="button" onclick="removeSplit('${
                split.id
            }')" class="btn btn-sm btn-outline-danger remove-split-button ${
                index == "0" || index == 0 ? "d-none" : ""
            } edit-action"><i
                            class="ri-delete-bin-2-line"></i> </button>`;
        }

        let splitHtml = `
<div class="card">
  <div class="card-header">
  <div class="row">
    <div class="col-12">
  <h4 class="card-title mb-0 splitCardTitle">Split ${splitCount} </h4>
 <span class="float-end ml-2" style="    margin-left: 6px;">${actionBtns}</span>
  <span class="float-end">AED ${numberFormat(split.split_amount)}</span>
<br>
      <span
          class="text-muted float-end fs-12"> Created By ${
              (split?.created_by?.full_name || "")
          }</span>
</div>
</div>
</div>
<div class="card-body">
  <div class="row">
    <div class="col-12">`;
        let i = 0;
        $.each(split.commissions, function (index2, commission) {
            if (i == 0) {
                splitHtml += splitFirstRow(commission, uuid_index);
            } else {

                sumUpUserCommissions(commission);
            }
            i++;
        });

        let j = 0;
        splitHtml += `<div class="collapse"  id="collapseExample${uuid_index}">`;
        $.each(split.commissions, function (index2, commission) {
            if (j > 0) {
                splitHtml += splitRow(commission);
            }
            j++;
        });
        splitHtml += splitCompanyRow(split.company_earning);
        let tce =
            split.company_earning == null || split.company_earning == undefined
                ? 0
                : parseFloat(split?.company_earning?.amount);
        total_company_earning += tce || 0;
        splitHtml += `</div></div></div></div>`;
        $("#splitList").append(splitHtml);
        splitCommissions = "";
        splitCount++;
    });
    if(splits.length < 2){
        $(".splitCardTitle").addClass("d-none");
        $(".user-total-amount").addClass("d-none");
    }
    $(".company-income").html(numberFormat(total_company_earning));
};
// const getDealDeductibles = async (id) => {
//     return "";
//     let response = await axios.get(`/api/vue/v1/get-deductible?deal_id=${id}`);
//     if (response.status == 200) {
//         let deductibless = response.data.data;
//         let html = "";
//         if (response.data.status == "1") {
//             if (deductibless !== null) {
//                 for (let i = 0; i < deductibless.length; i++) {
//                     html += drawDeductible(deductibless[i]);
//                 }
//             } else {
//                 html = `<div class="text-center text-muted">No fee added yet</div>`;
//             }
//             $("#deductibleList").html(html);
//             handleDealStatus(deal.value);
//         }
//     } else {
//         deductibles.value = [];
//         showToastify("Unable to fetch fee details", "error");
//     }
// };

const getDealSplits = async (id, refresh = false) => {
    totalCompanyEarning = 0;
        if (id == undefined || id == null || id == '') {
            return '';
        }
       // let response = await axios.get(route('get.deal.commission.calculations1') + '?deal_id=' + id + `&refresh_calculations=` + refresh)
        let response = await axios.get(route('get.deal.commission.calculations') + '?deal_id=' + id + `&refresh_calculations=` + refresh)
        if (response.status == 200) {
            splits.value = response.data.data
            drawSplits(splits.value)
            handleDealStatus(deal.value)
        } else {
            splits.value = []
            showToastify('Something went wrong', 'error')
        }
    }

const editSplit = async (id) => {
    // let splitSource = getSplitSource(deal.value.type);
    // $("#newSplitCommissionSource").html(splitSource);
    if (id == undefined || id == null || id == "") {
        return "";
    }
    let response = await axios.get(route("get.deal.split") + "?split_id=" + id);
    if (response.status == 200) {
        if (response.data.status == "1") {
            let data = response.data.data;
            $("#dealSplitModal").modal("show");
            $("#deal_split_deal_id").val(deal_id);
            $("#deal_split_deal_price").html(
                numberFormat(deal.value.deal_price)
            );
            $("#deal_split_gross_commission").html(
                numberFormat(deal.value.gross_commission)
            );
            $("#deal_split_net_commission").html(
                numberFormat(deal.value.net_commission)
            );
            $("#split_modal_deal_type").html(deal.value.type);
            $("#deal_split_id").val(data.id);
            $("#deal_split_agent_id").val(data.user_id).trigger("change");
            $("#deal_split_amount").val(numberFormat(data.split_amount));
            $("#deal_split_percentage").val(data.split_percentage);
            $("#dealSplitModal").find(".modal-title").html("Edit Deal Split");

            let user_commission = response.data.commission || {};
            if(user_commission.length > 0){
            let commission_sign = "";
            let html = "";
            for (const key in user_commission) {
                let uuid = uuidv4();
                if (user_commission[key].commission_slab != null) {
                    commission_sign =
                    user_commission[key].commission_slab?.commission_type ==
                        "Percentage"
                            ? "%"
                            : "AED";
                } else {
                    commission_sign = "AED";
                }

                // html += `<option value="${users[key].id}">${users[key].full_name}</option>`;
                html += `<div class="row ${(is_external_agent && globalUser.id != user_commission[key].user.id) ? 'd-none' : ''} mt-2">

                        <div class="d-flex col-5">
                            <div class="flex-shrink-0">
                                <div class="avatar-sm rounded">
                                    <img src="${user_commission[key].user.image_url}"
                                        alt="" class="member-img img-fluid d-block rounded">
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-14 mb-1">${user_commission[key].user.full_name}</h6>
                                <p class="text-muted mb-0">${user_commission[key].user.role_list[0]}</p>
                                <input type="hidden" name="user_id[]" value="${user_commission[key].user.id}">
                            </div>
                        </div>
                        <div class="col d-none">
                         <div class="flex-shrink-0 text-left ">
                            <select class="form-control user_commission_from" name="user_commission_from[]"
                              id="commission_from_${uuid}"
                                    data-uuid = "${uuid}"
                            required="">
                                <option value="Net Commission" ${user_commission[key]?.commission_from == 'Net Commission' ? 'selected' : ''} >Net commission</option>
                                <option value="Remaining Amount" ${user_commission[key]?.commission_from == 'Remaining Amount' ? 'selected' : ''}>Remaining Amount</option>
                            </select>
                         </div>
                         </div>
                          <div class="col">
                            <div class="flex-shrink-0 text-left ">
                             <div class="input-group">
                                    <span class="input-group-text">${commission_sign}</span>
                                    <input type="text"
                                    id="commission_percentage_${uuid}"
                                    data-uuid = "${uuid}"
                                    class="form-control cleave split_form_inputs split_comm_percentage" name="commission_percentage[]" value="${user_commission[key]?.commission_value}" required="">
                                </div>

                            </div>
                        </div>
                        <div class="col-sm-auto las-col">

                            <div class="flex-shrink-0 text-end">
                                <div class="input-group">
                                    <span class="input-group-text">AED</span>
                                    <input type="text"
                                      data-id="${user_commission[key].user.id}"
                                    id="commission_amount_${uuid}"
                                    data-uuid = "${uuid}"
                                    class="form-control cleave split_form_inputs split_comm_amt" name="commission_amount[]" required="" value="${user_commission[key]?.commission}">
                                </div>

                            </div>
                        </div>

                    </div>`;
            }

            $("#split_modal_user_list").html(html);
            if(response.data.referral != null){
                let referral = response.data.referral;
            $("#deal_split_referral_agent_id").val(referral.user_id).trigger("change");
            $("#deal_split_referral_amount").val(referral.referral_amount).trigger("change");
            }

        }

        } else {
        }
    }
};

const getSalesProgressions = () => {
    let transaction_type = $("#finance-transaction_type").val();

    if (
        transaction_type != null ||
        transaction_type != undefined ||
        transaction_type != ""
    ) {
        $.ajax({
            url:
                route("deal.get.sales.progressions") +
                "?transaction_type=" +
                transaction_type,
            type: "GET",
            dataType: "json",
            success: function (result) {
                if (result.status == "1") {
                    let html = "";
                    let users = result.data || {};

                    for (const key in users) {
                        if (salesProgressionSelected == true) {
                            html += `<option value="${users[key].id}" >${users[key].full_name}</option>`;
                        } else {
                            html += `<option value="${users[key].id}" ${
                                users[key].selected == true ? "selected" : ""
                            }>${users[key].full_name}</option>`;
                        }
                    }

                    $("#finance-sales_progression").val("").trigger("change");
                    $("#finance-sales_progression").empty();
                    $("#finance-sales_progression").append(html);
                } else {
                    $("#finance-sales_progression").val("").trigger("change");
                }
            },
        });
    }
};
const calculateCommission = () => {

    let deal_price = $("#finance-deal_price").val();
    let commission_amount = $("#finance-commission_amount").val();
    let progression_amount = $("#finance-progression_amount").val();
    commission_amount = clearNumber(commission_amount);

    deal_price = String(deal_price).replace(/,/g, "");
    deal_price = parseFloat(deal_price);
    let commission_percentage = $("#commission_percentage").val();
    // if($("#commission_cal_sign").val() == "percentage"){

    //     commission_amount = (deal_price / 100) * commission_percentage;
    // }else{

    //     commission_amount = parseFloat(commission_amount);
    // }

    let deposit_amount = 0;
    deposit_amount = (deal_price / 100) * 10;
    let gross_commission = deal_price;
    let total_commission_amount = 0;
    let total_vat = 0;
    let total_sales_progression = 0;
    total_sales_progression = $("#finance-progression_amount").val();
    total_sales_progression = clearNumber(total_sales_progression);

    total_commission_amount = isNaN(commission_amount) ? 0 : commission_amount;

    let total_net_commission_placeholder = isNaN(total_commission_amount)
        ? 0
        : numberFormat(total_commission_amount || 0);
    $("#finance-deposit_amount").val(numberFormat(deposit_amount || 0));
    console.log(commission_amount)
    $(".net_commission").text(numberFormat(commission_amount || 0)); //old net_commission

    //old net_commission
    commission_amount = clearNumber(commission_amount);
    $("#net_commission").val(commission_amount);
    $(".total_vat").html(numberFormat(total_vat || 0));
    let total_gross_commission =
        parseFloat(commission_amount) +
        parseFloat(total_vat) +
        parseFloat(total_sales_progression || 0);

    $("#financial-total_vat").val(total_vat.toFixed(2) || 0);
    total_commission_amount = isNaN(total_commission_amount) ? 0 : deal.value.gross_commission;
    total_gross_commission = isNaN(total_gross_commission) ? 0 : total_gross_commission;
    $(".gross_commission").html(numberFormat(total_gross_commission) || 0);
    $(".total_progression_fee").html(
        numberFormat(total_sales_progression) || 0
    );
    $("#financial-total_progression_fee").val(
        numberFormat(total_sales_progression) || 0
    );
    $("#financial-gross_commission").val(
        total_gross_commission.toFixed(2) || 0
    );
};
const calculateSplitAmount = () => {
    let deal_net_commission = deal?.value?.net_commission;
    let deal_split_amount = $("#deal_split_amount").val();
    let deal_split_percentage = $("#deal_split_percentage").val();
    if (deal_split_percentage > 100) {
        $("#deal_split_amount").val("");
        showToastify("It can't be greater than 100%", "error");
        $("#deal_split_percentage").val("");
        return false;
    }
    if (deal_split_percentage > 0) {
        deal_split_amount = (deal_net_commission / 100) * deal_split_percentage;
        $("#deal_split_amount").val(numberFormat(deal_split_amount));
    } else {
        $("#deal_split_amount").val("");
    }
    calculateSplitUserCommission();

};
const calculateSplitPercentage = () => {
    let deal_net_commission = deal?.value?.net_commission || $("#net_commission").val();
    let deal_split_amount = $("#deal_split_amount").val();
    let deal_split_percentage = $("#deal_split_percentage").val();
    deal_split_amount = deal_split_amount.replace(/,/g, "");
    if (deal_split_amount > deal_net_commission) {
        $("#deal_split_percentage").val("");
        $("#deal_split_amount").val("");
        showToastify("It can't be greater than net commission", "error");
        return false;
    }
    if (deal_split_amount > 0) {
        deal_split_percentage = (deal_split_amount / deal_net_commission) * 100;
        $("#deal_split_percentage").val(deal_split_percentage.toFixed(2));
    } else {
        $("#deal_split_percentage").val("");
    }
    let referral_amount = $("#deal_split_referral_amount").val() || 0;
    referral_amount = clearNumber(referral_amount);
    let remaining_amount =  parseFloat(deal_split_amount) - parseFloat(referral_amount);
    $("#deal_split_remaining_amount_label").html(numberFormat(remaining_amount));
    $("#deal_split_remaining_amount").val(remaining_amount);
    calculateSplitUserCommission();
};

const showCommissionCard = (getCommission = true) => {
    $("#commission_form_deal_id").val(deal_id);
    $("#commissionCard").removeClass("d-none");
    $("#notCommissionCard").addClass("d-none");
    $("#finance-transaction_type").select2({
        placeholder: "Select Transaction Type",
        allowClear: true,
    });

    if (deal.value.type == "Secondary Sale") {
        if (show_progression_data == true) {
            $(".sales-progression-section").removeClass("d-none");
        } else {
            $(".sales-progression-section").addClass("d-none");
        }
        getSalesProgressions();
        checkvalidations("sales-prog-prop");
    } else {
        $(".sales-progression-section").addClass("d-none");
    }
    if (getCommission === true) {
        getDealCommissions(deal_id);
    }

    if (deal.value != undefined || deal.value != null) {
        //  $(".net_commission").html(numberFormat(deal.value.net_commission) || 0);
        $(".total_vat").html("0");
       let gross_commission =  isNaN(deal.value.gross_commission) ? 0 : deal.value.gross_commission;
        $(".gross_commission").html(
            numberFormat(gross_commission) || 0
        );
        $("#net_commission").val(numberFormat(deal.value.net_commission));
        $(".netIncomeLabel").html(numberFormat(deal.value.net_commission));
        $("#financial-total_vat").val("0");
        $("#financial-gross_commission").val(gross_commission);
        $("#finance-deal_price").val(numberFormat(deal.value.deal_price));
        if (
            deal.value.gross_commission == 0 ||
            deal.value.gross_commission == null ||
            deal.value.gross_commission == ""
        ) {
            $("#finanical-commission_saveBtn").prop("disabled", true);
        } else {
            $("#finanical-commission_saveBtn").prop("disabled", false);
        }
    }
};
const showDealSplitModal = () => {
    $("#splitForm").trigger("reset");
    $(".split_form_inputs").val("").trigger("change");
    $("#dealSplitModal").modal("show");
    $("#split_modal_user_list").html('')
    $("#deal_split_deal_id").val(deal_id);
    $("#deal_split_deal_price").html(numberFormat(deal.value.deal_price));
    $("#deal_split_gross_commission").html(
        numberFormat(deal.value.gross_commission)
    );
    let net_commission =  $("#net_commission").val() || deal.value.net_commission;
    net_commission = clearNumber(net_commission);
    $("#deal_split_net_commission").html(numberFormat(net_commission)
    );
    $("#split_modal_deal_type").html(deal.value.type);
    // $("#deal_split_agent_id").val('').trigger('change');
    $("#dealSplitModal").find(".modal-title").html("Add Deal Split");
};
const getParentUsers = async (id) => {
    $.ajax({
        url: getParentUserUrl,
        type: "POST",
        dataType: "json",
        data: { user_id: id, deal_id: deal_id },
        success: function (result) {
            if (result.status == "1") {
                let html = "";
                let data = result.data || {};
                let commission_sign = "";
                let disableRemainingAmt = "";
                for (const key in data) {
                    let uuid = uuidv4();
                    if (data[key].commission_slab != null) {
                        commission_sign =
                            data[key].commission_slab?.commission_type ==
                            "Percentage"
                                ? "%"
                                : "AED";
                    } else {
                        commission_sign = "AED";
                    }
                    if(data[key].user.id == id){
                        disableRemainingAmt = "disabled";
                    }else{
                        disableRemainingAmt = "";
                    }
                    // html += `<option value="${users[key].id}">${users[key].full_name}</option>`;
                    html += `<div class="row mt-2">

                            <div class="d-flex col-5">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm rounded">
                                        <img src="${data[key].user.image_url}"
                                            alt="" class="member-img img-fluid d-block rounded">
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fs-14 mb-1">${data[key].user.full_name}</h6>
                                    <p class="text-muted mb-0">${data[key].user.role_list[0]}</p>
                                    <input type="hidden" id="user_id_${uuid}" name="user_id[]" value="${data[key].user.id}">
                                </div>
                            </div>
                            <div class="col d-none">
                             <div class="flex-shrink-0 text-left ">
                                <select class="form-control user_commission_from" name="user_commission_from[]"
                                  id="commission_from_${uuid}"
                                        data-uuid = "${uuid}"
                                required="">
                                    <option value="Net Commission" ${data[key].commission_slab?.calculation_from == 'Net Commission' ? 'selected' : ''} >Net commission</option>
                                    <option value="Remaining Amount" ${disableRemainingAmt} ${data[key].commission_slab?.calculation_from == 'Remaining Amount' ? 'selected' : ''}>Remaining Amount</option>
                                </select>
                             </div>
                             </div>
                              <div class="col">
                                <div class="flex-shrink-0 text-left ">
                                 <div class="input-group">
                                        <span class="input-group-text">${commission_sign}</span>
                                        <input type="text"
                                        id="commission_percentage_${uuid}"
                                        data-uuid = "${uuid}"
                                        class="form-control cleave split_form_inputs split_comm_percentage" name="commission_percentage[]" value="${data[key].commission_slab.commission_value}" required="">
                                    </div>

                                </div>
                            </div>
                            <div class="col-sm-auto las-col">

                                <div class="flex-shrink-0 text-end">
                                    <div class="input-group">
                                        <span class="input-group-text">AED</span>
                                        <input type="text"
                                        data-id="${data[key].user.id}"
                                        id="commission_amount_${uuid}"
                                        data-uuid = "${uuid}"
                                        class="form-control cleave split_form_inputs split_comm_amt" name="commission_amount[]" required="">
                                    </div>

                                </div>
                            </div>

                        </div>`;
                }
                html+=`<div class="row mt-2">
          <div class="col-5 d-flex">

            <div class="flex-shrink-0">
              <div class="avatar-sm rounded bg-dark">
                <img src="/assets/images/logo/logo.png" style="height: 80%" alt="" class="member-img img-fluid d-block rounded p-2">
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="fs-14 mb-1">AX CAPITAL</h6>
              <p class="text-muted mb-0">Real Estate Agency</p>
            </div>

          </div>
          <div class="col">-</div>
         <div class="col-sm-auto las-col">

                                <div class="flex-shrink-0 text-end">
                                    <div class="input-group">
                                        <span class="input-group-text">AED</span>
                                        <input type="text" id="commission_amount_comapny" class="form-control cleave split_form_inputs" name="company_commission" readonly>
                                    </div>

                                </div>
                            </div>

        </div>`;
                $("#split_modal_user_list").html(html);
            } else if (result.status == "2") {
            } else {
            }
        },
        error: function (jqXhr, json, errorThrown) {
            console.log(jqXhr);
            let data = jqXhr.responseJSON;
            $("#splitCommissionFormSubmitBtn").prop("disabled", false);
            if (data.errors) {
                $.each(data.errors, function (index, item) {
                    showToastify(item[0], "error");
                });
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(data.message, "error");
            }
        },
    });
};
$("#commission_percentage").on("keyup", function (e) {
    if($(this).val() > 100){
        showToastify('Commission percentage can not be greater than 100%', 'error');
        $(this).val('100').trigger('keyup');
        return false;
    }
});

$("#commission_cal_sign_label").on("click", function (e) {

    let commission_sign = $("#commission_cal_sign").val();
    if (commission_sign === "percentage") {
        $("#commission_cal_sign").val('amount');
        $("#commission_cal_sign_label").text("AED");
        $("#commission_percentage").addClass('d-none')
        $("#finance-commission_amount").removeClass('d-none')
    } else {
        $("#commission_cal_sign_label").text("%");
        $("#commission_percentage").removeClass('d-none')
        $("#finance-commission_amount").addClass('d-none')
        $("#commission_cal_sign").val('percentage');

    }
});
$("#finance-commission_amount").on("keyup", function (e) {
    let deal_price = $("#finance-deal_price").val();
    let amount = $("#finance-commission_amount").val();
    deal_price = clearNumber(deal_price);
    amount = clearNumber(amount);
    if(parseFloat(amount) > parseFloat(deal_price)){
        showToastify('Commission amount can not be greater than deal price', 'error');
        amount = amount.slice(0, -1);
        $("#finance-commission_amount").val(amount).trigger('keyup');
        return false;
    }


});

$(document).on("keyup", "#deal_split_referral_amount", function (e) {
    let referral_agent = $("#deal_split_referral_agent_id").val();
    if(referral_agent == null || referral_agent == ''){
        showToastify('Please select referral agent first', 'error');
        $("#deal_split_referral_amount").val('');
        return false;
    }
    let split_amount = $("#deal_split_amount").val();
    let referral_amount = $("#deal_split_referral_amount").val() || 0;
    if (
        split_amount == "" ||
        split_amount == null ||
        split_amount == undefined
    ) {
        showToastify("Please enter split amount first", "error");
        return false;
    }
    referral_amount = clearNumber(referral_amount);
    split_amount = clearNumber(split_amount);

    if (referral_amount > 0) {
        $("#split_amount_msg").html(
            numberFormat(referral_amount) + " referral amount included"
        );
    } else {
        $("#split_amount_msg").html("");
    }
    $(".split_comm_percentage").trigger("keyup");
    calculateSplitRemainingAmount();
    calculateSplitPercentage();

});
const calculateSplitRemainingAmount = () => {
    let deal_split_amount = $("#deal_split_amount").val();
    let referral_amount = $("#deal_split_referral_amount").val() || 0;
    let selectedAgentId = $("#deal_split_agent_id").val();

    let total_commission = 0;

    deal_split_amount = clearNumber(deal_split_amount);
    referral_amount = clearNumber(referral_amount);
    deal_split_amount = parseFloat(deal_split_amount) - parseFloat(referral_amount);
    let company_commission = deal_split_amount;
    let remaining_amount = deal_split_amount;
    $(".split_comm_amt").each(function () {
        let commission = $(this).val() || 0;
        commission = clearNumber(commission);
        let percentage = $("#commission_percentage_" + $(this).data("uuid")).val();
        let currentUserId = $(this).data("id");
        let uuid = $(this).data("uuid");
        if (currentUserId == selectedAgentId) {
            remaining_amount = parseFloat(deal_split_amount) - parseFloat(commission);
        }else{
            let commission_from = $("#commission_from_" + uuid).val();
            if (commission_from == 'Net Commission') {
                remaining_amount = parseFloat(remaining_amount) - parseFloat(commission);
            }

        }
        company_commission = parseFloat(company_commission) - parseFloat(commission);

        total_commission += parseFloat(commission);
        commission = clearNumber(commission);
    });


    $("#deal_split_remaining_amount_label").html(numberFormat(remaining_amount));
    $("#deal_split_remaining_amount").val(remaining_amount);
    $("#commission_amount_comapny").val(numberFormat(company_commission));
}
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

$(document).on("change", ".user_commission_from", async function (e) {
    let uuid = $(this).data("uuid");
    $("#commission_amount_" + uuid).val('0');
    calculateSplitRemainingAmount();
    await sleep(500);
    let percentage = $("#commission_percentage_"+uuid).val();
    let commission_from = $(this).val();
    let remaining_amount = $("#deal_split_remaining_amount").val();

    let split_amount = $("#deal_split_amount").val();
    let commission = 0;
    split_amount = clearNumber(split_amount);
    if (commission_from == 'Net Commission') {
        commission = (parseFloat(split_amount) * parseFloat(percentage)) / 100;
    }else{
        commission = (parseFloat(remaining_amount) * parseFloat(percentage)) / 100;


    }
  $("#commission_amount_" + uuid).val(numberFormat(commission) || "");

calculateSplitRemainingAmount();
calculateSplitUserCommission();
});
    $(document).on("keyup", ".split_comm_percentage", function (e) {
    let uuid = $(this).data("uuid");
    let percentage = $(this).val();
    let commission_from = $("#commission_from_" + uuid).val();
    let remaining_amount = $("#deal_split_remaining_amount").val();
    if (percentage > 100) {
        showToastify("A single user commission can't be greater than 100%", "error");
        return false;
    }
    let split_amount = $("#deal_split_amount").val();
    let referral_amount = $("#deal_split_referral_amount").val() || 0;
    if (
        split_amount == "" ||
        split_amount == null ||
        split_amount == undefined
    ) {
        showToastify("Please enter split amount first", "error");
        return false;
    }
    referral_amount = clearNumber(referral_amount);
    split_amount = clearNumber(split_amount);
    split_amount = parseFloat(split_amount) - parseFloat(referral_amount);
    let total = 0;
    if(commission_from == 'Net Commission'){
     total =
        percentage.length > 0
            ? (parseFloat(split_amount) * parseFloat(percentage)) / 100
            : 0;

    }else{
         total =
        percentage.length > 0
            ? (parseFloat(remaining_amount) * parseFloat(percentage)) / 100
            : 0;
    }
    $("#commission_amount_" + uuid).val(numberFormat(total) || "");
    calculateSplitRemainingAmount();
});

const calculateSplitUserCommission = () => {
    let split_amount = $("#deal_split_amount").val();
    let selected_agent_id = $("#deal_split_agent_id").val();
    let referral_amount = $("#deal_split_referral_amount").val() || 0;
    let total = 0;
    if (
        split_amount == "" ||
        split_amount == null ||
        split_amount == undefined
    ) {
        showToastify("Please enter split amount first", "error");
        return false;
    }
    referral_amount = clearNumber(referral_amount);
    split_amount = clearNumber(split_amount);
    split_amount = parseFloat(split_amount) - parseFloat(referral_amount);
    remaining_amount = parseFloat(split_amount);
    $(".split_comm_percentage").each(function () {
        let uuid = $(this).data("uuid");
        let user_id = $("#user_id_" + uuid).val();
        let commission_from = $("#commission_from_" + uuid).val();
        let percentage = $(this).val();
        let total = 0;

        if(commission_from == 'Net Commission'){
        total =
            percentage.length > 0
                ? (parseFloat(split_amount) * parseFloat(percentage)) / 100
                : 0;
                remaining_amount = parseFloat(remaining_amount) - parseFloat(total);
                // if(user_id == selected_agent_id){
                //     remaining_amount = parseFloat(remaining_amount) - parseFloat(referral_amount);
                // }
        }else{

            total =
            percentage.length > 0
                ? (parseFloat(remaining_amount) * parseFloat(percentage)) / 100
                : 0;
                remaining_amount = parseFloat(remaining_amount) - parseFloat(total);
        }
        $("#commission_amount_" + uuid).val(numberFormat(total) || "");
    });
    calculateSplitRemainingAmount();
}


$(document).on("select2:select", "#deal_split_agent_id", function (e) {
    let selectedValue = $(this).val();
    getParentUsers(selectedValue);
});

const removeSplit = (split_id) => {
    Swal.fire({
        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>' +
            '<div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4>' +
            '<p class="text-muted mx-4 mb-0">Are you Sure You want to remove this deal split from this deal ?</p>' +
            '</div></div>',


        showCancelButton: true,
        cancelButtonText: 'Cancel',
        cancelButtonClass: 'btn btn-warning action-close w-xs',

        showCloseButton: true,
        confirmButtonText: 'Delete',
        confirmButtonClass: 'btn btn-danger action-delete  w-xs me-2',

        buttonsStyling: false,

    }).then(function (result) {
        if (result.isConfirmed) {
            deleteDealSplit(split_id, deal_id);

        }
    })

}
const deleteDealSplit = (split_id, deal_id) => {
    let data = {
        id: split_id,
        deal_id: deal_id
    }
    axios.post(`/api/vue/v1/deal-split-remove`, data).then((response) => {
        getDealSplits(deal_id, true);
    });
}
const getDealReferrals = async (id) => {
        if (id == undefined || id == null || id == '') {
            return '';
        }
        $.ajax({
            url: route('admin.deals_revamped.referral.all') + '?deal_id=' + id,
            type: "GET",
            dataType: "json",
            success: function (result) {
                if (result.status == "1") {

                    let referrals = result.data
                    if(referrals.length > 0){

                        let html = '';
                              for(const key in referrals){

                                html += `<div v-if="split" class="row finRow">
          <div class="col-6 d-flex">

            <div class="flex-shrink-0">
              <div class="avatar-sm rounded bg-dark">
                <img src="/assets/images/logo/logo.png" style="height: 80%" alt="" class="member-img img-fluid d-block rounded p-2">
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <h6 class="fs-14 mb-1">${referrals[key].user.full_name}</h6>
              <p class="text-muted mb-0">${referrals[key].user.role_list[0]}</p>
            </div>

          </div>
          <div class="col">-</div>
          <div class="col-sm-auto"><div class="flex-shrink-0 text-end">
              <h6 class="fs-14 mb-1">AED &nbsp;${numberFormat(referrals[key].referral_amount)}</h6></div>
          </div></div>`

                              }
                            $("#dealReferralList").html(html);
                            $("#referralSection").removeClass('d-none');
                    }else{
                        $("#referralSection").addClass('d-none');
                    }

                }
            },
            error: function (jqXhr, json, errorThrown) {
                console.log(jqXhr);
                let data = jqXhr.responseJSON;
                if (data.errors) {
                    $.each(data.errors, function (index, item) {
                        showToastify(item[0], "error");
                    });
                }
                if (jqXhr.status == 500 || jqXhr.status == 400) {
                    showToastify(data.message, "error");
                }
            },
        });

        // let response = await axios.get(route('admin.deals_revamped.referral.all') + '?deal_id=' + id)
        // if (response.status == 200) {
        //     let referrals = response.data.data
        //     console.log(referrals.length)
        //     let html = '';
        //             for (let i = 0; i < referrals.length; i++) {
        //                console.log(referrals[i])
        //             }

        //         $("#dealReferralList").html(html);


        // } else {

        //     showToastify('Something went wrong', 'error')
        // }
    }

    const editSingleCommission = async (id) => {
        if (id == undefined || id == null || id == '') {
            return '';
        }
        let response = await axios.get(route('get.deal.split.commission') + '?split_id=' + id)
        if (response.status == 200) {
            if (response.data.status == '1') {
                let data = response.data.data;
                $("#singleUserDealCommissionModal").modal('show');
                $("#su_commission_id").val(data.id);
                $("#su_modal_deal_id").val(data.deal_id);
                $("#si_commission_agent_id").val(data.user_id);
                $("#su_commission_agent_name").html(data.user?.full_name);
                $(".su_commission_category").html(data.commission_category);
                let commType = '';
                if (data.commission_type == 'Percentage') {
                    commType = '%';
                } else {
                    commType = '/-';
                }

                $("#su_commission_type").html(commType);
                $("#su_commission_type_value").val(data?.commission_value);
                $("#su_commission_amount").val(data.commission);
                $(".su_edit_net_commission").html(numberFormat(deal.value.net_commission));
                $(".su_edit_split_amount").html(numberFormat(response?.data?.split?.split_amount));
                $("#su_edit_split_amount_input").val(response?.data?.split?.split_amount);


                $("#singleUserDealCommissionModal").find('.modal-title').html('Edit Deal Split Commission');
            } else {

            }


        } else {
            splits.value = []
            showToastify('Something went wrong', 'error')
        }
    }
    $(document).on('submit', '#splitSingleCommissionForm', function (event) {
        $("#suFormSubmitBtn").prop('disabled', true);
        event.preventDefault();
        var formData = new FormData($(this)[0]);
        //remove comma from split amount
        let split_amount = formData.get('split_amount');
        console.log(split_amount);
        // split_amount = String(split_amount).replace(/,/g, '');

        // formData.set('amount', split_amount);

        $.ajax({
            url: route('update.deal.split.commission.manual'),
            type: 'POST',
            dataType: 'json',
            data: formData,
            cache: false,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.status == '1') {
                    // getDealSplits(deal_id, true);

                    getDealSplits(deal_id);
                    showToastify(result.msg, "success")
                    $('#dealSplitCommissionModal').modal('hide'); //hide form modal
                    $("#suFormSubmitBtn").prop('disabled', false); //enable submit button
                    $('#splitSingleCommissionForm').trigger('reset'); //

                } else if (result.status == '2') {
                    $("#suFormSubmitBtn").prop('disabled', false);
                    $.each(result.errors, function (index, item) {
                        showToastify(item[0], "error")
                    })
                } else {
                    $("#suFormSubmitBtn").prop('disabled', false);
                    showToastify(result.msg, "error")
                }
            },
            error: function (jqXhr, json, errorThrown) {
                let data = jqXhr.responseJSON;
                $("#suFormSubmitBtn").prop('disabled', false);
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

