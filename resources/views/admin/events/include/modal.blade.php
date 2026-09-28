

<div class="modal zoomIn" id="viewEventModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-header bg-soft-danger p-3">
                <h5 class="modal-title" id="modal-title">View Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-hidden="true"></button>
            </div>
            <div class="modal-body p-4">
                <div class="">

                </div>
                <div class="text-end">
                    <a href="javascript:void(0)" class="btn btn-sm btn-soft-primary" id="edit-event-btn" data-id="edit-event"
                       onclick="editEvent()"
                       id="btn-edit-event" role="button">Edit</a>
                </div>
                <div class="event-details">
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1 d-flex align-items-center">
                            <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                                <i class="bx bx-pin text-muted fs-16"></i>
                                <span style="vertical-align: text-bottom;" class="text-muted">   Priority:</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="d-block fw-semibold mb-0 calendarViewPriority" ></h6>
                            </div>
                        </div>
                    </div><div class="d-flex mb-2">
                        <div class="flex-grow-1 d-flex align-items-center">
                            <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                                <i class=" bx bx-abacus text-muted fs-16"></i>
                                <span style="vertical-align: text-bottom;" class="text-muted">   Event type:</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="d-block fw-semibold mb-0" id="event-type-tag"></h6>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1 d-flex align-items-center">
                            <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                                <i class="ri-calendar-event-line text-muted fs-16"></i>
                                <span style="vertical-align: text-bottom;" class="text-muted">Dates:</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="d-block fw-semibold mb-0" id="event-start-date-tag"></h6>
                            </div>
                        </div>
                    </div>


                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                            <i class="ri-map-pin-line text-muted fs-16"></i>
                            <span style="vertical-align: text-bottom;" class="text-muted">Location:</span>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="d-block fw-semibold mb-0"><span id="event-location-tag"></span></h6>
                        </div>
                    </div>
                    <div class="d-flex mb-3 d-none" id="event-property-block">
                        <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                            <i class="bx bx-building-house text-muted fs-16" title="Property/Listing"></i>
                            <span style="vertical-align: text-bottom;" class="text-muted">Property:</span>
                        </div>

                        <div class="flex-grow-1">
                            <b>
                                <p class="d-block text-muted mb-0" id="event-property-tag"></p>
                            </b>
                        </div>
                    </div>
                    <div class="d-flex mb-3 d-none" id="event-contact-block">
                        <div class="flex-shrink-0 me-3" style="min-width: 90px;" >
                            <i class="ri-contacts-book-2-line text-muted fs-16" title="Contact"></i>
                            <span style="vertical-align: text-bottom;" class="text-muted">Contact:</span>
                        </div>

                        <div class="flex-grow-1">
                            <b>
                                <p class="d-block text-muted mb-0" id="event-contact-tag"></p>
                            </b>
                        </div>
                    </div>
                    <div class="d-flex mb-2 d-none" id="event-lead-block">
                        <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                            <i class="bx bx-git-branch text-muted fs-16" title="Lead"></i>  <span style="vertical-align: text-bottom;" class="text-muted" >Lead:</span>
                        </div>

                        <div class="flex-grow-1">
                            <b>
                                <p class="d-block text-muted mb-0" id="event-lead-tag"></p>
                            </b>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                            <i class="bx bx-crosshair text-muted fs-16" title="Event title"></i>  <span style="vertical-align: text-bottom;" class="text-muted">Title:</span>
                        </div>

                        <div class="flex-grow-1">
                            <b>
                                <p class="d-block text-muted mb-0" id="event-title-tag"></p>
                            </b>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                            <i class="ri-discuss-line text-muted fs-16" title="Event description"></i>  <span style="vertical-align: text-bottom;" class="text-muted">Description:</span>
                        </div>

                        <div class="flex-grow-1">
                            <p class="d-block text-muted mb-0" id="event-description-tag"></p>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1 d-flex align-items-center">
                            <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                                <i class="ri-calendar-event-line text-muted fs-16"></i>
                                <span style="vertical-align: text-bottom;" class="text-muted">Created At:</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="d-block fw-semibold mb-0" id="event-created-date-tag"></h6>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex mb-2">
                        <div class="flex-grow-1 d-flex align-items-center">
                            <div class="flex-shrink-0 me-3" style="min-width: 90px;">
                                <i class="ri-calendar-event-line text-muted fs-16"></i>
                                <span style="vertical-align: text-bottom;" class="text-muted">Created By:</span>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="d-block fw-semibold mb-0" id="event-created-by-tag"></h6>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-0" id="viewAgentHtml">

                                <a href="javascript: void(0);" class="avatar-group-item d-none"
                                   data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top"
                                   aria-label="Tonya" data-bs-original-title="Tonya">
                                    <img src="" alt=""
                                         class="rounded-circle avatar-xxs">
                                </a>
                            </div>
                            <div class="flex-grow-1">
                                <div class="avatar-group" id="attendeesList">
                                    <a href="javascript: void(0);" class="avatar-group-item"
                                       data-bs-toggle="tooltip" data-bs-trigger="hover"
                                       data-bs-placement="top" aria-label="Tonya"
                                       data-bs-original-title="Tonya">
                                        <img src="" alt=""
                                             class="rounded-circle avatar-xxs">
                                    </a>
                                    <a href="javascript: void(0);" class="avatar-group-item"
                                       data-bs-toggle="tooltip" data-bs-trigger="hover"
                                       data-bs-placement="top" aria-label="Frank"
                                       data-bs-original-title="Frank">
                                        <img src="" alt=""
                                             class="rounded-circle avatar-xxs">
                                    </a>
                                    <a href="javascript: void(0);" class="avatar-group-item"
                                       data-bs-toggle="tooltip" data-bs-trigger="hover"
                                       data-bs-placement="top" aria-label="Herbert"
                                       data-bs-original-title="Herbert">
                                        <img src="" alt=""
                                             class="rounded-circle avatar-xxs">
                                    </a>
                                </div>
                            </div>
                            <div class="flex-shrink-0">

                            </div>
                        </div>
                    </div>
                </div>

                <!--end row-->
                <div class="hstack gap-2 justify-content-end">

                    @can("Events Delete")  <button type="button" class="btn btn-soft-danger" onclick="deleteEvent()"> Delete</button> @endcan
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="fullscreeexampleModal" tabindex="-1" aria-labelledby="report-title" style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="report-title">Full screen modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 animate-to-right" style="font-size: 16px">
                        <i class=" bx bx-chevrons-right"></i><i class=" bx bx-chevrons-right"></i><i class=" bx bx-chevrons-right"></i><i class=" bx bx-chevrons-right"></i><i class=" bx bx-chevrons-right"></i><i class=" bx bx-chevrons-right"></i>
                    </div>
                </div>
                <div class="" id="put-data-here">

                </div>
            </div>

        </div>
    </div>
</div>
