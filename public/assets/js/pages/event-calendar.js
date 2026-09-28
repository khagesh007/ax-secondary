
$("#filter_agents").select2({
    placeholder: "Select users",
    allowClear: true,
    closeOnSelect: false,
})
$("#filter_type").select2({
    placeholder: "Select types",
    allowClear: true,
    closeOnSelect: false,
})
$("#calendar_agent_id").select2({
    placeholder: "Select agent",
    allowClear: true,
    dropdownParent: $('#event-modal')
})
flatpickr('#start_time', {
    "minDate": new Date(),
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    onChange: function(){
        console.log($("#start_time").val())
        //set min date for end date
        flatpickr('#end_time', {
            "minDate": new Date($("#start_time").val()),
            enableTime: true,
            dateFormat: "Y-m-d H:i",
        });
        }

});

flatpickr('#end_time', {
    "minDate": new Date(),
    enableTime: true,
    dateFormat: "Y-m-d H:i",
});



$("#priority").select2({
    placeholder: "Select priority",
    allowClear: true,
    dropdownParent: $('#event-modal')
})

$("#calendar_attendees").select2({
    placeholder: "Select attendees",
    allowClear: true,
    closeOnSelect: false,
    dropdownParent: $('#event-modal')
})

$("#reminder_frequency").select2({
    placeholder: "Select reminder frequency",
    allowClear: true,
    dropdownParent: $('#event-modal')
})
$("#calendar_property_id").select2({
    placeholder: "Select property",
    allowClear: true,
    dropdownParent: $('#event-modal')
})
$("#calendar_lead_id").change(function () {
    if (formMode != 'view') {
        let title = '';
        let leads = $(this).select2('data');
        let property = $("#calendar_property_id").select2('data');
        generateEventTitle();


    }
});








// $(document).on('submit', '#form-event', function (event) {
//     $("#btn-save-event").prop('disabled', true);
//     event.preventDefault();
//     let formData = new FormData($("#form-event")[0]);
//     $.ajax({
//         url: isEventUpdate ? eventUpdateUrl : eventCreateUrl,
//         type: 'POST',
//         dataType: 'json',
//         data: formData,
//         cache: false,
//         processData: false,
//         contentType: false,
//         success: function (result) {
//             if (result.status == '1') {
//                 showToastify(result.msg, "success")
//                 $("#btn-save-event").prop('disabled', false); //enable submit button
//                 $('#form-event').trigger('reset'); //
//                 $("input").val("").trigger("change")
//                 $("input").removeClass("is-invalid")
//                 $("select").val("").trigger("change");
//                 $("#event-modal").modal("hide");
//                 calendar.refetchEvents();
//             } else if (result.status == '2') {
//                 $("#btn-save-event").prop('disabled', false);
//                 let error_message = '';
//                 $.each(result.errors, function (key, value) {
//                     error_message += '' + key + ': ' + value + ' | ';
//                 });
//                 showToastify(error_message, "error")
//             } else {
//                 $("#btn-save-event").prop('disabled', false);
//                 showToastify(result.msg, "error")
//             }
//         },
//         error: function (jqXhr, json, errorThrown) {
//             $("#btn-save-event").prop('disabled', false);
//             let data = jqXhr.responseJSON;
//             if (data.errors) {
//                 $.each(data.errors, function (index, item) {
//                     showToastify(item[0], "error")
//                 })
//             }
//             if (jqXhr.status == 500 || jqXhr.status == 400) {
//                 showToastify(data.message, "success")
//             }
//         },
//     });
// });
