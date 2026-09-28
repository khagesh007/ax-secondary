$(document).on("change", "#shoot_property_id", function () {
    if(shootFormMode != 'view') {
        let propertySelect2Data = $(this).select2('data')[0];
        $("#shoot_input_title").val("Property Shoot at- (" + propertySelect2Data.ref_no + ") " + propertySelect2Data.text);
    }
    });
$(document).on("change", "#shoot_agent_id", function () {
    $("#shoot_property_id").html('<option value="" disabled selected>Select Property</option>');
    $("#shoot_input_title").val("");
    $("#shoot_property_id").propertySelect2('#shootModal', true, true, $("#shoot_agent_id").val());
});


function getShootModal(agent_id = null,property_id = null) {
    // let agent_id = null;

    $.ajax({
        url: getShootFormModalUrl,
        type: 'GET',
        dataType: 'json',
        async: false,
        data: {agent_id: agent_id,property_id:property_id},
        cache: false,

        success: function (result) {
            if (result.status) {
                $("#shootFormDiv").html(result.html);
                $("#shootModal").modal('show');
                if(agent_id && property_id == null){
                let option = new Option(agent_id.text, agent_id.id, true, true);
                $("#shoot_agent_id").append(option).trigger('change');
                    $("#shoot_agent_id").val(agent_id).trigger('change');
                }else  if(agent_id && property_id){

                }else {
                    $("#shoot_agent_id").getAgentSelect2('#shootModal');
                }
                flatpickr('#shoot_preferred_time', {
                    "minDate": new Date(),
                    enableTime: true,
                    dateFormat: dateTimePickerFormat(),
                    allowInput: true,
                    static: true,
                });
                drawRequestedTag();
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

function shootViewModal(id) {
    if (id) {
        //write javascript redirect page code


        window.location = shootIndex + '?id=' + id;
        // $.ajax({
        //     url: viewShootBookingModal,
        //     type: 'GET',
        //     dataType: 'json',
        //     async: false,
        //     data: { id: id },
        //     cache: false,
        //     success: function(result) {
        //         if (result.status) {
        //             $( "#shootFormDiv" ).html(result.html);
        //             $("#viewShootingsModal").modal('show');
        //         }
        //         else{
        //             console.log(result.status);
        //         }
        //     },
        //     error: function(jqXhr, json, errorThrown) {
        //         let data = jqXhr.responseJSON;
        //         if (data.errors) {
        //             $.each(data.errors, function(index, item) {
        //                 showToastify(item[0], "error")
        //             })
        //         }
        //         if (jqXhr.status == 500 || jqXhr.status == 400) {
        //             showToastify(data.msg, "error")
        //         }
        //     },
        // });
    }
}

$(document).on('submit', '#shoot-form', function (event) {
    event.preventDefault();

    var $btnSaveShoot = $("#btn-save-shoot");
    $btnSaveShoot.prop('disabled', true);

    var formData = new FormData($(this)[0]);

    $.ajax({
        url: isShootUpdate ? shootFormUpdateUrl : shootFormUrl,
        type: 'POST',
        dataType: 'json',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        success: function (result) {
            $btnSaveShoot.prop('disabled', false); // enable submit button

            if (result.status === '1') {
                showToastify(result.msg, "success");
                $('#shoot-form').trigger('reset');
                $("#shootModal").modal('hide');
                // if(shootPage != '') {
                //     window.location = shootPage + '?id=' + result.data.id;
                // }
            } else if (result.status === '2') {
                var error_message = '';

                $.each(result.errors, function (key, value) {
                    error_message += '' + key + ': ' + value + ' | ';
                });

                showToastify(error_message, "error");
            } else {
                showToastify(result.msg, "error");
            }
        },
        error: function (jqXhr, json, errorThrown) {
            $btnSaveShoot.prop('disabled', false);

            var data = jqXhr.responseJSON;

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

function drawRequestedTag() {
    $("#shoot_requested_tags").select2({
        tags: true,
        tokenSeparators: [',', ' '],
       closeOnSelect: false,
    })

}
function shootingRequestStatus(status){
   //create a object or array of status and badge colors
    let statusBadge = {
        'Waiting for Approval': 'badge-soft-info',
        'In Progress': 'badge-soft-success',
        'Approved': 'badge-soft-success',
        'Rejected': 'badge-soft-danger',
        'Cancelled': 'badge-soft-danger',
        'Completed': 'badge-soft-success',

    }
    return statusBadge[status];
}
function showShootTNCModal(){
$("#shootModalTNC").modal("show");
}
function showShootModal(){
    $("#shootModalTNC").modal("hide");
    $("#shootModal").modal("show");
};
$(document).on("click", "#shootModalSubmitTNC", function () {
    $("#btn-save-shoot").trigger("click");
});

