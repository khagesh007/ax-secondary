@if (isset($duplicates))
@if ($duplicates instanceof Illuminate\Support\Collection && $duplicates->count() > 0)
<div class="col-md-12">
    <div class="card">

        <div class="card-body mt-0 form-steps">

            <h5 class="mt-2">Duplicate Leads</h5>
            <table class="table table-nowrap">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Name</th>

                        <th scope="col">Mobile</th>

                        <th scope="col">Source</th>



                        <th scope="col">Purpose</th>
                        <th scope="col">Type</th>
                        <th scope="col">Agent</th>

                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($duplicates as $lead)
                        <tr>
                            <th scope="row"><a href="#" class="fw-semibold">{{ $lead->id }}</a></th>
                            <td><span class='me-1'><span class='me-1'><i
                                            class='{{ $lead->leadMedium->icon ?? 'mdi mdi-account-cash' }}'></i></span>{{ $lead->first_name }}
                                    {{ $lead->last_name }}</td>
                            <td>{{ $lead->mobile }}</td>

                            <td><span class="display-inline"><img
                                        src='{{ $lead->leadSource?->thumbnail ?? 'https://ui-avatars.com/api/?name=' . $lead->leadSource?->name }}'
                                        class="pe-1" alt=""
                                        style="height: 20px; width: 20px">{{ $lead->leadSource?->name }}</span></td>


                            <td>
                                @if (is_array($lead->for))
                                    @foreach ($lead->for as $item)
                                        <span class="badge align-middle badge-soft-dark">{{ $item }}</span>
                                    @endforeach
                                @else
                                    <span class="badge align-middle badge-soft-dark">Unspecified</span>
                                @endif
                            </td>


                            <td>{{ $lead->leadType?->name }}</td>

                            <td>
                                <div class="d-flex align-items-center userDetails" data-agent-id="${data.agent.id}"
                                    title="${data.agent.name}">
                                    <div class="flex-shrink-0 me-2">
                                        <img src="{{ $lead->agent?->thumb_url }}" alt=""
                                            class="avatar-xs rounded-circle">
                                    </div>
                                    <div class="flex-grow-1">{{ $lead->agent?->full_name }}<p title="Created at"
                                            class="mb-0">
                                            <small>{{ \Carbon\Carbon::parse($lead->created_at)->diffForHumans() }}</small>
                                        </p>
                                    </div>
                                </div>

                            </td>

                            <td><span
                                    class="badge align-middle {{ $lead->leadStatus?->badge ?? 'color-bg-19' }}">{{ $lead->leadStatus?->name }}</span>
                            </td>

                            <td><a href="javascript:void(0);" onclick="viewData({{ $lead->id }})"
                                    class="link-success">View<i class="ri-arrow-right-line align-middle"></i></a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endif

