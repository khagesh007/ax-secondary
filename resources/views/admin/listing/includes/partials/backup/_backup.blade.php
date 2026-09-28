    function getActivityLog(id) {
            $("#activity-log-div").removeClass("d-none")
            $("#activity-log-div").find(".user-activity-timeline").html("");
            fullyLoaded = true;
            $.ajax({
                url: "{{ route('admin.listing.properties.activity.log') }}",
                type: 'GET',
                dataType: 'json',
                data: {
                    subject_model: "Property",
                    subject_id: id
                },
                success: function(result) {
                    let logs = "";
                    let data = result?.data.reverse();
                    data.map(item => {
                        let date = moment(item.created_at).fromNow()
                        let imageUrl =
                            `${item.causer ? item.causer.thumb_url : "{{ URL::asset('assets/images/users/multi-user.jpg') }}"}`;

                        logs += `<div class="accordion-item border-0">
                                  <div class="accordion-header" id="heading6">
                                  <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapse${item.id}" aria-expanded="false">

                                      <div class="d-flex">
                                        <div class="flex-shrink-0">
                                          <img src="${imageUrl}" alt="" class="avatar-xs rounded-circle">
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                          <h6 class="fs-14 mb-1"> ${item.causer ? item.causer.name :"System"}  </h6>
                                          <small class="text-muted">${item.description} - <small class="mb-0 text-muted">${date}</small> </small>
                                        </div>
                                      </div>
                                    </a>
                                  </div>
                                  <div id="collapse${item.id}" class="accordion-collapse collapse hide" aria-labelledby="heading6" data-bs-parent="#accordion${item.id}">
                                    <div class="accordion-body ms-2 ps-5 pt-2">
                                    <div class="code-view" id="hiddenLog${item.id}">
                                 <span class="language-markup">
                                    <small class="text-muted"><strong>New data:</strong> <br>${activityChangeDataFormat(item.properties.attributes)}<br><strong>Old Data:</strong><br>${activityChangeDataFormat(item.properties.old)} </small></span></div> </div>
                                          </div>
                                        </div>`;
                    })
                    $("#activity-log-div").find(".user-activity-timeline").html(logs);

                    let element = document.getElementById("activities");
                    element.scrollTop = element.scrollHeight

                    fullyLoaded = true;

                },
                error: function(result) {

                    console.log(result);
                }
            });
        }
