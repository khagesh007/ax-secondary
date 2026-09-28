<p class="text-muted text-uppercase fs-12 fw-medium mb-2 mt-3">Contacts ({{$contactsCount}} Records)</p>

@if ($contacts->count() > 0)
    @foreach ($contacts as $contact)
        <div class="col-md-12">
            <div class="card mb-1">
                <div class="card-body text-start">
                    <a class="d-flex align-items-center" data-bs-toggle="collapse" href="#contact_{{ $contact->id }}"
                        role="button" aria-expanded="false" aria-controls="contact_{{ $contact->id }}_{{ $contact->id }}">
                        <div class="flex-shrink-0 me-2">
                            <img src="{{'https://ui-avatars.com/api/?name='. $contact->first_name.' '.$contact->last_name}} "
                                alt="" class="avatar-xs rounded-circle shadow" />
                        </div>
                        <div class="flex-grow-1 ">
                            <h6 class="fs-14 mb-1">ID:{{ $contact->id }} <span class="searchable">{{ $contact->first_name }} {{ $contact->last_name }}</span></h6>
                            <p class="text-muted mb-0"> Mobile: <span class="searchable">{{ $contact->mobile }}</span></p>
                        </div>
                    </a>
                </div>
                <div class="collapse border-top border-top-dashed" id="contact_{{ $contact->id }}">
                    <div class="card-body text-start">

                        <ul class="list-unstyled vstack gap-2 mb-0">
                            @if ($contact->email)
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="mdi mdi-email"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">Email</h6>
                                        <small class="text-muted"><span class="searchable">{{$contact->email}}</span></small>
                                    </div>
                                </div>
                            </li>
                            @endif
                            @if ($contact->id)
                            <li>
                                <div class="d-flex">
                                    <div class="flex-shrink-0 avatar-xxs text-muted">
                                        <i class="mdi mdi-id"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">ID</h6>
                                        <small class="text-muted"><span class="searchable">{{$contact->id}}</span></small>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="card-footer hstack gap-2">
                        <a href="{{ route('admin.work.contacts.index') }}?contact={{ $contact->id }}"
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
    <span class="text-muted">No Contact Found</span>
@endif
