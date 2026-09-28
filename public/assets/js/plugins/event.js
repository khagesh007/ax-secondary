let eventTypes = [];
let eventFormMode = 'create';
let isEventUpdate = false;
let refreshBtnId = null;
let reoccurringDays = [];
$("#calendar_type").select2({
    placeholder: "Select Event Type",
    allowClear: true,
    dropdownParent: $('#event-modal')
})

$("#calendar_attendees").select2({
    placeholder: 'Select attendees',
    allowClear: true,
    closeOnSelect: false,
    templateResult: formatAgentSelect2,
    templateSelection: formatAgentSelect2,
    dropdownParent: $('#event-modal')});

function initalizeSelect2onEventInputs() {

}

function changeUserSelectbox() {
    $("#calendar_agent_id").getAgentSelect2('#event-modal');
}

function destroyEventModal() {
    $("#eventModalsDiv").html("");
    $(".modal-backdrop show").remove();
}

$(document).on("change", "#event_check_all_attendees", function () {
    if ($(this).is(":checked")) {
        $("#event_check_team_attendees").prop("checked", false);
        $("#calendar_attendees").val("").trigger("change");
    }
});
$(document).on("change", "#event_check_team_attendees", function () {
    if ($(this).is(":checked")) {
        $("#event_check_all_attendees").prop("checked", false);
        $("#calendar_attendees").val("").trigger("change");
    }


});

function getEvent(id) {
    eventObj = {};
    $("#form-event").removeClass("was-validated");
    $("#form-event").trigger("reset");
    if (isEventPage) {
        $("#form-event select").val("").trigger("change");
    }
    $.ajax({
        url: route('admin.events.get'),
        type: "get",
        async: false,
        data: {id: id},
        success: function (result) {
            if (result.status == '1') {
                eventObj = result.data;
                fetched = result.data;
            } else {
                eventObj = {};
            }
        }
    });
    return eventObj;
}

function showAddEventModal(info, refreshBtnId = null) {
    $("#form-event").trigger("reset");
    $("#form-event").removeClass("was-validated");
    getEventTypes();
    eventFormMode = 'create';
    isEventUpdate = false;
    eventObj = {};
    if (info?.type != null) {
        let selectEventType = eventTypes.find(x => x.name == info?.type);
        $("#calendar_type").val(selectEventType.id);
        $("#calendar_type").trigger("change");
        $("#calendar_type").select2({
            placeholder: "Select Event Type",
            allowClear: true,
            dropdownParent: $('#event-modal')
        });
    }
    if (info?.agent != null && info?.agent?.id != null) {
        if ($("#calendar_agent_id").find("option[value='" + info?.agent?.id + "']").length == 0) {
            $("#calendar_agent_id").append('<option value="' + info?.agent?.id + '" selected="true">' + info?.agent?.name + '</option>');
            $("#calendar_agent_id").val(info?.agent?.id).trigger("change");
        } else {
            $("#calendar_agent_id").val(info?.agent?.id).trigger("change");
        }
    }

    if (info?.contact != null && info?.contact?.id != null) {
        if ($("#calendar_contact_id").find("option[value='" + info?.contact?.id + "']").length == 0) {
            $("#calendar_contact_id").append('<option value="' + info?.contact?.id + '" selected="true">' + info?.contact?.name + '</option>');
        }
        $("#modalContactInputs").removeClass("d-none");
    } else {
        $("#modalContactInputs").addClass("d-none");
    }

    if (info?.lead != null && info?.lead?.id != null) {
        if ($("#calendar_lead_id").find("option[value='" + info?.lead?.id + "']").length == 0) {
            $("#calendar_lead_id").append('<option value="' + info?.lead?.id + '" selected="true">' + info?.lead?.name + '</option>');
        }
        $("#modalLeadInputs").removeClass("d-none");
    } else {
        $("#modalLeadInputs").addClass("d-none");
    }
    if (info?.property != null && info?.property?.id != null) {

        if ($("#calendar_property_id").find("option[value='" + info?.property?.id + "']").length == 0) {
            $("#calendar_property_id").append('<option value="' + info?.property?.id + '" data-location="' + info?.property?.location + '" data-ref_no="' + info?.property?.ref_no + '" selected="true">' + info?.property?.title + '</option>');
        }
        $("#calendar_property_id").val(info?.property?.id).trigger("change");
        $("#modalPropertyInput").removeClass("d-none");
    } else {
        $("#modalPropertyInput").addClass("d-none");
    }
    initalizeSelect2onEventInputs();
    generateEventTitle();
    getAttendees();
    // let start_date = info.date!=null ? moment(info.date).format("YYYY-MM-DD")+' 12:00:00' : moment().format("YYYY-MM-DD")+' 12:00:00'
    $("#start_time").val(globalDateTime(info.date)).trigger("change")
    $("#end_time").val(globalDateTime(moment(info.date).add(0.5, 'hours'))).trigger("change")
    $("#modal-title").text("Add Event")
    $("#btn-delete-event, #btn-edit-event").addClass("d-none")
    $("#btn-save-event").removeClass("d-none")
    if (refreshBtnId != null) {
        $("#refreshBtnId").val(refreshBtnId);
    }
    $("#event-modal").modal("show");
}

$(document).on("change", "#reminder_to_attendees", function () {
    //checkbox checked
    if ($(this).is(":checked")) {
        $("#reminder_to_attendees").val("1");
        $(".reminder-sec").removeClass("d-none");
        $("#calendar_reminder").val("").focus();
    } else {
        $("#reminder_to_attendees").val("0");
        $(".reminder-sec").addClass("d-none");
        $("#calendar_reminder").val("");
    }
});
//Recurring event section ------======---------=======-------======------------========--------
const getEventSummary = () => {
    //route calendar.form.event.summary
   // console.log('summary generating..')
    $.ajax({
        url: "/events/get-event-summary",
        type: "post",
        data: {
            start_time : $("#calendar_start_time").val(),
            recurring_end_date : $("#recurring_end_date").val(),
            onDate : $("#recurring_date").val(),
            recurring_frequency : $("#recurring_frequency").val(),
            recurring_count : $("#event_recurring_count").val(),
            reoccurringDays : reoccurringDays,
        },
        success: function (result) {
            if (result.status == '1') {
                $("#event-occurring-summar").html(result.message);
            }
        }
    });
}
$(document).on("change", "#calendar_start_time, #recurring_end_date, #recurring_date, #recurring_frequency,#event_recurring_count", function () {
    console.log('summary generating..')
    getEventSummary();

});
const recurringDataChanged = () => {
    let recurring_frequency = $("#recurring_frequency").val();
    let summaryText = "";
    if (recurring_frequency == "daily") {
        summaryText = "Every day";
    } else if (recurring_frequency == "weekly") {
        let days = reoccurringDays.map(item => {
            return item;
        });
        summaryText = "Every " + days.join(", ");
    } else if (recurring_frequency == "monthly") {
        summaryText = "Every month";
    } else if (recurring_frequency == "yearly") {
        summaryText = "Every year";
    }
    $(".recurring_summary").html(summaryText);

}

$(document).on('select2:select', "#recurring_frequency", function (e) {

   let selectedValue = $("#recurring_frequency").val();

   if(selectedValue === "daily"){

      $(".rec-days").addClass("active");
      $("#daysSunday").removeClass("active");
       reoccurringDays = [];
       addRecurringDays("mon");
       addRecurringDays("tue");
       addRecurringDays("wed");
       addRecurringDays("thu");
       addRecurringDays("fri");
       addRecurringDays("sat");
       $("#occurences_label").text("Days");

       $("#event_recurring_count").attr("disabled", true);
      // $("#recurring_date").attr("readonly", true);
         $("#recurring_end_date").attr("readonly", false);

    }else if(selectedValue == "weekly"){
       $(".rec-days").removeClass("active");
       $("#occurences_label").text("Weeks");
       reoccurringDays = [];
   }else if(selectedValue == "monthly"){
       $(".rec-days").removeClass("active");
       reoccurringDays = [];
       $("#event_days_list").addClass("d-none");
   }else{
       $(".rec-days").removeClass("active");
       reoccurringDays = [];
   }
    if($("#recurring_frequency").val() != "" || $("#recurring_frequency").val() != null || $("#recurring_frequency").val() != undefined || $("#recurring_frequency").val() != "null" || $("#recurring_frequency").val() != "undefined"){
        $("#recurring_date").val("");
        // $(".rec-days").removeClass("active");
        reoccurringDays = [];
        $("#event_recurring_count").prop("disabled", false);
        $("#recurring_end_date").prop("disabled", false);
    }
});
$(document).on("change", "#recurring_date", function () {
    $("#recurring_frequency").val("").trigger("change");
    $("#event_recurring_count").prop("disabled", false);
    $("#recurring_end_date").prop("disabled", false);
    //disable recurring count and end date
    if ($(this).val() != "") {
        $("#event_recurring_count").prop("disabled", true);
        $("#recurring_end_date").prop("disabled", true);
        $("#recurring_end_date").prop("");
        $(".rec-days").removeClass("active");
        reoccurringDays = [];
    }else {
        $("#event_recurring_count").prop("disabled", false);
        $("#recurring_end_date").prop("disabled", false);
    }
});
$(document).on("change", "#event_recurring_checkbox", function () {
    console.log("recurring")
    //checkbox checked
    if ($(this).is(":checked")) {
        $("#event_recurring_checkbox").val("1");
        $(".recurring-sec").removeClass("d-none");
        $("#calendar_reminder").val("").focus();
        $("#recurring_frequency").select2({
            placeholder: "Select Frequency",
            allowClear: true,
            dropdownParent: $('#event-modal')
        });
    } else {
        $("#event_recurring_checkbox").val("0");
        $(".recurring-sec").addClass("d-none");
        $("#calendar_reminder").val("");
    }
});
$(document).on("click", ".rec-days", function () {
    if ($(this).hasClass("active")) {
        $(this).removeClass("active");
    } else {
        $(this).addClass("active");
    }
});
const addRecurringDays = (day) => {
    if (!reoccurringDays.includes(day)) {
        reoccurringDays.push(day);
    } else {
        reoccurringDays = reoccurringDays.filter(item => item !== day);
    }
    //console.log(reoccurringDays);
    //call summary generator function
    getEventSummary()

}

//Recurring event section end ------======---------=======-------======------------========--------

function showEventViewModal(id) {
    eventFormMode = 'view';
    attendeesHtml = '';
    getEventTypes();
    $("#attendeesList").html("");
    let eventdb = eventObj = getEvent(id);
    //eventObj = getEvent(id);

    if (!$.isEmptyObject(eventdb)) {
        let priority = "";
        if (eventdb.priority == 'High') {
            priority =
                `<span class="badge bg-soft-danger text-danger align-middle">High</span>`;
        } else if (eventdb.priority == 'Medium') {
            priority =
                `<span class="badge bg-soft-warning text-warning align-middle">Medium</span>`;
        } else if (eventdb.priority == 'Low') {
            priority =
                `<span class="badge bg-soft-success text-success align-middle">Low</span>`;
        }
        let typeBadge = generateEventTypeBadge(eventdb.type?.name);

        $(".calendarViewPriority").html(priority);
        $("#event-type-tag").html(typeBadge);
        if (eventdb.agent_id != null) {
            $("#viewAgentHtml").html(`<a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="${eventdb?.agent?.full_name}" data-bs-placement="top" aria-label="${eventdb?.agent?.first_name}" data-bs-original-title="${eventdb?.agent?.first_name}">
                                                <img src="${eventdb?.agent?.image_url}" alt="" class="rounded-circle avatar-xxs">
                                            </a>`);
        }
        if (eventdb.attendees != null) {
            if (eventdb.attendees.length > 5) {
                //attendeesHtml = 'more than 5 attendees';
                let attendee = eventdb.attendees;
                for (let i = 0; i <= 6; i++) {
                    attendeesHtml += `<a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="${attendee[i]?.full_name}" data-bs-placement="top" aria-label="${attendee[i].full_name}" data-bs-original-title="${attendee[i].full_name}">
                                                    <img src="${attendee[i].image_url}" alt="" class="rounded-circle avatar-xxs">
                                                </a>`
                }
                let moreAttendees = parseInt(eventdb.attendees.length) - 7;
                attendeesHtml += ` <span style="margin-top: 5px;"> & ` + moreAttendees + ` more attendees </span>`;
            } else {
                let attendeesHtml = "";
                eventdb.attendees.forEach(function (attendee) {
                    attendeesHtml += `<a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="${attendee?.full_name}" data-bs-placement="top" aria-label="${attendee.full_name}" data-bs-original-title="${attendee.full_name}">
                                                    <img src="${attendee.image_url}" alt="" class="rounded-circle avatar-xxs">
                                                </a>`

                })
            }
            $("#attendeesList").html(attendeesHtml);
        }
        if (eventdb.lead_id != null && eventdb.lead != null) {
            $("#event-lead-block").removeClass("d-none");
            $("#event-lead-tag").html("Lead: " + (eventdb?.lead?.first_name || "") + " " + (eventdb?.lead?.last_name || ""));
        }
        if (eventdb.property_id != null && eventdb.property != null) {
            $("#event-property-block").removeClass("d-none");
            $("#event-property-tag").html("Listing: (" + (eventdb?.property?.ref_no || "") + ") " + (eventdb?.property?.title || ""));
        }
        if (eventdb.contact_id != null && eventdb.contact != null) {
            $("#event-contact-block").removeClass("d-none");
            $("#event-contact-tag").html("Contact: (" + (eventdb?.contact?.first_name || "") + ") " + (eventdb?.contact?.last_name || ""));
        }
        $("#event-start-date-tag").html(moment(eventdb.start_time).format("ddd, DD MMM YYYY hh:mm:ss A") + ` - ` + moment(eventdb.end_time).format("ddd, DD MMM YYYY hh:mm:ss A"));
        $("#event-created-date-tag").html(moment(eventdb.created_at).format("ddd, DD MMM YYYY hh:mm:ss A"));
        $("#event-location-tag").html(eventdb.location);
        $("#event-created-by-tag").html(`<a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="Marvin Njoroge" data-bs-placement="top" aria-label="Marvin" data-bs-original-title="Marvin">
                                                <img src="`+eventdb?.created_by?.image_url+`" alt="" class="rounded-circle avatar-xxs">
                                            </a>`+eventdb?.created_by?.full_name);
        $("#event-title-tag").html(eventdb.title);
        $("#event-description-tag").html(eventdb.description);
        $("#viewEventModal").modal("show")
        if (!isEventPage) {
            $("#edit-event-btn").addClass("d-none");
        } else {
            $("#edit-event-btn").removeClass("d-none");
        }
        $("#edit-event-btn").attr("onclick", "editEvent(" + eventdb.id + ")");
        $("#btn-delete-event, #btn-edit-event").removeClass("d-none");
        $("#btn-save-event").addClass("d-none");
    } else {

        showToastify("Event not found", "error")
    }

}

function deleteEvent() {


    let id = eventObj.id;
    Swal.fire({

        html: '<div class="mt-3"><lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon><div class="mt-4 pt-2 fs-15 mx-5"><h4>Are you Sure ?</h4><p class="text-muted mx-4 mb-0">You want to Delete this Event ?</p></div></div>',
        showCancelButton: !0,
        confirmButtonClass: "btn btn-danger w-xs me-2 mb-1",
        confirmButtonText: "Yes, Delete It!",
        cancelButtonClass: "btn btn-soft-success w-xs mb-1",
        buttonsStyling: !1,
        showCloseButton: !0


    })
        .then(function (t) {

            if (t.isConfirmed) {
                $.ajax({
                    url: route('admin.events.delete'),
                    type: 'POST',
                    dataType: 'json',
                    data: {id},
                    success: function (result) {
                        if (result.status == '1') { //if successfully deleted
                            showToastify(result.msg)
                            let refreshBtnId = $("#refreshBtnId").val() ?? null;
                            if (isEventPage) {
                                calendar.refetchEvents()
                            } else {
                                $(".event" + id).remove();
                                let event_type = $("#event-type-tag .badge").text();
                                if (event_type.text == "Meeting") {
                                    $('#log-meeting-tab').tab('show');
                                    $("#log-meeting-tab").click();
                                } else if (event_type.text == "Task") {
                                    $('#log-task-tab').tab('show');
                                    $("#log-task-tab").click();
                                } else if (event_type.text == "Viewing") {
                                    $('#log-viewing-tab').tab('show');
                                    $("#log-viewing-tab").click();
                                }


                                if (refreshBtnId != null && refreshBtnId != undefined && refreshBtnId != "") {
                                    $("#" + refreshBtnId).click();
                                }
                            }
                            $('#event-modal').modal('hide');
                            $('#viewEventModal').modal('hide');
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


}

function showEventEditForm() {
    getEventTypes();

    isEventUpdate = true;
    // console.log(eventObj);
    if (currentUserId == eventObj.agent_id) {
        $(".agent_select_input").addClass("d-none");
    } else {
        $(".agent_select_input").removeClass("d-none");
    }
    $("#calendar_agent_id").val(eventObj.agent_id).trigger("change");
    if (eventObj.agent != null) {

        $("#calendar_agent_id").append(`<option data-image="${eventObj.agent?.image || ""}" value="${eventObj.agent_id}" selected>${eventObj.agent.full_name}</option>`)
        $("#calendar_agent_id").select2({
            placeholder: 'Select contact',
            allowClear: true,
            dropdownParent: $('#event-modal'),
            templateResult: formatAgentSelect2,
            templateSelection: formatAgentSelect2
        }).trigger('change');
    }
    Object.keys(eventObj).map((index) => {
        if (index == 'id') {
            $("#event_id").val(eventObj[index]);
        }
        $(`#${index}`).val(eventObj[index]).trigger("change")
    })
    if (eventObj.reminder > 0) {
        $("#reminder_to_attendees").val("1");
        $(".reminder-sec").removeClass("d-none")
    } else {
        $("#reminder_to_attendees").val("0");
        $(".reminder-sec").addClass("d-none")
    }
    $("#calendar_reminder").val(eventObj.reminder);
    $("#calendar_priority").val(eventObj.priority);
    $("#event_input_location").val(eventObj.location);
    $("#calendar_type").val(eventObj.event_type_id).trigger("change");
    $("#send_notification").val(1).prop("checked", eventObj.send_notification);
    $("#reminder_to_attendees").val(1).prop("checked", eventObj.reminder_to_attendees);
    $("#calendar_start_time").val(moment(eventObj.start_time).format("DD-MM-YYYY HH:mm")).trigger("change")
    $("#calendar_end_time").val(moment(eventObj.end_time).add(1, 'hours').format("DD-MM-YYYY HH:mm")).trigger("change")
    $("#event-form-modal-title").text(eventObj.title)
    // getLeads(result.data.lead_id)
    setTimeout(function () {
        if (eventObj.lead != null) {
            let lead = eventObj.lead;
            let text = `${lead.first_name} ${lead?.last_name || ""} - ${lead?.mobile || ""} - ${lead?.email || ""}`;
            if ($("#calendar_lead_id").find("option[value='" + lead.id + "']").length) {
                $("#calendar_lead_id").val(eventObj.lead_id).trigger('change');

            } else {
                $("#calendar_lead_id").append('<option value="' + eventObj.lead_id + '" selected="true">' + text + '</option>');

            }
            $("#calendar_lead_id").val(eventObj.lead_id).trigger("change");
            $("#calendar_lead_id").select2({
                placeholder: 'Select lead',
                allowClear: true,
                dropdownParent: $('#event-modal'),
                templateResult: formatLeadSelect2,
                templateSelection: formatLeadSelect2
            }).trigger('change');
            $("#modalLeadInputs").removeClass("d-none");
        }
    }, 1000);
    setTimeout(function () {
        if (eventObj.contact != null) {
            let contact = eventObj.contact;
            let text = `${contact.first_name} ${contact?.last_name || ""} - ${contact?.mobile || ""} - ${contact?.email || ""}`;
            if ($("#calendar_contact_id").find("option[value='" + contact.id + "']").length) {
                $("#calendar_contact_id").val(contact.contact_id).trigger('change');

            } else {
                $("#calendar_contact_id").append('<option value="' + eventObj.contact_id + '" selected="true">' + text + '</option>');

            }
            $("#calendar_contact_id").val(eventObj.contact_id).trigger("change");
            $("#calendar_contact_id").select2({
                placeholder: 'Select contact',
                allowClear: true,
                dropdownParent: $('#event-modal'),
                templateResult: formatOwnerSelect2,
                templateSelection: formatOwnerSelect2
            }).trigger('change');
            $("#modalContactInputs").removeClass("d-none");
        }
    }, 1000)
    setTimeout(function () {
        if (eventObj.property != null) {
            $("#calendar_property_ids").select2();
            // {
            //     placeholder: 'Select property',
            //         allowClear: true,
            //     dropdownParent: $('#event-modal'),
            //     templateResult: formatPropertySelect2,
            //     templateSelection: formatPropertySelect2
            // }
            let edit_property = eventObj.property;
            let text = `${edit_property?.publish_status || ""} ${edit_property?.category || ""}  ${edit_property?.unit_no || ""} - ${edit_property?.location || ""} ${edit_property.ref_no}  ${edit_property.for}`;
            if ($("#calendar_property_ids").find("option[value='" + edit_property.id + "']").length) {
                $("#calendar_property_ids").val(edit_property.id).trigger('change');
            } else {
                $("#calendar_property_ids").append('<option value="' + eventObj.property_id + '" selected="true">' + text + '</option>');
            }
            $("#calendar_property_ids").val(eventObj.property_id).trigger("change");
            $("#modalPropertyInput").removeClass("d-none");
        }
    }, 1000)
    $("#calendar_attendees").val("").trigger("change");
    let attendees = [];
    // eventObj?.attendees.map(item => {
    //     attendees.push(item.id)
    // })
    setTimeout(function () {
        if (eventObj?.attendees != null) {
            eventObj?.attendees.map(item => {
                attendees.push(item.id)
                let text = `${item.first_name} ${item?.last_name || ""}`;
                if ($("#calendar_attendees").find("option[value='" + item.id + "']").length == 0) {
                    $("#calendar_attendees").append('<option value="' + item.id + '" selected="true">' + text + '</option>');
                }
            })
        }
        $("#calendar_attendees").val(attendees).trigger("change");
    }, 1000);


    $("#calendar_input_title").val(eventObj.title);
    $("#event-modal").modal("show");
    $("#viewEventModal").modal("hide")
    $("#btn-delete-event, #btn-edit-event").addClass("d-none");
    $("#btn-save-event").removeClass("d-none");
    setTimeout(function () {
        eventFormMode = 'edit';
    }, 2000)
}

function resetAgentDependent() {
    $('#calendar_contact_id').val("").trigger('change');
    $('#calendar_lead_id').val("").trigger('change');
    $('#calendar_contact_id').empty();
    $('#calendar_lead_id').empty();
    $("#calendar_property_id").val(null).trigger("change")
    $('#calendar_property_id').empty();
    $('#calendar_attendees').val("").trigger("change");
    $('#calendar_attendees').empty();
    // $("#event_input_location").val("");
}

$(document).on('select2:select', "#calendar_agent_id", function (e) {
    let data = e.params.data;
    let id = data.id;
    resetAgentDependent();
    //$("calendar_contact_id").contactSelect2('#event-modal', true, true,agent_id,'Select Contact');

    getContacts();
    getLeads();
    getAttendees();
    calendarGetProperties();

});
// $(document).on("change", "#calendar_agent_id", function () {
//     let agent_id = $(this).val();
//     //$("#calendar_type").trigger("change");
//     resetAgentDependent();
//     //$("calendar_contact_id").contactSelect2('#event-modal', true, true,agent_id,'Select Contact');
//
//     getContacts();
//     getLeads();
//     getAttendees();
//     calendarGetProperties();
// });
$(document).on("change", "#calendar_lead_id", function () {

    if (eventFormMode != 'view') {
        generateEventTitle();
    }
});
$(document).on("change", "#calendar_contact_id", function () {

    if (eventFormMode != 'view') {

        $("#calendar_contact_title").val($(this).find("option:selected").text());
        generateEventTitle();
    }
});



function generateTitleBase(contact, lead, property) {
    if (contact && !lead && !property) {
        return "Contact - " + contact.first_name + " " + contact.last_name;
    } else if (!contact && lead && !property) {
        return "Lead - " + lead.first_name + " " + lead.last_name;
    } else if (!contact && !lead && property) {
        return "Property - " + property.ref_no;
    } else if (contact && lead && !property) {
        return "Contact - " + contact.first_name + " " + contact.last_name + ", Lead - " + lead.first_name + " " + lead.last_name;
    } else if (contact && !lead && property) {
        return "Contact - " + contact.first_name + " " + contact.last_name + ", Property - " + property.ref_no;
    } else if (!contact && lead && property) {
        return "Lead - " + lead.first_name + " " + lead.last_name + ", Property - " + property.ref_no;
    } else {
        return "-";
    }
}

function generateEventTitle() {


    if (eventFormMode != 'view') {

        let title = '';

        let type = $("#calendar_type").select2("data")[0];
        let calendar_property_title = $("#calendar_property_title").val();
        $("#calendar_input_title").val(title || "");
        let property = isSelect2Initialized($("#calendar_property_ids")) ? $("#calendar_property_ids").select2("data")[0] : null;
        let contact = isSelect2Initialized($("#calendar_contact_id")) ? $("#calendar_contact_id").select2("data")[0] : null;
        let lead = isSelect2Initialized($("#calendar_lead_id")) ? $("#calendar_lead_id").select2("data")[0] : null;
        let location = $("#calendar_property_id").select2().find(":selected").data("location");
        let base = generateTitleBase(contact, lead, property);
        $("#calendar_input_title").val(type.text + " " + base);
        let leadtext = lead != null ? `${lead.text || ""}` : "";
        let contactText = contact != null ? `${contact.text || ""}` : "";

        if (property) {
            const propertyTitle = property.text || "";

            if (type.text === "Viewing") {
                if (lead) {
                    title = `Viewing arranged with ${leadtext || ""} for ${propertyTitle}`;
                } else {
                    title = `Viewing arranged for ${propertyTitle}`;
                }
            } else if (type.text === "Property Shoot") {
                if (contact) {
                    title = `A property shoot has been scheduled at ${propertyTitle} with ${contactText || ""}`;
                } else {
                    title = `A property shoot has been scheduled for ${propertyTitle}`;
                }
            }

        } else if (contact && type.text === "Meeting" && lead) {
            title = `Meeting arranged with contact ${contactText || ""} and lead ${leadtext || ""}`;
        } else if (contact && type.text === "Meeting") {
            title = `Meeting arranged with contact ${contactText || ""}`;
        } else if (lead && type.text === "Meeting") {
            title = `Meeting arranged with lead ${leadtext || ""}`;
        } else if (lead && contact && type.text === "Task") {
            title = `Some task has been scheduled with lead ${leadtext || ""} and contact ${contactText || ""}`;
        } else if (contact && type.text === "Task") {
            title = `Some task has been scheduled with ${contactText || ""}`;
        } else if (lead && type.text === "Task") {
            title = `Some task has been scheduled with ${leadtext || ""}`;
        } else if (lead && contact && type.text === "Schedule a call") {
            title = `A call has been scheduled with lead ${leadtext || ""} and contact ${contactText || ""}`;
        } else if (lead && type.text === "Schedule a call") {
            title = `A call has been scheduled with ${leadtext || ""}`;
        } else if (contact && type.text === "Schedule a call") {
            title = `A call has been scheduled with ${contactText || ""}`;
        } else if (contact && type.text === "Follow-up Reminder" && lead) {
            title = `Follow-up reminder has been set for contact ${contactText || ""} and lead ${leadtext || ""}`;
        } else if (contact && type.text === "Follow-up Reminder") {
            title = `Follow-up reminder has been set for contact ${contactText || ""}`;
        } else if (lead && type.text === "Follow-up Reminder") {
            title = `Follow-up reminder has been set for lead ${leadtext || ""}`;
        } else if (contact && type.text === "Property Shoot") {
            title = `A property shoot has been scheduled at ${property?.text || "N/A"} with ${contactText || ""}`;
        } else if (contact && type.text === "Video shoot Booking") {
            title = `A video shoot has been scheduled with ${contactText || ""}`;
        }


        if (location != null && location != undefined && location != "") {
            $("#event_input_location").val(location);
        }
        $("#calendar_input_title").val(title || "");

    }
}

function generateEventTypeBadge(type) {

    if (type == "Viewing") {
        return `<span class="badge bg-soft-success text-success">${type}</span>`
    } else if (type == "Meeting") {
        return `<span class="badge bg-soft-info text-info">${type}</span>`
    } else if (type == "Task") {
        return `<span class="badge bg-soft-warning text-warning">${type}</span>`
    } else if (type == "Schedule a call") {
        return `<span class="badge bg-soft-danger text-danger">${type}</span>`
    } else if (type == "Photographer Booking") {
        return `<span class="badge bg-soft-primary text-primary">${type}</span>`
    } else if (type == "Holiday/Leave") {
        return `<span class="badge bg-soft-success text-success">${type}</span>`
    } else {
        return `<span class="badge bg-soft-info text-info">${type}</span>`
    }
}

$(document).on("change", "#calendar_attendees", function (e) {
    let ids = $(this).val();
    if (ids.length > 0) {
        $("#send_notification_div").removeClass("d-none");
        $("#send_notification").prop("checked", true);
        $("#event_check_all_attendees").prop("checked", false);
        $("#event_check_team_attendees").prop("checked", false);
    } else {
        $("#send_notification_div").addClass("d-none");
        $("#send_notification").prop("checked", false);
    }
});
$(document).on('submit', '#form-event', function (event) {
    $("#btn-save-event").html(`<span class="d-flex align-items-center">
                    <span class="spinner-border flex-shrink-0" role="status">
                        <span class="visually-hidden">Loading...</span>
                            </span>
                                <span class="flex-grow-1 ms-2">
                                    Processing...
                                    </span>
                                </span>`);

    $("#btn-save-event").prop('disabled', true);
    event.preventDefault();
    let event_type = $("#calendar_type").select2("data")[0];
    let formData = new FormData($("#form-event")[0]);
    //append reoccurringDays
    formData.append("reoccurringDays", reoccurringDays);
    let refreshBtnId = $("#refreshBtnId").val() ?? null;

    $.ajax({
        url: isEventUpdate ? route('admin.events.update') : route('admin.events.store'),
        type: 'POST',
        dataType: 'json',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {


            if (result.status == '1') {
                showToastify(result.msg, "success")
                $("#btn-save-event").prop('disabled', false); //enable submit button
                $("#btn-save-event").html(`Save`);
                $('#form-event').trigger('reset'); //
                $("#event-modal").modal("hide");
                if (isEventPage) {
                    $("input").val("").trigger("change")
                    $("input").removeClass("is-invalid")
                    $("select").val("").trigger("change");
                    calendar.refetchEvents();
                } else {


                    if (event_type.text == "Meeting") {
                        $('#log-meeting-tab').tab('show');
                        $("#log-meeting-tab").click();
                    } else if (event_type.text == "Task") {
                        $('#log-task-tab').tab('show');
                        $("#log-task-tab").click();
                    } else if (event_type.text == "Viewing") {
                        $('#log-viewing-tab').tab('show');
                        $("#log-viewing-tab").click();
                    }
                }

                if (refreshBtnId != null && refreshBtnId != undefined && refreshBtnId != "") {
                    $("#" + refreshBtnId).click();
                }
            } else if (result.status == '2') {
                $("#btn-save-event").prop('disabled', false);
                $("#btn-save-event").html(`Save`);
                let error_message = '';
                $.each(result.errors, function (key, value) {
                    error_message += '' + key + ': ' + value + ' | ';
                });
                showToastify(error_message, "error")
            } else {
                $("#btn-save-event").prop('disabled', false);
                $("#btn-save-event").html(`Save`);
                showToastify(result.msg, "error")
            }
        },
        error: function (jqXhr, json, errorThrown) {
            $("#btn-save-event").prop('disabled', false);
            $("#btn-save-event").html(`Save`);
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


function getEventTypes() {
    $.ajax({
        url: route('admin.events.type.get.all'),
        type: "get",
        async: false,
        dataType: 'json',
        success: function (result) {
            if (result.status == '1') {
                $("#calendar_type").html("");
                let options = "";
                result?.data.map((item, index) => {
                    options += '<option value="' + item.id + '">' + item.name + '</option>';
                });
                eventTypes = result?.data;
                $("#calendar_type").append(options);

            }
        }
    });
}

function isSelect2Initialized(element) {
    return $(element).data("select2") !== undefined;
}


//var hasSelect2 = isSelect2Initialized($(this));

$(document).on("change", "#calendar_type", function () {

    if (eventFormMode != 'view') {
        $("#calendar_property_ids").val("");
        $("#calendar_property_title").val("");
        // $("#calendar_contact_id").val("");
        $("#send_notification").val(1).prop("checked", true);
        let agent_id = $("#calendar_agent_id").val();
        $("#calendar_input_title, #event_input_location").val("");
        let selectedEventType = $("#calendar_type").select2("data")[0];
        let selectedType = $(this).find(':selected').text() ?? selectedEventType.text;

        if (selectedEventType != null && selectedEventType != undefined) {

            resetAgentDependent();

            //let selectedEventType = eventTypes.find(x => x.id == $(this).val());
            if (selectedType == 'Viewing') {
                $("#modalContactInputs").addClass('d-none');
                $("#modalPropertyInput, #modalLeadInputs").removeClass('d-none');
                if (isEventPage) {
                    calendarGetProperties();
                    getLeads();
                }
            } else if (selectedType == 'Schedule a call' || selectedType == 'Task') {
                $("#modalLeadInputs, #modalContactInputs, #modalPropertyInput").removeClass('d-none');
                getContacts();
                getLeads();

            } else if (selectedType == 'Meeting') {
                $("#modalLeadInputs, #modalContactInputs").removeClass('d-none');
                getContacts();
                getLeads();
            } else if (selectedType == 'Follow-up Reminder') {
                $("#modalLeadInputs, #modalContactInputs, #modalPropertyInput").removeClass('d-none');
                getContacts();
                getLeads();
                if (isEventPage) {
                    calendarGetProperties();

                }
            } else if (selectedType == 'Property Shoot') {
                $("#modalPropertyInput, #modalContactInputs").removeClass('d-none');
                $("#modalLeadInputs").addClass('d-none');
                if (isEventPage) {
                    calendarGetProperties();

                }
            } else {
                $("#modalPropertyInput, #modalContactInputs, #modalLeadInputs").addClass('d-none');

            }
        }
    }
    generateEventTitle();
});


function getContacts() {


    isSelect2Initialized($("#calendar_contact_id")) ? $('#calendar_contact_id').select2('destroy') : '';
    $('#calendar_contact_id').select2({
        placeholder: 'Select contact',
        allowClear: true,
        dropdownParent: $('#event-modal'),
        templateResult: formatOwnerSelect2,
        templateSelection: formatOwnerSelect2,
        ajax: {
            url: route('ajax.contacts.select2'),
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
                    search: params.term,
                    agent_id: $("#calendar_agent_id").val(),
                }
            },
            processResults({data}) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: `${item.first_name || ""} ${item.last_name || ""}`,
                            id: item.id,
                            first_name: item.first_name,
                            last_name: item.last_name,
                            full_name: item.first_name + ' ' + item.last_name,
                            mobile: item.mobile,
                            email: item.email
                        }
                    })
                }
            }
        }
    });
}

function getLeads(id = null) {
    isSelect2Initialized($("#calendar_lead_id")) ? $('#calendar_lead_id').select2('destroy') : '';

    $('#calendar_lead_id').select2({
        placeholder: 'Select lead',
        allowClear: true,
        dropdownParent: $('#event-modal'),
        templateResult: formatLeadSelect2,
        templateSelection: formatLeadSelect2,
        ajax: {
            url: route('ajax.leads.select2'),
            dataType: 'json',
            type: 'GET',
            data: function (params) {
                return {
                    search: params.term,
                    agent_id: $("#calendar_agent_id").val(),
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
                            email: item.email
                        }
                    })
                }
            }
        }
    });
    if (id != null) {
        $('#calendar_lead_id').val(id).trigger('change');
    }
}

$(document).on("change", "#calendar_property_ids", function () {
    if (eventFormMode != 'view') {
        $("#calendar_property_title").val($(this).find(':selected').text());
        let property_id = $(this).val();
        if (property_id != null) {
            $.ajax({
                url: eventPropertyDetailsUrl,
                type: "get",
                data: {id: property_id},
                dataType: 'json',
                success: function (result) {

                    if (result.status == '1') {
                        $("#event_input_location").val(result.data?.location);
                        // $("#calendar_contact_id").val("").trigger("change");
                        // if (result.data.owner_id != null && result.data.owner != null) {
                        //     let text = `${result.data.owner.first_name} ${result.data.owner?.last_name || ""}`;
                        //     if ($("#calendar_contact_id").find("option[value='" + result.data.owner_id + "']").length == 0) {
                        //         $("#calendar_contact_id").append('<option value="' + result.data.owner_id + '" selected="true">' + text + '</option>');
                        //     }
                        //     $("#calendar_contact_id").val(result.data.owner_id).trigger("change");
                        // }
                        generateEventTitle();
                        // $("#title").val("Viewing " + result.data?.title + " (" + result.data?.ref_no + ")");
                    }
                }
            });
        }
    }
});

function getEventModal(data, refreshBtnId = null) {
    // let agent_id = null;

    $.ajax({
        url: route('admin.events.calendar.form.modal'),
        type: 'GET',
        dataType: 'json',
        async: false,
        data: {data},
        cache: false,

        success: function (result) {
            if (result.status) {
                $("#eventModalsDiv").html(result.html);
                $("#event-modal").modal('show');
                $("#calendar_priority, #reminder_frequency, #calendar_type").select2({
                    dropdownParent: $('#event-modal'),
                });


                if (data?.user == null || data?.user == undefined || data?.user == {} || data?.user == "") {
                    getAttendees();
                    // if(data.type == null && $("#calendar_type").val() == null){
                    //     let currentValue = $("#calendar_agent_id").val();
                    //     $("#calendar_agent_id").val('');
                    //     $("#calendar_agent_id").val(currentValue).trigger("change");
                    // }
                    $("#calendar_agent_id").getAgentSelect2('#event-modal');
                    if (data.type != null && data.type == "Viewing") {
                        if (data.lead == null || data.lead == undefined || data.lead == {} || data.lead == "") {
                            getLeads();
                        }
                        if (result.property) {
                            $("#calendar_property_ids").select2({
                                placeholder: 'Select property',
                                allowClear: true,
                                dropdownParent: $('#event-modal')
                            });
                        }
                        if (isEventPage) {
                            calendarGetProperties();
                            getLeads();
                        }

                    } else if (data.type != null && data.type == "Property Shoot") {
                        calendarGetProperties();
                    } else if (data.type != null && (data.type == "Meeting")) {
                        $("#modalPropertyInput").addClass("d-none");
                        getContacts();
                        getLeads();
                    } else if (data.type != null && (data.type == "Follow-up Reminder")) {
                        $("#modalPropertyInput").removeClass("d-none");
                        calendarGetProperties();
                        getContacts();
                        getLeads();
                    }

                } else {

                    getAttendees();
                    if (!isSelect2Initialized($("#calendar_agent_id"))) {
                        $("#calendar_agent_id").select2({
                            placeholder: 'Select user',
                            allowClear: true,
                            dropdownParent: $('#event-modal'),
                            templateResult: formatAgentSelect2,
                            templateSelection: formatAgentSelect2
                        }).trigger('change');
                    }
                    if (!isSelect2Initialized($("#calendar_contact_id"))) {
                        $("#calendar_contact_id").select2({
                            placeholder: 'Select contact',
                            allowClear: true,
                            dropdownParent: $('#event-modal'),
                            templateResult: formatOwnerSelect2,
                            templateSelection: formatOwnerSelect2
                        });
                    }
                    if (!isSelect2Initialized($("#calendar_lead_id"))) {
                        $("#calendar_lead_id").select2({
                            placeholder: 'Select lead',
                            allowClear: true,
                            dropdownParent: $('#event-modal'),
                            templateResult: formatLeadSelect2,
                            templateSelection: formatLeadSelect2
                        });
                    }


                    if (data.type != null && data.type == "Viewing") {
                        if (data.lead == null || data.lead == undefined || data.lead == {} || data.lead == "") {
                            getLeads();
                        }
                        if (result.property) {
                            $("#calendar_property_ids").select2({
                                placeholder: 'Select property',
                                allowClear: true,
                                dropdownParent: $('#event-modal')
                            });
                        }
                        if (isEventPage) {
                            calendarGetProperties();
                            getLeads();
                        }

                    } else if (data.type != null && data.type == "Meeting") {
                        $("#modalPropertyInput").addClass("d-none");
                        getContacts();
                        getLeads();
                    } else if (data.type != null && data.type == "Task") {
                        getContacts();
                        getLeads();
                    } else if (data.type != null && data.type == "Property Shoot") {

                        calendarGetProperties();
                    } else if (data.type != null && data.type == "Follow-up Reminder") {
                        $("#modalPropertyInput").removeClass("d-none");

                        if (data.property != null && data.property != undefined && data.property != "") {

                            if (currentPage == "Listing") {
                                $("#calendar_contact_id").val("").trigger("change");
                                $("#calendar_input_title").val("Follow-up reminder has been set for ")
                            } else {
                                calendarGetProperties();
                            }
                        }
                        if (result.property) {

                            $("#calendar_property_ids").select2({
                                placeholder: 'Select property',
                                allowClear: true,
                                dropdownParent: $('#event-modal')
                            });
                        }
                        if (data.lead == null || data.lead == undefined || data.lead == {} || data.lead == "") {
                            getLeads();
                        }
                        if (data.contact == null || data.contact == undefined || data.contact == {} || data.contact == "") {
                            getContacts();
                        }

                        if (isEventPage) {
                            calendarGetProperties();
                        }
                    }
                }
                //  let start_date =  moment().format("YYYY-MM-DD")+' 12:00:00'
                flatpickr('#calendar_start_time', {
                    "minDate": new Date(),
                    enableTime: true,
                    dateFormat: "d-m-Y H:i",
                    allowInput: true,
                    static: true,
                    onChange: function () {
                        //add 30 minutes to start date
                        // let startDate = new Date($("#calendar_start_time").val());
                        let startDate = $("#calendar_start_time").val();
                        let givenDateStr = dateFormatHandle(startDate);
                        $("#calendar_end_time").val(moment(givenDateStr).add(30, 'minutes').format('DD-MM-YYYY HH:mm'));

                        flatpickr('#calendar_end_time', {
                            minDate: givenDateStr, // Use 'date' instead of 'this.value'
                            enableTime: true,
                            static: true,
                            allowInput: true,
                            dateFormat: "d-m-Y H:i",
                        });
                    }

                });
                flatpickr('#recurring_date', {
                    minDate: new Date(),
                    enableTime: false,
                    static: true,
                    allowInput: true,
                    dateFormat: "d/m/Y",
                });
                if (refreshBtnId != null) {

                    $("#refreshBtnId").val(refreshBtnId);
                }
                if (result.title == '') {
                    generateEventTitle();
                }
            } else {
                console.log(result.status);
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

}

function getAttendees() {
    $('#calendar_attendees').select2({
        placeholder: 'Select attendees',
        allowClear: true,
        closeOnSelect: false,
        templateResult: formatAgentSelect2,
        templateSelection: formatAgentSelect2,
        dropdownParent: $('#event-modal'),
        ajax: {
            url: route('ajax.getUsersSelect2'),
            dataType: 'json',
            type: 'POST',
            data: function (params) {
                return {
                    term: params.term,
                    agent_id: $("#calendar_agent_id").val(),
                }
            },
            processResults({data}) {
                let options = '';
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: `${item.text}`,
                            id: item.id,
                            mobile: item.mobile,
                            email: item.email,
                            image: item.image,
                        };



                    })
                }
            }
        }
    });
}

function calendarGetProperties() {
    let id = $("#calendar_agent_id").val();
    if (id != null) {
        $('#calendar_property_ids').propertySelect2('#event-modal', true, true, id, 'Select property');

    }
}

// function selectEventType(type){
//     eventTypes.map((item, index) => {
//         if(item.name == type){
//             $("#calendar_type").val(item.id).trigger("change");
//         }
//     });
// }
