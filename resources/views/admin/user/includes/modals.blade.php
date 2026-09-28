<div id="exportModal" class="modal fade" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-lg">
        <form id="exportForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-start align-items-center gap-2">
                            <h5><strong>Select at least columns</strong></h5>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all-export">
                                <label class="form-check-label" for="select-all-export">
                                    Select All
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="row">
                            @foreach (getUserExportColumns() as $index => $column)
                                <div class="col-4">
                                    <div class="form-check form-switch form-switch-success">
                                        <input class="form-check-input export-columns" type="checkbox" name="columns[]"
                                               value="{{ $index }}" role="switch"
                                               id="export-column-{{ $index }}">
                                        <label class="form-check-label" for="export-column-{{ $index }}">
                                            {{ $column }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-export">Export</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade bs-example-modal-lg" id="userDeletionModal" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <form id="dataAssignForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Assign Current User data before deletion</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p class="fs-13 text-muted">Are you sure you want to delete user <span id="deleteUserName"></span> ?
                        This user has been associated
                        with these data please assign these data to any other user.</p>


                    <input type="hidden" class="assignInput" name="id" id="assignSourceAgentId">
                    <input type="hidden" value="2" name="assign_only" id="assignOnly">
                    <div class="row mb-2">
                        <div class="col-12">
                            <ul class="list-group">
                                <li class="list-group-item" style="    background: #fff8f8;">
                                    <div class="" id="agentCardAssignModal"></div>
                                </li>
                                <br>
                                <li class="list-group-item">
                                    <div class="row">

                                        <div class="col-5">
                                            <i class="ri-building-line align-middle me-1 "></i>
                                            <span id="agentPropertiesText"></span>
                                        </div>
                                        <div class="col-7">
                                            <select class="userSelect2 assignInput " style="width:100% !important;"
                                                    id="assignPropertyAgent" name="property_user" required>

                                                <option selected disabled>Select an agent</option>


                                            </select>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item ">
                                    <div class="row">

                                        <div class="col-5">
                                            <i class="las la-user-clock align-middle me-1"></i>
                                            <span id="agentLeadsText">
                                            </span>
                                        </div>
                                        <div class="col-7">
                                            <select class=" userSelect2 assignInput" id="assignLeadAgent"
                                                    style="width:100% !important;" name="lead_user" required>
                                                <option selected disabled>Select an agent</option>

                                            </select>
                                        </div>

                                </li>
                                <li class="list-group-item ">
                                    <div class="row">

                                        <div class="col-5">
                                            <i class="las la-address-book align-middle me-2"></i>
                                            <span id="agentContactsText">
                                            </span>
                                        </div>
                                        <div class="col-7">
                                            <select class="userSelect2 assignInput" id="assignContactAgent"
                                                    style="width:100% !important;" name="contact_user" required>
                                                <option selected disabled>Select an agent</option>

                                            </select>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>


                    </div>


                </div>
                <div class="modal-footer align-items-center d-flex">
                    <div class="flex-grow-1">


                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit" class="d-none" id="hSubmitBtn"></button>
                        <a href="javascript:void(0);" class="btn btn-light fw-medium shadow-none action-close"
                           data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                        <button type="button" id="assignDataOnly" class="btn btn-success btn-load action-assign">Assign
                            Only
                        </button>
                        <button type="submit" id="assignDataToUser" class="btn btn-danger btn-load action-delete">Assign
                            &
                            Delete User
                        </button>
                    </div>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>


<div class="modal fade bs-example-modal-lg" id="userDeactivationModal" tabindex="-1" role="dialog"
     aria-labelledby="myLargeModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
        <div class="modal-content">
            <form id="dataAssignFormDeactivation">
                <div class="modal-header">
                    <h5 class="modal-title" id="myLargeModalLabel">Assign Current User data before Deactivation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>
                <div class="modal-body">
                    <p class="fs-13 text-muted">Are you sure you want to Deactivate user <span id="deactivateUserName"></span> ?
                        This user has been associated with these data please assign these data to any other user.</p>
                    <input type="hidden" class="assignInput" name="id" id="assignSourceAgentIdDeactivation">
                    <input type="hidden" value="2" name="assign_only" id="assignOnly">
                    <div class="row mb-2">
                        <div class="col-12">
                            <ul class="list-group">
                                <li class="list-group-item" style="    background: #fff8f8;">
                                    <div class="" id="agentCardAssignModalDeactivate"></div>
                                </li>
                                <br>
                                <li class="list-group-item">
                                    <div class="row">

                                        <div class="col-5">
                                            <i class="ri-building-line align-middle me-1 "></i>
                                            <span id="agentPropertiesTextDeactivate"></span>
                                        </div>
                                        <div class="col-7">
                                            <select class="userSelect3 assignInput " style="width:100% !important;"
                                                    id="assignPropertyAgentDeactivate" name="property_user" required>

                                                <option selected disabled>Select an agent</option>


                                            </select>
                                        </div>
                                    </div>
                                </li>
                                <li class="list-group-item ">
                                    <div class="row">

                                        <div class="col-5">
                                            <i class="las la-user-clock align-middle me-1"></i>
                                            <span id="agentLeadsTextDeactivate">
                                            </span>
                                        </div>
                                        <div class="col-7">
                                            <select class=" userSelect3 assignInput" id="assignLeadAgentDeactivate"
                                                    style="width:100% !important;" name="lead_user" required>
                                                <option selected disabled>Select an agent</option>

                                            </select>
                                            <small><small>Except Unsuccessful and Archived Leads. </small></small>
                                            <label for="assignLeadAgentDeactivate" class="mt-1"> Send to Leadpool
                                                <input type="checkbox" class="" value="1" name="send_to_leadpool" id="send_to_leadpool">
                                            </label>

                                        </div>

                                </li>
                                <li class="list-group-item ">
                                    <div class="row">

                                        <div class="col-5">
                                            <i class="las la-address-book align-middle me-2"></i>
                                            <span id="agentContactsTextDeactivate">
                                            </span>
                                        </div>
                                        <div class="col-7">
                                            <select class="userSelect3 assignInput" id="assignContactAgentDeactivate"
                                                    style="width:100% !important;" name="contact_user" required>
                                                <option selected disabled>Select an agent</option>

                                            </select>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>


                    </div>


                </div>
                <div class="modal-footer align-items-center d-flex">
                    <div class="flex-grow-1">


                    </div>
                    <div class="flex-shrink-0">
                        <button type="submit" class="d-none" id="hSubmitBtn"></button>
                        <a href="javascript:void(0);" class="btn btn-light fw-medium shadow-none action-close"
                           data-bs-dismiss="modal"><i class="ri-close-line me-1 align-middle"></i> Close</a>
                        {{-- <button type="button" id="assignDataOnly" class="btn btn-success btn-load action-assign">Assign Only
                        </button> --}}
                        <button class="btn btn-danger btn-load action-delete d-none" disabled id="assignDataToUserDeactivate-loader">
                            <span class="d-flex align-items-center">
                                <span class="flex-grow-1 me-2">
                                    Loading...
                                </span>
                                <span class="spinner-border flex-shrink-0" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </span>
                            </span>
                        </button>

                        <button type="submit" id="assignDataToUserDeactivate" class="btn btn-danger btn-load action-delete">
                            Assign & Deactivate User
                        </button>
                    </div>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<div id="locationModal" class="modal fade" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true"
     style="display: none;">
    <div class="modal-dialog modal-xl">
        <form id="traccarMapForm" class="modal-content needs-validation" novalidate>
            <div class="modal-header">
                <h5 class="modal-title" id="locationModalLabel">Set Location</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
        <div class="col-6">
            <div class="row">
            <div class="col-4">Title</div>
            <div class="col-8">
                <input type="hidden" id="traccar_id" name="id">
                <input type="text" class="form-control clrInput" id="traccar_title" name="title">
            </div>
            <div class="col-4 mt-2">Radius <small>(In meter)</small></div>
            <div class="col-8 mt-2">
                <input type="range" class="form-range" min="100" max="5000" name="radius" id="traccar_radius" value="1000">
            </div>
            <div class="col-4 mt-2">Zoom</div>
            <div class="col-5 mt-2">

                <div class="input-step mapZoom">
                    <button type="button" class="minus">–</button>
                    <input type="number" class="product-quantity" id="traccar_zoom" name="zoom" value="12" min="0" max="100" readonly="" data-ddg-inputtype="unknown">
                    <button type="button" class="plus">+</button>
                </div>
            </div>
                <div class="col-3 mt-2" style="margin-top: -5px !important;">
                    <small>Radius (<span id="selectedRadius">1000m</span>)</small>
                </div>
            <div class="col-4 mt-2">Stay Time <small>(In minute)</small></div>
            <div class="col-8 mt-2">
                <input type="text" class="form-control clrInput" id="traccar_stay_time" name="stay_time">
            </div>
                <div class="col-4 mt-2">Latitude</div>
                <div class="col-8 mt-2">
                    <input type="text" class="form-control clrInput" id="location_latitude" name="latitude">
                </div>

                <div class="col-4 mt-2">Longitude</div>
                <div class="col-8 mt-2">
                    <input type="text" class="form-control clrInput" id="location_longitude" name="longitude">
                </div>
        </div>
        </div>
        <div class="col-6">
            <div class="row">

                <div class="col-2">Place</div>
                <div class="col-10">
                    <input type="text" class="form-control clrInput" id="traccar_place" name="traccar_place">
                </div>
                    <div class="col-12 mt-2">
                        <div id="map"></div>
                    </div>
            </div>

                </div></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light action-close" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary action-save" id="traccarFormBtn">Save</button>
            </div>
        </form>
    </div>
</div>
