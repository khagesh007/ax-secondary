/* additional agent section */
$("#add_on_agent").on("change", function () {
    let agent_id = $(this).val();
    let contact_id = $("#id").val();
    if (isUpdate) {
        if (!agent_id) {
            return;
        }
        $.ajax({
            url: add_more_agent_url,
            type: "POST",
            dataType: "json",
            data: { contact_id: contact_id, agent_id: agent_id },
            success: function (result) {
                if (result.status == "1") {
                    showToastify(result.msg, "success");

                    $("#agent_table").append(
                        ` <tr>
                                    <td class="d-flex">
                                        <img src="` +
                            result.agent?.thumb_url +
                            `" alt="" class="avatar-xs rounded-3 shadow me-2">
                                        <div>
                                            <h5 class="fs-13 mb-0">` +
                            result.agent?.name +
                            `</h5>
                                            <p class="fs-12 mb-0 text-muted">` +
                            result.agent?.email +
                            `</p>
                                        </div>
                                    </td>
                                    <td>
                                        ` +
                            result.contact_agent?.created_by?.name +
                            `<br>
                                        <span class="text-muted fs-7">` +
                            globalDateTime(result.contact_agent?.created_at) +
                            `</span>
                                    </td>

                                    <td style="width:5%;">
                                        <a href="javascript:void(0)" onclick="removeAgent(${result.contact_agent?.id})" data-id=` +
                            result.contact_agent?.id +
                            ` ><i class="bx bxs-trash text-danger fs-4"></i></a>
                                    </td>
                                </tr>`
                    );
                } else {
                    showToastify(result.msg, "error");
                }
            },
            error: function (result) {
                console.log(result);
            },
        });
    }
});

function viewAgentList(id) {
    $.ajax({
        url: fetch_additional_agents,
        type: "get",
        data: { contact_id: id },
        success: function (result) {
            if (result.status == "1") {
                $("#agent_table").html("");
                Object.keys(result.agents).map((index) => {
                    if (
                        (call_now_button == 1 || call_now_button == 3) &&
                        globalUser.id == result.agents[index].agent?.id
                    ) {
                        $("#call_now_button").removeClass("d-none");
                    }
                    $("#agent_table").append(
                        `<tr>
                                    <td class="d-flex"><img src="` +
                            result.agents[index].agent?.thumb_url +
                            `" alt="" class="avatar-xs rounded-3 shadow me-2">
                                        <div>
                                            <h5 class="fs-13 mb-0">` +
                            result.agents[index].agent?.name +
                            `</h5>
                                            <p class="fs-12 mb-0 text-muted">` +
                            result.agents[index].agent?.email +
                            `</p>
                                        </div>
                                    </td>
                                    <td>
                                        ` +
                            result.agents[index].created_by?.name +
                            `<br>
                                        <span class="text-muted fs-7">` +
                            globalDateTime(result.agents[index].created_at) +
                            `</span>
                                    </td><td style="width:5%;">
                                        <a href="javascript:void(0)" onclick="removeAgent('` +
                            result.agents[index]?.id +
                            `')" data-id="` +
                            result.agents[index]?.id +
                            `" ><i class="bx bxs-trash text-danger fs-4"></i></a>
                                    </td>
                                </tr>`
                    );
                });
            }
        },
    });
}

function removeAgent(id) {
    if (canRemoveAdditionalAgents) {
        swal({
            title: "Are you sure?",
            text: "You want to remove this agent from this contact!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                // on confirm delete the category
                $.ajax({
                    url: remove_additional_agent,
                    type: "get",
                    data: { id: id },
                    success: function (result) {
                        if (result.status == "1") {
                            showToastify(result.msg, "success");
                            $("#agent_table").html("");
                            viewAgentList(dataObj?.id);
                        }
                    },
                });
            }
        });
    }
}

/* additional agent section end*/

/* followup realted code */
$(document).on("submit", "#meetingForm", function (event) {
    $("#meetingFormSubmitBtn").prop("disabled", true);
    event.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
        url: $("#meeting_id").val() ? meetingFormUpdateLink : meetingFormLink,
        type: "POST",
        dataType: "json",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {
            $("#meetingFormSubmitBtn").prop("disabled", false);
            if (result.status == "1") {
                let contact_id = $("#id").val();
                getMeetingLog(contact_id);
                showToastify(result.message, "success");
                $("#meetingModal").modal("hide"); //hide form modal
                $("#meetingForm").trigger("reset"); //
            } else if (result.status == "2") {
                showToastify(result.message, "error");
            } else {
                showToastify(result.message, "error");
            }
        },
        error: function (jqXhr, json, errorThrown) {
            $("#meetingFormSubmitBtn").prop("disabled", false);
            let data = jqXhr.responseJSON;
            if (data.errors) {
                $.each(data.errors, function (index, item) {
                    showToastify(item[0], "error");
                });
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(result.msg, "error");
            }
        },
    });
});
$(document).on("submit", "#taskForm", function (event) {
    $("#taskModalsubmitBtn").prop("disabled", true);
    event.preventDefault();
    var formData = new FormData($(this)[0]);
    $.ajax({
        url: $("#task_id").val() ? taskFormUpdateLink : taskFormLink,
        type: "POST",
        dataType: "json",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {
            $("#taskModalsubmitBtn").prop("disabled", false);
            if (result.status == "1") {
                let contact_id = $("#id").val();
                getTask(contact_id);
                showToastify(result.message, "success");
                $("#taskModal").modal("hide"); //hide form modal
                $("#taskForm").trigger("reset"); //
            } else if (result.status == "2") {
                showToastify(result.message, "error");
            } else {
                showToastify(result.message, "error");
            }
        },
        error: function (jqXhr, json, errorThrown) {
            $("#taskModalsubmitBtn").prop("disabled", false);
            let data = jqXhr.responseJSON;
            if (data.errors) {
                $.each(data.errors, function (index, item) {
                    showToastify(item[0], "error");
                });
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                toastr.error(data.message, "Error");
                showToastify(data.message, "error");
            }
        },
    });
});

function makeCall() {
    let contact_id = $("#id").val();
    let dailer_id = $("#dailer_id").val();
    var formData = new FormData();
    formData.append("contact_id", contact_id);
    formData.append("dailer_id", dailer_id);
    $.ajax({
        url: makeCallLink,
        type: "POST",
        dataType: "json",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.status == "1") {
                getCallLog(contact_id);
                getContactNotes(contact_id);
                showToastify(result.msg);
                $("#start-call-button").addClass("d-none");
                $("#calling-button").removeClass("d-none");
                $text =
                    "Call Started <br><br> <strong>" +
                    $("#first_name").val() +
                    " " +
                    $("#last_name").val() +
                    "</strong>";
                $("#call_id").html($text);
                $('#countdown').html("");
                setTimeout(() => {
                    $("#start-call-button").removeClass("d-none");
                    $("#calling-button").addClass("d-none");
                }, 5000);
            } else {
                showToastify(result.msg, "error");
            }
        },
        error: function (jqXhr, json, errorThrown) {
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
}

$(document).on("submit", "#assignAgentsForm", function (event) {
    event.preventDefault();
    let id = $("#id").val();
    var formData = new FormData($(this)[0]);
    formData.append("id", id);
    let buttons = $(this).find("button");
    $(buttons).prop("disabled", true);
    $.ajax({
        url: bulkAssignLink,
        type: "POST",
        dataType: "json",
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {
            $(buttons).prop("disabled", false);
            if (result.status == "1") {
                showToastify(result.msg, "success");
                getContact(id);
                $("#assignAgentsModal").modal("hide");
            } else if (result.status == "2") {
                let error_message = "";
                $.each(result.errors, function (key, value) {
                    error_message += "" + key + ": " + value + " | ";
                });
                showToastify(error_message, "error");
            } else {
                showToastify(result.msg, "error");
            }
        },
        error: function (jqXhr, json, errorThrown) {
            $(buttons).prop("disabled", false);
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
});
