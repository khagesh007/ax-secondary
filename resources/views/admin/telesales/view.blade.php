@extends('admin.layouts.master')
@section('title')
    Leads
@endsection
@section('style')
    <link href="{{ URL::asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css"/>
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css"/>
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card mt-n4 mx-n4">
                <div class="bg-soft-warning">
                    <div class="card-body pb-0 px-4">
                        <div class="row mb-3">
                            <div class="col-md">
                                <div class="row align-items-center g-3">
                                    <div class="col-md-auto">
                                        <div class="avatar-md">
                                            <div class="avatar-title bg-white rounded-circle">
                                                <img src="{{ $lead->agent->thumb_url ?? URL::asset('assets/images/brands/slack.png') }}" alt="" class="avatar-xs">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div>
                                            <div class="hstack gap-3 flex-wrap">
                                                <div>Agent : <span class="fw-medium">{{ $lead->agent->name ?? "" }}</span></div>
                                                <div class="vr"></div>
                                                <div>Create Date : <span class="fw-medium">{{ $lead->created_at->format("d M, Y") }}</span></div>
                                                <div class="vr"></div>
                                                <div>Follow-up Date : <span class="fw-medium">{{ formatDate($lead->follow_up_date, "d M, Y") }}</span></div>
                                                <div class="vr"></div>
                                                <div class="badge rounded-pill bg-dark fs-12">{{ $lead->priority }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-auto">
                                <div class="hstack gap-1 flex-wrap">
                                    <button type="button" class="btn py-0 fs-16 favourite-btn active shadow-none">
                                        <i class="ri-star-fill"></i>
                                    </button>
                                    <button type="button" class="btn py-0 fs-16 text-body shadow-none">
                                        <i class="ri-share-line"></i>
                                    </button>
                                    <button type="button" class="btn py-0 fs-16 text-body shadow-none">
                                        <i class="ri-flag-line"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <ul class="nav nav-tabs-custom border-bottom-0" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active fw-semibold" data-bs-toggle="tab" href="#project-overview"
                                   role="tab">
                                    Overview
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link fw-semibold" data-bs-toggle="tab" href="#project-activities" role="tab">
                                    Call log
                                </a>
                            </li>

                        </ul>
                    </div>
                    <!-- end card body -->
                </div>
            </div>
            <!-- end card -->
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="tab-content text-muted">
                <div class="tab-pane fade show active" id="project-overview" role="tabpanel">
                    <div class="row">
                        <div class="col-xl-9 col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-muted">
                                        <div class="pt-3">
                                            <div class="row">
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">First Name :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->first_name }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Last Name :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->last_name }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Email :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->email }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Mobile :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->mobile }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Whatsapp :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->whatsapp }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Phone :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->phone }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Nationality :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->nationality->name ?? "" }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Gender :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->gender }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">DOB :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->dob ?? "" }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Company :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->company ?? "" }}</h5>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-sm-6">
                                                    <div>
                                                        <p class="mb-2 text-uppercase fw-medium">Property :</p>
                                                        <h5 class="fs-15 mb-0">{{ $lead->property->ref_no ?? "" }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Criteria</h4>
                                    <div class="flex-shrink-0">
                                        <div class="dropdown card-header-dropdown" style="    width: 225px;">.</div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <div class="row row-cols-lg-6 g-3 align-items-center">
                                        <div class="col-2 mt-2">Beds</div>
                                        <div class="col-2 mt-2">
                                            <select class="form-control select2 inputs" name="min_bed_id"
                                                    id="min_bed_id">
                                                <option value="" selected disabled>Select</option>
                                                @foreach ($beds as $bed)
                                                    <option value="{{$bed->id}}" {{ $bed->id == ($lead->criteria->min_bed_id ?? "") ? "selected" : "" }}>{{$bed->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose an option.
                                            </div>
                                        </div>
                                        <div class="col-2 mt-2">
                                            <select class="form-control select2 inputs" name="max_bed_id"
                                                    id="max_bed_id">
                                                <option value="" selected disabled>Select</option>
                                                @foreach ($beds as $bed)
                                                    <option value="{{$bed->id}}" {{ $bed->id == ($lead->criteria->max_bed_id ?? "") ? "selected" : "" }}>{{$bed->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose an option.
                                            </div>
                                        </div>
                                        <div class="col-2 mt-2">Bath</div>
                                        <div class="col-auto mt-2">
                                            <select class="form-control select2 inputs" name="min_bath_id"
                                                    id="min_bath_id">
                                                <option value="" selected disabled>Select</option>
                                                @foreach ($baths as $bath)
                                                    <option value="{{$bath->id}}" {{ $bath->id == ($lead->criteria->min_bath_id ?? "") ? "selected" : "" }}>{{$bath->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose an option.
                                            </div>
                                        </div>
                                        <div class="col-2 mt-2">
                                            <select class="form-control select2 inputs" name="max_bath_id"
                                                    id="max_bath_id">
                                                <option value="" selected disabled>Select</option>
                                                @foreach ($baths as $bath)
                                                    <option value="{{$bath->id}}" {{ $bath->id == ($lead->criteria->max_bath_id ?? "") ? "selected" : "" }}>{{$bath->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose an option.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row  mt-2 align-items-center">
                                        <div class="col-2 mt-2">Sub Community</div>
                                        <div class="col-6 mt-2">
                                            <select class="select2 inputs" multiple id="sub_community_id"
                                                    name="sub_community_id[]">
                                                @foreach ($sub_communities as $subCommunity)
                                                    <option value="{{$subCommunity->id}}" {{ in_array($subCommunity->id, $lead->criteria->communities ?? []) ? "selected" : "" }}>{{$subCommunity->name}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-2 mt-2">Furnished</div>
                                        <div class="col-2 mt-2">
                                            <select class="form-control select2 inputs" id="furnished" name="furnished"
                                                    required>
                                                <option value="FURNISHED" {{ ($lead->criteria->furnished ?? "") == "FURNISHED" ? "selected" : "" }}>Furnished</option>
                                                <option value="SEMI_FURNISHED" {{ ($lead->criteria->furnished ?? "") == "SEMI_FURNISHED" ? "selected" : "" }}>Semi Furnished</option>
                                                <option value="UNFURNISHED" {{ ($lead->criteria->furnished ?? "") == "UNFURNISHED" ? "selected" : "" }}>Unfurnished</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row  mt-2 align-items-center">
                                        <div class="col-2 mt-2">Min price</div>
                                        <div class="col-4 mt-2">
                                            <input type="number" class="form-control inputs" name="min_price"
                                                   id="min_price"
                                                   value="{{ $lead->criteria->min_price ?? "" }}"
                                                   placeholder="min price">
                                        </div>
                                        <div class="col-2 mt-2">Max Price</div>
                                        <div class="col-4 mt-2">
                                            <input type="number" class="form-control inputs" name="max_price"
                                                   id="max_price"
                                                   value="{{ $lead->criteria->max_price ?? "" }}"
                                                   placeholder="min price">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Notes</h4>
                                    <div class="flex-shrink-0"></div>
                                </div>

                                <div class="card-body">
                                    <div data-simplebar style="height: 300px;" class="px-3 mx-n3 mb-2  scrollable-notes" id="notes"></div>
                                    <div class="mt-4">
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label for="exampleFormControlTextarea1" class="form-label text-body">Leave
                                                    a Note</label>
                                                <textarea class="form-control bg-light border-light" id="note" rows="3"
                                                          placeholder="Enter your note..."></textarea>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-success" id="addNoteButton">Save</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- ene col -->
                        <div class="col-xl-3 col-lg-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-4 mt-2">Lead Status</div>
                                        <div class="col-8 mt-2">
                                            <select class="form-control select2 inputs" name="lead_status_id"
                                                    id="lead_status_id">
                                                <option value="" selected disabled>Select lead status</option>
                                                @foreach($statuses ?? [] as $status)
                                                    <option value="{{$status->id}}" {{ $lead->lead_status_id == $status->id ? "selected" : "" }}>{{$status->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose lead status.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2">Lead Sub Status</div>
                                        <div class="col-8 mt-2">
                                            <select class="form-control select2 inputs" name="lead_sub_status_id"
                                                    id="lead_sub_status_id">
                                                <option value="" selected disabled>Select lead sub status</option>
                                                @foreach($sub_statuses ?? [] as $subStatus)
                                                    <option value="{{$status->id}}" {{ $lead->lead_sub_status_id == $subStatus->id ? "selected" : "" }}>{{$subStatus->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose lead sub status.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2">Priority</div>
                                        <div class="col-8 mt-2">
                                            <select class="form-control select2 inputs" name="priority" id="priority">
                                                @foreach(\App\Models\Lead::PRIORITY_SELECT ?? [] as $priority)
                                                    <option
                                                        value="{{ $priority }}" {{ $priority == $lead->priority ? "selected" : "" }}>{{ $priority }}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose priority.
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-body form-steps">
                                    <div class="row">
                                        <div class="col-4 mt-2">Lead Type</div>
                                        <div class="col-8 mt-2">
                                            <select class="form-control select2 inputs" name="lead_type_id"
                                                    id="lead_type_id">
                                                <option value="" selected disabled>Select Type</option>
                                                @foreach($types ?? [] as $type)
                                                    <option value="{{$type->id}}" {{ $type->id == $lead->lead_type_id ? "selected" : "" }}>{{$type->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose lead type.
                                            </div>
                                        </div>

                                        <div class="col-4 mt-2">Lead Source</div>
                                        <div class="col-8 mt-2">
                                            <select class="form-control select2 inputs" name="lead_source_id"
                                                    id="lead_source_id">
                                                <option value="" selected disabled>Select Source</option>
                                                @foreach($sources ?? [] as $src)
                                                    <option value="{{$src->id}}" {{ $src->id == $lead->lead_source_id ? "selected" : "" }}>{{$src->name}}</option>
                                                @endforeach
                                            </select>
                                            <div class="invalid-feedback">
                                                Please choose lead source.
                                            </div>
                                        </div>
                                        <div class="col-4 mt-2">Follow up date</div>
                                        <div class="col-8 mt-2">
                                            <input type="date" class="form-control inputs" data-provider="flatpickr"
                                                   data-date-format="Y-m-d" value="{{ $lead->follow_up_date }}" name="follow_up_date" id="follow_up_date">
                                            <div class="invalid-feedback">
                                                Please enter follow-up date.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header align-items-center d-flex border-bottom-dashed">
                                    <h4 class="card-title mb-0 flex-grow-1">Additional Agents</h4>

                                </div>

                                <div class="card-body">
                                    <div data-simplebar style="height: 235px;" class="mx-n3 px-3">
                                        <div class="vstack gap-3">
                                            @foreach($agents ?? [] as $agent)
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-xs flex-shrink-0 me-3">
                                                    <img src="{{ $agent->agent->thumb_url }}" alt=""
                                                         class="img-fluid rounded-circle">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="fs-13 mb-0"><a href="#" class="text-body d-block">{{ $agent->agent->name }}</a></h5>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="d-flex align-items-center gap-1">
                                                        <div class="dropdown">
                                                            <button
                                                                class="btn btn-icon btn-sm fs-16 text-muted dropdown shadow-none"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                                <i class="ri-more-fill"></i>
                                                            </button>
                                                            <ul class="dropdown-menu">
                                                                <li><a class="dropdown-item" href="javascript:void(0);"><i
                                                                            class="ri-delete-bin-5-fill text-muted me-2 align-bottom"></i>Remove</a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        <!-- end list -->
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->
                        </div>
                        <!-- end col -->
                    </div>
                    <!-- end row -->
                </div>
                <!-- end tab pane -->

                <div class="tab-pane fade" id="project-activities" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Activities</h5>
                            <div class="acitivity-timeline py-3">
                                <div class="acitivity-item d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('assets/images/users/avatar-1.jpg') }}" alt=""
                                             class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Oliver Phillips <span
                                                class="badge bg-soft-primary text-primary align-middle">New</span></h6>
                                        <p class="text-muted mb-2">We talked about a project on linkedin.</p>
                                        <small class="mb-0 text-muted">Today</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                        <div class="avatar-title bg-soft-success text-success rounded-circle shadow">
                                            N
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Nancy Martino <span
                                                class="badge bg-soft-secondary text-secondary align-middle">In
                                                Progress</span></h6>
                                        <p class="text-muted mb-2"><i class="ri-file-text-line align-middle ms-2"></i>
                                            Create new project Buildng product</p>
                                        <div class="avatar-group mb-2">
                                            <a href="javascript: void(0);" class="avatar-group-item shadow"
                                               data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                               data-bs-original-title="Christi">
                                                <img src="{{ URL::asset('assets/images/users/avatar-4.jpg') }}" alt=""
                                                     class="rounded-circle avatar-xs" />
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item shadow"
                                               data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                               data-bs-original-title="Frank Hook">
                                                <img src="{{ URL::asset('assets/images/users/avatar-3.jpg') }}" alt=""
                                                     class="rounded-circle avatar-xs" />
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item shadow"
                                               data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                               data-bs-original-title=" Ruby">
                                                <div class="avatar-xs">
                                                    <div class="avatar-title rounded-circle bg-light text-primary">
                                                        R
                                                    </div>
                                                </div>
                                            </a>
                                            <a href="javascript: void(0);" class="avatar-group-item shadow"
                                               data-bs-toggle="tooltip" data-bs-placement="top" title=""
                                               data-bs-original-title="more">
                                                <div class="avatar-xs">
                                                    <div class="avatar-title rounded-circle">
                                                        2+
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <small class="mb-0 text-muted">Yesterday</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('assets/images/users/avatar-2.jpg') }}" alt=""
                                             class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Natasha Carey <span
                                                class="badge bg-soft-success text-success align-middle">Completed</span>
                                        </h6>
                                        <p class="text-muted mb-2">Adding a new event with attachments</p>
                                        <div class="row">
                                            <div class="col-xxl-4">
                                                <div class="row border border-dashed gx-2 p-2 mb-2">
                                                    <div class="col-4">
                                                        <img src="{{ URL::asset('assets/images/small/img-2.jpg') }}" alt=""
                                                             class="img-fluid rounded shadow" />
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-4">
                                                        <img src="{{ URL::asset('assets/images/small/img-3.jpg') }}" alt=""
                                                             class="img-fluid rounded shadow" />
                                                    </div>
                                                    <!--end col-->
                                                    <div class="col-4">
                                                        <img src="{{ URL::asset('assets/images/small/img-4.jpg') }}" alt=""
                                                             class="img-fluid rounded shadow" />
                                                    </div>
                                                    <!--end col-->
                                                </div>
                                                <!--end row-->
                                            </div>
                                        </div>
                                        <small class="mb-0 text-muted">25 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('assets/images/users/avatar-6.jpg') }}" alt=""
                                             class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Bethany Johnson</h6>
                                        <p class="text-muted mb-2">added a new member to velzon dashboard</p>
                                        <small class="mb-0 text-muted">19 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-soft-danger text-danger">
                                                <i class="ri-shopping-bag-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Your order is placed <span
                                                class="badge bg-soft-danger text-danger align-middle ms-1">Out of
                                                Delivery</span></h6>
                                        <p class="text-muted mb-2">These customers can rest assured their order has been
                                            placed.</p>
                                        <small class="mb-0 text-muted">16 Nov</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('assets/images/users/avatar-7.jpg') }}" alt=""
                                             class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Lewis Pratt</h6>
                                        <p class="text-muted mb-2">They all have something to say beyond the words on the
                                            page. They can come across as casual or neutral, exotic or graphic. </p>
                                        <small class="mb-0 text-muted">22 Oct</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-soft-info text-info shadow">
                                                <i class="ri-line-chart-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">Monthly sales report</h6>
                                        <p class="text-muted mb-2"><span class="text-danger">2 days left</span>
                                            notification to submit the monthly sales report. <a href="javascript:void(0);"
                                                                                                class="link-warning text-decoration-underline">Reports Builder</a></p>
                                        <small class="mb-0 text-muted">15 Oct</small>
                                    </div>
                                </div>
                                <div class="acitivity-item d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ URL::asset('assets/images/users/avatar-8.jpg') }}" alt=""
                                             class="avatar-xs rounded-circle acitivity-avatar shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">New ticket received <span
                                                class="badge bg-soft-success text-success align-middle">Completed</span>
                                        </h6>
                                        <p class="text-muted mb-2">User <span class="text-secondary">Erica245</span>
                                            submitted a ticket.</p>
                                        <small class="mb-0 text-muted">26 Aug</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end card-body-->
                    </div>
                    <!--end card-->
                </div>
                <!-- end tab pane -->
            </div>
        </div>
        <!-- end col -->
    </div>
    <!-- end row -->
@endsection
@section('script')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
        {{-- <script src=" "></script> --}}


    <script>


        $("#for").select2({
            placeholder: "Select lead for",
            allowClear: true,
            closeOnSelect: false
        })
        $("#add_on_agent").select2({
            placeholder: "Select Agent",
            allowClear: true,
            closeOnSelect: false
        })
        $("#sub_community_id").select2({
            placeholder: "Select Sub Community",
            allowClear: true,
            closeOnSelect: false
        })

        function getLeadNotes() {
            @can("Lead Notes List")
            let lead_id = "{{ $lead->id }}";
            $("#notes").html("");
            $.ajax({
                url: "{{route('ajax.lead-notes')}}",
                type: 'GET',
                dataType: 'json',
                data: {lead_id},
                success: function (result) {
                    let notes = "";
                    result?.data.map(item => {
                        let date = moment(item.created_at).format("DD/MM/YY HH:mm")
                        let imageUrl = `${item.created_by ? item.created_by.thumb_url : "{{ URL::asset('assets/images/users/avatar-8.jpg')}}"}`;
                        notes += `<div class="d-flex mb-4">
                                        <div class="flex-shrink-0">
                                            <img src="${imageUrl}" alt=""
                                                 class="avatar-xs rounded-circle"/>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h5 class="fs-13"><a href="javascript:void(0)">${item.created_by ? item.created_by.name : "System"}</a> <small
                                                    class="text-muted">${date}</small></h5>
                                            <p class="text-muted">${item.note}</p>
                                        </div>
                                    </div>`
                    })
                    $("#notes").html(notes)
                },
                error: function (result) {
                    console.log(result);
                }
            });
            @endcan
        }

        getLeadNotes()

        $(document).on("click", "#addNoteButton", function () {
            @can("lead Notes Create")
            let id = "{{ $lead->id }}";

            let note = $("#note").val();
            if (note == "") {
                return;
            }

            var formData = new FormData();
            formData.append("lead_id", id)
            formData.append("note", note)
            $.ajax({
                url: "{{route('admin.work.leads.add-note')}}",
                type: 'POST',
                dataType: 'json',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                success: function (result) {
                    if (result.status == '1') {
                        showToastify(result.msg, "success")
                        $("#note").val("").trigger("change");
                        getLeadNotes()
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
            @endcan
        })
    </script>
@endsection
