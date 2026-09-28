@if(!auth()->user()->hasRole(["Sales Director", "Line Manager", "Team Leader", "Agent"]))

<p class="text-muted text-uppercase fs-12 fw-medium mb-2 mt-3">Users ({{$usersCount}} Records)</p>
@if ($users->count() > 0)
    @foreach ($users as $user)
        <div class="col-md-12">
            <div class="card mb-1">
                <div class="card-body text-start">
                    <a class="d-flex align-items-center " data-bs-toggle="collapse" href="#user_slug_{{ $user->id }}"
                        role="button" aria-expanded="false" aria-controls="user_{{ $user->id }}_{{ $user->slug }}">
                        <div class="flex-shrink-0 me-2">
                            <img src="{{ URL::asset($user->thumb_url) }}" alt=""
                                class="avatar-xs rounded-circle shadow" />
                        </div>
                        <div class="flex-grow-1 ">


                            <div class="row">
                                <div class="col-md-8">

                                    <h6 class="fs-14 mb-1"><span class="searchable">{{ $user->full_name }}</span></h6>

                                </div>
                                <div class="col-md-4 text-center">
                                    @if ($user->roles->count() > 0)
                                        <span class="badge bg-success rounded-pill">
                                            {{ $user->roles->first()->name }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <p class="text-muted mb-0"> <b>Manager:</b> <span
                                    class="searchable">{{ $user->parent?->full_name }}</span></p>
                            <p class="text-muted mb-0"><span class="searchable">{{ $user->email }}</span></p>
                        </div>
                    </a>
                </div>
                <div class="collapse border-top border-top-dashed" id="user_slug_{{ $user->id }}">
                    <div class="card-body text-start">

                        <ul class="list-unstyled vstack gap-2 mb-0">
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="mdi mdi-account-circle-outline"></i>

                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">ID</h6>
                                        <small class="text-muted">{{ $user->id }}</small>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="mdi mdi-cellphone"></i>

                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Mobile</h6>
                                        <small class="text-muted"><span
                                                class="searchable">{{ $user->mobile }}</span></small>
                                    </div>
                                </div>
                            </li>

                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="mdi mdi-account-cash"></i>

                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Total Leads</h6>
                                        <small class="text-muted">{{ $user->leads->count() }}</small>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="mdi mdi-home"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Total Listings</h6>
                                        <small class="text-muted">{{ $user->listings->count() }}</small>
                                    </div>
                                </div>
                            </li>

                        </ul>
                    </div>
                    <div class="card-footer hstack gap-2">
                        <a href="{{ route('admin.user.users.index') }}?id={{ $user->id }}"
                            class="btn btn-primary btn-sm w-100"><i class="ri-eye-line align-bottom me-1"></i> View</a>
                        {{-- <button class="btn btn-info btn-sm w-100"><i
                        class="ri-question-answer-line align-bottom me-1"></i>
                    Message</button> --}}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@else
    <span class="text-muted">No User Found</span>
@endif

@endif
