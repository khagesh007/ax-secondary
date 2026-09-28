function getEventsCard(params,divElement = '#eventWidgetArea') {
    // let agent_id = null;

    $.ajax({
        url: route('admin.widget.events'),
        type: 'POST',
        dataType: 'json',
        async: false,
        data: {params},
        cache: false,

        success: function (result) {
            if (result.status) {
                divElement = divElement ? divElement : '#eventWidgetArea';
                $(divElement).html(result.html);
                if(params.call_log) {
                    if(params.model_key == 'lead_id') {
                        getLeadCallLogs(params.id, params.model_key, result.unique_id)
                    }else if(params.model_key == 'listing_lead_id') {
                        getListingLeadCallLogs(params.id, params.model_key, result.unique_id)
                    }else {
                        getCallLog(params.id, params.model_key, result.unique_id)
                    }
                }
               // getMeetingLog(params.id,'contact_id', result.unique_id)
                if(params.task_log) {
                    getTask(params.id, params.model_key, result.unique_id)
                }
                if(params.followup_log) {
                    getEventLog(params.id, params.model_key, result.unique_id, 'Follow-up Reminder', 'followupLogs')
                }
                if(params.meeting_log) {
                    getEventLog(params.id, params.model_key, result.unique_id, 'Meeting', 'MeetingLogTab')
                }
                if(params.viewing_log) {
                    getEventLog(params.id, params.model_key, result.unique_id, 'Viewing', 'viewingLogs')
                }
                if(params.combined_log) {
                    getEventLog(params.id, params.model_key, result.unique_id, 'Events', 'allLogs')
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
function getCallLog(id,model_id,unique_id) {

    let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="`+callIcon+`" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Call Records</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;
    $.ajax({
        url: route('admin.widget.events.call.log'),
        type: 'GET',
        dataType: 'json',
        data: {
            id: id,
            model_id: model_id
        },
        success: function(result) {

            if (result.status == '1') {
                $("#callLogTbody").html('');
                let html = '';
                let callIcon = '';
                let classColor = '';
                let duration = '0';
                let callStatus = '';
                Object.keys(result.call_log).map((index) => {
                    if (result.call_log[index].call_type == 'INCOMING') {
                        callIcon = '<i class="mdi mdi-phone-incoming"></i>';
                        classColor = 'bg-soft-success text-success';
                    } else if (result.call_log[index].call_type == 'OUTGOING') {
                        callIcon = '<i class="mdi mdi-phone-outgoing"></i>';
                        classColor = 'bg-soft-info text-info';
                    } else {
                        callIcon = '<i class="mdi mdi-phone-sync"></i>';
                        classColor = 'bg-soft-primary text-primary';
                    }
                    callStatus = result.call_log[index].call_answered_at != null ?
                        `<span
                                                                    class="badge bg-soft-success text-success align-middle">Answered</span>` :
                        `<span
                                                                    class="badge bg-soft-danger text-danger align-middle">Missed</span>`;
                    duration = ((result.call_log[index].call_duration || 0) / 60);
                    let audio = result.call_log[index].call_recording_url ? `<div class="px-2 fs-16" style="width: 50%">
                                                            <audio style="width: 100%" src="${result.call_log[index].call_recording_url}" controls></audio>
                                                        </div>` : "";
                    html += `<a href="#!" class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3">
                                                <span
                                                    class="avatar-title ` + classColor + ` rounded-circle fs-16">
                                                    ` + callIcon + `
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <h6 class="mt-0 mb-2 lh-base"><b>` + result.call_log[index].call_type +
                        `</b> ` + callStatus + `
                                                    <span class="text-secondary"> ${duration > 0 ? duration.toFixed(2) : duration}min</span></h6>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> ` + globalDateTime(result
                            .call_log[index].call_started_at) + `</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-16 d-none">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                            ${audio}
                                        </div>
                                    </a>`;
                });
                $("#callLogTbody"+unique_id).append(html);
                let element = document.getElementById("callLogTbody"+unique_id);
                if(element) {
                    element.scrollTop = element.scrollHeight;
                }

            } else {
                $("#callLogTbody"+unique_id).html(alertMsg);
            }
        },
        error: function(jqXhr, json, errorThrown) {
            $("#callLogTbody").html(alertMsg);
            let data = jqXhr.responseJSON;
            if (data.errors) {
                $.each(data.errors, function(index, item) {
                    showToastify(item[0], "error")
                })
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(data.message, "error")
            }
        },
    });
}
function getLeadCallLogs(id,model_id,unique_id) {
    // remove this code block
    let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="${callIcon}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Call Records</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;
    $.ajax({
        url: route('admin.work.leads.call.log'),
        type: 'GET',
        dataType: 'json',
        data: {
            id: id,
            model_id: model_id
        },
        success: function(result) {

            if (result.status == '1') {
                $("#callLogTbody"+unique_id).html('');
                let html = '';
                let callIcon = '';
                let classColor = '';
                let duration = '0';
                let callStatus = '';
                Object.keys(result.call_log).map((index) => {
                    if (result.call_log[index].call_type == 'INCOMING') {
                        callIcon = '<i class="mdi mdi-phone-incoming"></i>';
                        classColor = 'bg-soft-success text-success';
                    } else if (result.call_log[index].call_type == 'OUTGOING') {
                        callIcon = '<i class="mdi mdi-phone-outgoing"></i>';
                        classColor = 'bg-soft-info text-info';
                    } else {
                        callIcon = '<i class="mdi mdi-phone-sync"></i>';
                        classColor = 'bg-soft-primary text-primary';
                    }

                    callStatus = result.call_log[index].call_answered_at != null ?
                        `<span
                                                                    class="badge bg-soft-success text-success align-middle">Answered</span>` :
                        `<span
                                                                    class="badge bg-soft-danger text-danger align-middle">Missed</span>`;
                    duration = ((result.call_log[index].call_duration || 0) / 60);
                    let audio = '';
               if(leadsViewMyRecording) {
                   audio = result.call_log[index].call_recording_url ? `<div class="px-2 fs-16" style="width: 50%">
                                                            <audio style="width: 100%" src="${result.call_log[index].call_recording_url}" controls></audio>
                                                        </div>` : "";
               }

               var seconds = result.call_log[index].call_duration||0;
               var hours = Math.floor(seconds / 3600);
               seconds %= 3600;
               var minutes = Math.floor(seconds / 60);
               seconds %= 60;
               if(hours > 0) {
                     var time = hours + ":" + minutes + ":" + seconds;
               }else {
                    var time = minutes + ":" + seconds;
               }

                    html += `<a href="#!" class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3">
                                                <span
                                                    class="avatar-title ` + classColor + ` rounded-circle fs-16">
                                                    ` + callIcon + `
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <h6 class="mt-0 mb-2 lh-base"><b>` + result.call_log[index].call_type +
                        `</b> ` + callStatus + `
                                                    <span class="text-secondary"> ${time > 0 ? time : time}</span></h6>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> ` + globalDateTime(result
                            .call_log[index].call_started_at) + `</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-16 d-none">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                            ${audio}
                                        </div>
                                    </a>`;
                });
                $("#callLogTbody"+unique_id).append(html);
                let element = document.getElementById("callLogTbody"+unique_id);
               if(element) {
                   element.scrollTop = element.scrollHeight;
               }

            } else {
                $("#callLogTbody"+unique_id).html(alertMsg);
            }
        },
        error: function(jqXhr, json, errorThrown) {
            $("#callLogTbody").html(alertMsg);
            let data = jqXhr.responseJSON;
            if (data.errors) {
                $.each(data.errors, function(index, item) {
                    showToastify(item[0], "error")
                })
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(data.message, "error")
            }
        },
    });
}
function getListingLeadCallLogs(id,model_id,unique_id) {
    // remove this code block
    let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="${callIcon}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Call Records</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;
    $.ajax({
        url: route('admin.widget.events.call.log'),
        type: 'GET',
        dataType: 'json',
        data: {
            id: id,
            model_id: model_id
        },
        success: function(result) {

            if (result.status == '1') {
                $("#callLogTbody"+unique_id).html('');
                let html = '';
                let callIcon = '';
                let classColor = '';
                let duration = '0';
                let callStatus = '';
                Object.keys(result.call_log).map((index) => {
                    if (result.call_log[index].call_type == 'INCOMING') {
                        callIcon = '<i class="mdi mdi-phone-incoming"></i>';
                        classColor = 'bg-soft-success text-success';
                    } else if (result.call_log[index].call_type == 'OUTGOING') {
                        callIcon = '<i class="mdi mdi-phone-outgoing"></i>';
                        classColor = 'bg-soft-info text-info';
                    } else {
                        callIcon = '<i class="mdi mdi-phone-sync"></i>';
                        classColor = 'bg-soft-primary text-primary';
                    }

                    callStatus = result.call_log[index].call_answered_at != null ?
                        `<span
                                                                    class="badge bg-soft-success text-success align-middle">Answered</span>` :
                        `<span
                                                                    class="badge bg-soft-danger text-danger align-middle">Missed</span>`;
                    duration = ((result.call_log[index].call_duration || 0) / 60);
                    let audio = '';
               if(leadsViewMyRecording) {
                   audio = result.call_log[index].call_recording_url ? `<div class="px-2 fs-16" style="width: 50%">
                                                            <audio style="width: 100%" src="${result.call_log[index].call_recording_url}" controls></audio>
                                                        </div>` : "";
               }

               var seconds = result.call_log[index].call_duration||0;
               var hours = Math.floor(seconds / 3600);
               seconds %= 3600;
               var minutes = Math.floor(seconds / 60);
               seconds %= 60;
               var time = minutes + ":" + seconds;

                    html += `<a href="#!" class="text-reset notification-item d-block dropdown-item">
                                        <div class="d-flex">
                                            <div class="avatar-xs me-3">
                                                <span
                                                    class="avatar-title ` + classColor + ` rounded-circle fs-16">
                                                    ` + callIcon + `
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <h6 class="mt-0 mb-2 lh-base"><b>` + result.call_log[index].call_type +
                        `</b> ` + callStatus + `
                                                    <span class="text-secondary"> ${time > 0 ? time : time}</span></h6>
                                                <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                                                    <span><i class="mdi mdi-clock-outline"></i> ` + globalDateTime(result
                            .call_log[index].call_started_at) + `</span>
                                                </p>
                                            </div>
                                            <div class="px-2 fs-16 d-none">
                                                <input class="form-check-input" type="checkbox">
                                            </div>
                                            ${audio}
                                        </div>
                                    </a>`;
                });
                $("#callLogTbody"+unique_id).append(html);
                let element = document.getElementById("callLogTbody"+unique_id);
               if(element) {
                   element.scrollTop = element.scrollHeight;
               }

            } else {
                $("#callLogTbody"+unique_id).html(alertMsg);
            }
        },
        error: function(jqXhr, json, errorThrown) {
            $("#callLogTbody").html(alertMsg);
            let data = jqXhr.responseJSON;
            if (data.errors) {
                $.each(data.errors, function(index, item) {
                    showToastify(item[0], "error")
                })
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(data.message, "error")
            }
        },
    });
}

// function getMeetingLog(id,model_id,unique_id) {
//
//
//     let alertMsg = `<div class="card-animate">
//                                 <div class="card-body">
//                                     <div class="d-flex mb-3 text-center">
//                                         <div class="flex-grow-1">
//                                             <lord-icon src="`+meetingLogIcon+`" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
//                                         </div>
//
//
//                                     </div>
//                                     <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Meetings</span><small class="text-muted fs-13"></small></h3>
//                                     <h6 class="text-muted mb-0"></h6>
//                                 </div>
//                             </div>`;
//
//     $.ajax({
//         url: route('admin.events.all'),
//         type: 'POST',
//         dataType: 'json',
//         data: {
//             column: model_id,
//             value: id,
//             type: 'Meeting'
//         },
//         success: function(result) {
//             $("#meetingLogs"+unique_id).html('');
//
//
//             if (result.status == '1') {
//                 let html = '';
//                 let status = '';
//                 Object.keys(result.log).map((index) => {
//                     if (result.log[index].status == 'PENDING') {
//                         status = `Pending`;
//                     } else if (result.log[index].status == 'RUNNING') {
//                         status = `Running`;
//                     } else if (result.log[index].status == 'COMPLETED') {
//                         status = `Completed`;
//                     } else if (result.log[index].status == 'CANCELED') {
//                         status = `Canceled`;
//                     } else {
//                         status = ``;
//                     }
//                     let statusDropDown = '';
//                     if (meetingStatusChangePermission) {
//                         statusDropDown = `<button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
//                                                     </button>
//                                                     <div class="dropdown-menu" data-id="${result.log[index].id}">
//                                                         ${evenStatusDropDown}
//                                                     </div>`;
//                     }
//                     let start_time = result.log[index].start_time ? moment(result.log[index]
//                         .start_time).format("DD/MM/YYYY HH:mm A") : "";
//                     let end_time = result.log[index].end_time ? moment(result.log[index]
//                         .end_time).format("DD/MM/YYYY HH:mm A") : "";
//
//                     // html += `<div class="text-reset notification-item d-block dropdown-item">
//                     //             <div class="d-flex">
//                     //                 <div class="flex-1 " onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
//                     //                     <h6 class="mt-0 mb-1 fs-13 fw-semibold"> ${result.log[index].subject || ""}</h6>
//                     //                     <div class="fs-13 text-muted">
//                     //                         <p class="mb-1">${result.log[index].description || ""}</p>
//                     //                     </div>
//                     //                     <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
//                     //                         <span><i class="mdi mdi-clock-outline"></i> ${start_time || ""} - ${end_time || ""}<i class="mdi mdi-google-maps"></i> ${result.log[index].location || ""}</span>
//                     //                     </p>
//                     //                 </div>
//                     //                 <div class="px-2 fs-16">
//                     //                     <div class="btn-group">
//                     //                         <button class="btn btn-light btn-sm" type="button">
//                     //                             ${status}
//                     //                         </button>
//                     //                         ${statusDropDown}
//                     //                     </div>
//                     //                 </div>
//                     //             </div>
//                     //         </div>`;
//                     html+=`<div class="card mb-3" onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
//                                                     <div class="card-body">
//                                                         <div class="d-flex mb-3">
//                                                             <div class="flex-grow-1"><i
//                                                                     class="mdi mdi-checkbox-blank-circle me-2 text-info"></i><span
//                                                                     class="fw-medium">${start_time || ""} - ${end_time || ""}</span></div>
//                                                             <div class="flex-shrink-0"><small class="badge badge-soft-primary ms-auto">${start_time || ""} </small></div>
//                                                         </div>
//                                                         <h6 class="card-title fs-16">${result.log[index].subject || ""}</h6>
//                                                         <p class="text-muted text-truncate-two-lines mb-0">${result.log[index].description || ""}</p>
//
//                                                         <p class="text-muted text-truncate-two-lines mb-0"> <div class="px-2 fs-16">
//                                                 <div class="btn-group" onClick="event.stopPropagation();">
//                                                      <button class="btn btn-light btn-sm" type="button">
//                                                          ${status}
//                                                     </button>
//                                                      ${statusDropDown}
//                                                 </div>
//                                             </div> </p>
//                                                     </div>
//                                                 </div>`;
//                 });
//                 $("#meetingLogs"+unique_id).html(html);
//                 let element = document.getElementById("meetingLogs");
//                 element.scrollTop = element.scrollHeight;
//
//             } else {
//                 $("#meetingLogs"+unique_id).html(alertMsg);
//             }
//         },
//         error: function(jqXhr, json, errorThrown) {
//             $("#meetingLogs").html(alertMsg);
//             let data = jqXhr.responseJSON;
//             if (data.errors) {
//                 $.each(data.errors, function(index, item) {
//                     showToastify(item[0], "error")
//                 })
//             }
//             if (jqXhr.status == 500 || jqXhr.status == 400) {
//                 showToastify(data.message, "success")
//             }
//         },
//     });
//
// }
// function getTask(id,model_id,unique_id) {
//
//
//     let alertMsg = `<div class="card-animate">
//                                 <div class="card-body">
//                                     <div class="d-flex mb-3 text-center">
//                                         <div class="flex-grow-1">
//                                             <lord-icon src="`+noTaskIcon+`" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:55px;height:55px"> </lord-icon>
//                                         </div>
//
//
//                                     </div>
//                                     <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No Tasks</span><small class="text-muted fs-13"></small></h3>
//                                     <h6 class="text-muted mb-0"></h6>
//                                 </div>
//                             </div>`;
//     let changeTaskStatusPermission = "{{ auth()->user()->can('Contact Task Status Change') }}";
//     $.ajax({
//         url: route('admin.events.all'),
//         type: 'POST',
//         dataType: 'json',
//         data: {
//             column: 'contact_id',
//             value: id,
//             type: 'Task'
//         },
//         success: function(result) {
//             $("#taskLogs").html('');
//             if (result.status == '1') {
//                 let html = '';
//                 let priority = '';
//                 let status = '';
//                 let due_date = '';
//                 Object.keys(result.log).map((index) => {
//                     if (result.log[index].priority == 'High') {
//                         priority =
//                             `<span class="badge bg-soft-danger text-danger align-middle">High</span>`;
//                     } else if (result.log[index].priority == 'Medium') {
//                         priority =
//                             `<span class="badge bg-soft-warning text-warning align-middle">Medium</span>`;
//                     } else {
//                         priority =
//                             `<span class="badge bg-soft-success text-success align-middle">Low</span>`;
//                     }
//                     if (result.log[index].status == 'PENDING') {
//                         status = `<span class="badge badge-soft-primary">Pending</span>`;
//                     } else if (result.log[index].status == 'RUNNING') {
//                         status = `<span class="badge badge-soft-secondary">Running</span>`;
//                     } else if (result.log[index].status == 'COMPLETED') {
//                         status = `<span class="badge badge-soft-success">Completed</span>`;
//                     } else if (result.log[index].status == 'CANCELED') {
//                         status = `<span class="badge badge-soft-danger">Canceled</span>`;
//                     } else {
//                         status = ``;
//                     }
//                     due_date = moment(result.log[index].start_time).format(
//                         'DD/MM/YYYY hh:mm A');
//                     let statusDropDown = '';
//                     if (changeTaskStatusPermission) {
//                         statusDropDown = `<button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
//                                                     </button>
//                                                     <div class="dropdown-menu" data-id="${result.log[index].id}">
//                                                         ${evenStatusDropDown}
//                                                     </div>`;
//                     }
//                     // html += `<div class="text-reset notification-item d-block dropdown-item">
//                     //                     <div class="d-flex">
//                     //                         <div class="flex-1 task-selects" onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
//                     //                             <h6 class="mt-0 mb-1 fs-13 fw-semibold">${priority} ${result.log[index].title || ""}</h6>
//                     //                             <div class="fs-13 text-muted">
//                     //                                 <p class="mb-1">${result.log[index].description || ""}</p>
//                     //                             </div>
//                     //                             <p class="mb-0 fs-11 fw-medium text-muted">
//                     //                                 <span><i class="mdi mdi-clock-outline"></i> Due by ${due_date || ""}</span>
//                     //                             </p>
//                     //                         </div>
//                     //                         <div class="px-2 fs-16">
//                     //                            <div class="btn-group">
//                     //                                 <button class="btn btn-light btn-sm" type="button">
//                     //                                     ${status}
//                     //                                 </button>
//                     //                             ${statusDropDown}
//                     //                             </div>
//                     //                         </div>
//                     //                     </div>
//                     //                 </div>`;
//                     html+=`<div class="card mb-3" onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
//                                                     <div class="card-body">
//                                                         <div class="d-flex mb-3">
//                                                             <div class="flex-grow-1"><i
//                                                                     class="mdi mdi-checkbox-blank-circle me-2 text-info"></i><span
//                                                                     class="fw-medium">${due_date || ""} - ${due_date || ""}</span></div>
//                                                             <div class="flex-shrink-0"><small class="badge badge-soft-primary ms-auto">${due_date || ""} </small></div>
//                                                         </div>
//                                                         <h6 class="card-title fs-16">${result.log[index].subject || ""}</h6>
//                                                         <p class="text-muted text-truncate-two-lines mb-0">${result.log[index].description || ""}</p>
//
//                                                         <p class="text-muted text-truncate-two-lines mb-0"> <div class="px-2 fs-16">
//                                                 <div class="btn-group" onClick="event.stopPropagation();">
//                                                      <button class="btn btn-light btn-sm" type="button">
//                                                          ${status}
//                                                     </button>
//                                                      ${statusDropDown}
//                                                 </div>
//                                             </div> </p>
//                                                     </div>
//                                                 </div>`;
//                 });
//                 $("#taskLogs"+unique_id).html(html);
//                 let element = document.getElementById("taskLogs");
//                 element.scrollTop = element.scrollHeight;
//             } else {
//                 $("#taskLogs"+unique_id).html(alertMsg);
//
//             }
//         },
//         error: function(jqXhr, json, errorThrown) {
//             $("#taskLogs").html(alertMsg);
//             let data = jqXhr.responseJSON;
//             if (data.errors) {
//                 $.each(data.errors, function(index, item) {
//                     showToastify(item[0], "error")
//                 })
//             }
//             if (jqXhr.status == 500 || jqXhr.status == 400) {
//                 showToastify(data.message, "success")
//             }
//         },
//     });
// }
function getEventLog(id,model_id,unique_id,type,eventNamePrefix) {

    let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="${commonNoDataIcon}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:125px;height:125px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No ${type}s</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;

    $.ajax({
        url: route('admin.events.all'),
        type: 'POST',
        dataType: 'json',
        data: {
            column: model_id,
            value: id,
            type: type
        },
        success: function(result) {
            $("#meetingLogs"+unique_id).html('');


            if (result.status == '1') {
                let html = '<ul class="list-group list-group-flush border-dashed">';
                let status = '';
                let dateColor='';
                Object.keys(result.log).map((index) => {
                    if (result.log[index].status == 'PENDING') {
                        status = `Pending`;
                    } else if (result.log[index].status == 'RUNNING') {
                        status = `Running`;
                    } else if (result.log[index].status == 'COMPLETED') {
                        status = `Completed`;
                    } else if (result.log[index].status == 'CANCELED') {
                        status = `Canceled`;
                    } else {
                        status = ``;
                    }
                    let statusDropDown = '';
                    if (meetingStatusChangePermission) {
                        statusDropDown = `<button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split " data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" data-id="${result.log[index].id}">
                                                        ${evenStatusDropDown}
                                                    </div>`;
                    }
                    let start_date = result.log[index].start_time ? moment(result.log[index].start_time).format("DD MMM YYYY") : "";
                    let start_date_date = result.log[index].start_time ? moment(result.log[index].start_time).format("DD") : "";

                    let start_date_day = result.log[index].start_time ? moment(result.log[index].start_time).format("ddd") : "";
                    let start_time = result.log[index].start_time ? moment(result.log[index].start_time).format("hh:mm A") : "";
                    let end_time = result.log[index].end_time ? moment(result.log[index].end_time).format("hh:mm A") : "";


                    if (moment().isSame(moment(start_date), 'day')) {
                            dateColor='bg-soft-danger';
                    }
                    else if (moment() < moment(start_date)) {
                        dateColor='bg-soft-warning';
                    }
                    else{
                        dateColor='bg-light';

                    }



                    // html += `<div class="text-reset notification-item d-block dropdown-item">
                    //             <div class="d-flex">
                    //                 <div class="flex-1 " onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
                    //                     <h6 class="mt-0 mb-1 fs-13 fw-semibold"> ${result.log[index].subject || ""}</h6>
                    //                     <div class="fs-13 text-muted">
                    //                         <p class="mb-1">${result.log[index].description || ""}</p>
                    //                     </div>
                    //                     <p class="mb-0 fs-11 fw-medium text-uppercase text-muted">
                    //                         <span><i class="mdi mdi-clock-outline"></i> ${start_time || ""} - ${end_time || ""}<i class="mdi mdi-google-maps"></i> ${result.log[index].location || ""}</span>
                    //                     </p>
                    //                 </div>
                    //                 <div class="px-2 fs-16">
                    //                     <div class="btn-group">
                    //                         <button class="btn btn-light btn-sm" type="button">
                    //                             ${status}
                    //                         </button>
                    //                         ${statusDropDown}
                    //                     </div>
                    //                 </div>
                    //             </div>
                    //         </div>`;
                    let attendeesHtml = "";
                    if (result.log[index]?.attendees != null) {
                        //let attendeesHtml = "";
                        result.log[index]?.attendees.forEach(function (attendee) {
                            let attendeesHtml2 = `<a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="${attendee?.full_name}" data-bs-placement="top" aria-label="${attendee.full_name}" data-bs-original-title="${attendee.full_name}">
                                                    <img src="${attendee.image_url}" alt="" class="rounded-circle avatar-xxs">
                                                </a>`;
                            attendeesHtml+=`<div class="avatar-group-item">
                                            <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" title="${attendee.full_name}" data-bs-original-title="Jansh Brown">
                                                <img src="${attendee.image_url}" alt="${attendee.full_name}" class="rounded-circle avatar-xxs">
                                            </a>
                                        </div>`;

                        })
                        $("#attendeesList").html(attendeesHtml);
                    }

                    html+=`
                            <li class="list-group-item ps-0 cursor-pointer event${result.log[index].id}" onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar-sm p-1 py-2 h-auto bg-light ${dateColor} rounded-3" title="${start_date}">
                                            <div class="text-center">
                                                <h5 class="mb-0">${start_date_date || ""}</h5>
                                                <div class="text-muted">${start_date_day || ""}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">${start_time || ""} - ${end_time || ""}</h5>
                                        <a href="javascript:;" class="text-reset fs-14 mb-0">${result.log[index].title || ""}</a>
                                        <p>${result.log[index].description || ""}</p>
                                    </div>
                                    <div class="col-sm-auto">
                                        <div class="avatar-group">
                                             <div class="avatar-group-item">
                                                <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" title="${result.log[index]?.agent?.full_name}" data-bs-original-title="Stine Nielsen">
                                                    <img src="${result.log[index]?.agent?.image_url}" alt="${result.log[index]?.agent?.full_name}" class="rounded-circle avatar-xxs">
                                                </a>
                                            </div>



                                                ${attendeesHtml}

                                        </div>
                                    </div>
                                </div>
                            </li>
                    `;

                });
                $("#"+eventNamePrefix+unique_id).html(html);

                scrollAtBottom(eventNamePrefix + unique_id);

            } else {
                $("#"+eventNamePrefix+unique_id).html(alertMsg);
            }
        },
        error: function(jqXhr, json, errorThrown) {
            $("#"+eventNamePrefix+unique_id).html(alertMsg);
            let data = jqXhr.responseJSON;
            if (data.errors) {
                $.each(data.errors, function(index, item) {
                    showToastify(item[0], "error")
                })
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(data.message, "success")
            }
        },
    });

}
function getEventDataHtmlList(id,model_id,unique_id,type,eventNamePrefix) {


    let alertMsg = `<div class="card-animate">
                                <div class="card-body">
                                    <div class="d-flex mb-3 text-center">
                                        <div class="flex-grow-1">
                                            <lord-icon src="${commonNoDataIcon}" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:125px;height:125px"> </lord-icon>
                                        </div>


                                    </div>
                                    <h3 class="mb-2 text-center"><span class="counter-value text-muted text-center" data-target="97685">No ${type !='' ? type : 'event'}s</span><small class="text-muted fs-13"></small></h3>
                                    <h6 class="text-muted mb-0"></h6>
                                </div>
                            </div>`;

    $.ajax({
        url: route('admin.events.all'),
        type: 'POST',
        dataType: 'json',
        data: {
            column: model_id,
            value: id,
            type: type
        },
        success: function(result) {
            $("#"+eventNamePrefix+unique_id).html('');


            if (result.status == '1') {
                let html = '<ul class="list-group list-group-flush border-dashed">';
                let status = '';
                let dateColor='';
                Object.keys(result.log).map((index) => {
                    if (result.log[index].status == 'PENDING') {
                        status = `Pending`;
                    } else if (result.log[index].status == 'RUNNING') {
                        status = `Running`;
                    } else if (result.log[index].status == 'COMPLETED') {
                        status = `Completed`;
                    } else if (result.log[index].status == 'CANCELED') {
                        status = `Canceled`;
                    } else {
                        status = ``;
                    }
                    let statusDropDown = '';
                    // if (meetingStatusChangePermission) {
                    //     statusDropDown = `<button type="button" class="btn btn-sm btn-light dropdown-toggle dropdown-toggle-split " data-bs-toggle="dropdown" aria-haspopup="false" aria-expanded="false">
                    //                                 </button>
                    //                                 <div class="dropdown-menu" data-id="${result.log[index].id}">
                    //                                     ${evenStatusDropDown}
                    //                                 </div>`;
                    // }
                    let start_date = result.log[index].start_time ? moment(result.log[index].start_time).format("DD MMM YYYY") : "";
                    let start_date_date = result.log[index].start_time ? moment(result.log[index].start_time).format("DD") : "";

                    let start_date_day = result.log[index].start_time ? moment(result.log[index].start_time).format("ddd") : "";
                    let start_time = result.log[index].start_time ? moment(result.log[index].start_time).format("hh:mm A") : "";
                    let end_time = result.log[index].end_time ? moment(result.log[index].end_time).format("hh:mm A") : "";

                    if (moment().isSame(moment(start_date), 'day')) {
                        dateColor='bg-soft-danger';
                    }
                    else if (moment() < moment(start_date)) {
                        dateColor='bg-soft-warning';
                    }
                    else{
                        dateColor='bg-light';

                    }
                    let attendeesHtml = "";
                    if (result.log[index]?.attendees != null) {
                        let attendeesHtml = "";
                        result.log[index]?.attendees.forEach(function (attendee) {
                            attendeesHtml += `<a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="${attendee?.full_name}" data-bs-placement="top" aria-label="${attendee.full_name}" data-bs-original-title="${attendee.full_name}">
                                                    <img src="${attendee.image_url}" alt="" class="rounded-circle avatar-xxs">
                                                </a>`

                        })
                        $("#attendeesList").html(attendeesHtml);
                    }
                    html+=`
                            <li class="list-group-item ps-0 cursor-pointer" onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
                                <div class="row align-items-center g-3">
                                    <div class="col-auto">
                                        <div class="avatar-sm p-1 py-2 h-auto bg-light ${dateColor} rounded-3" title="${start_date}">
                                            <div class="text-center">
                                                <h5 class="mb-0">${start_date_date || ""}</h5>
                                                <div class="text-muted">${start_date_day || ""}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <h5 class="text-muted mt-0 mb-1 fs-13">${start_time || ""} - ${end_time || ""}</h5>
                                        <a href="javascript:;" class="text-reset fs-14 mb-0">${result.log[index].title || ""}</a>
                                        <p>${result.log[index].description || ""}</p>
                                    </div>
                                    <div class="col-sm-auto">
                                        <div class="avatar-group">
                                             <div class="avatar-group-item">
                                                <a href="javascript: void(0);" class="d-inline-block" data-bs-toggle="tooltip" data-bs-placement="top" title="${result.log[index]?.agent?.full_name}" data-bs-original-title="Stine Nielsen">
                                                    <img src="${result.log[index]?.agent?.image_url}" alt="${result.log[index]?.agent?.full_name}" class="rounded-circle avatar-xxs">
                                                </a>
                                            </div>



                                                ${attendeesHtml}

                                        </div>
                                    </div>
                                </div>
                            </li>
                    `;
                    // html+=`<div class="card mb-3 cursor-pointer" onclick="viewEvent('${result.log[index].id}')" data-id="${result.log[index].id}">
                    //                                 <div class="card-body">
                    //                                     <div class="d-flex mb-3">
                    //                                         <div class="flex-grow-1"><i
                    //                                                 class="mdi mdi-checkbox-blank-circle me-2 text-info"></i><span
                    //                                                 class="fw-medium">${start_date || ""}</span></div>
                    //                                         <div class="flex-shrink-0"><small class="badge badge-soft-primary ms-auto">${start_time || ""} -${end_time || ""} </small></div>
                    //                                     </div>
                    //                                     <h6 class="card-title fs-14">${result.log[index].title || ""}</h6>
                    //                                     <p class="text-muted text-truncate-two-lines mb-0">${result.log[index].description || ""}</p>
                    //
                    //                                     <p class="text-muted text-truncate-two-lines mb-0"> <div class="px-2 fs-16 d-flex">
                    //                                     <div class="flex-grow-0">
                    //                                     <a href="javascript: void(0);" class="avatar-group-item" data-bs-toggle="tooltip" data-bs-trigger="hover" title="${result.log[index]?.agent?.full_name}" data-bs-placement="top" aria-label="${result.log[index]?.agent?.first_name}" data-bs-original-title="${result.log[index]?.agent?.first_name}">
                    //                             <img src="${result.log[index]?.agent?.image_url}" alt="" class="rounded-circle avatar-xxs">
                    //                         </a>
                    //                         </div>
                    //                         <div class="flex-grow-1">
                    //             ${attendeesHtml}
                    //         </div>
                    //                             <div class="flex-shrink-0"><div class="btn-group d-none" onClick="event.stopPropagation();">
                    //                                  <button class="btn btn-light btn-sm" type="button">
                    //                                      ${status}
                    //                                 </button>
                    //                                  ${statusDropDown}
                    //                             </div></div>
                    //
                    //                         </div>
                    //                         </p>
                    //                                 </div>
                    //                             </div>`;
                });
                $("#"+eventNamePrefix+unique_id).html(html);



            } else {
                $("#"+eventNamePrefix+unique_id).html(alertMsg);
            }
        },
        error: function(jqXhr, json, errorThrown) {

            let data = jqXhr.responseJSON;
            if (data.errors) {
                $.each(data.errors, function(index, item) {
                    showToastify(item[0], "error")
                })
            }
            if (jqXhr.status == 500 || jqXhr.status == 400) {
                showToastify(data.message, "success")
            }
        },
    });

}

//check if scrollAtBottom function is not deaclared already
if (typeof scrollAtBottom !== 'function') {
    function scrollAtBottom($el) {
        let element = document.getElementById($el);
        if(element) {
            element.scrollTop = element.scrollHeight;
        }

    }
}

