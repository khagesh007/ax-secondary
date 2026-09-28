<div class="row">
    <div class="col-xxl-12">
        <div class="table-sticky-header-container" id="table-sticky-header">

        </div>
        <div class="card" id="contactList">
            <div class="card-body">

                <div class="table-responsive mb-4">
                    <table id="datatable" class="table align-middle" style="width:100%">
                        <thead class="table-light2">
                            <tr class="text-uppercase table-light2 ">
                                <th scope="col" style="width: 10px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                            data-com.bitwarden.browser.user-edited="yes">
                                    </div>
                                </th>

                                {{-- <th>ID</th> --}}

                                <th>Ref</th>
                                <th>Unit</th>
                                <th>Type</th>
                                <th>Category</th>
                                {{-- <th>Rera Permit No.</th> --}}
                                {{-- <th>Rera Validity </th> --}}
                                <th width="15%">Location</th>
                                <th>Beds</th>
                                <th>Price</th>
                                <th>Agent</th>
                                {{-- <th>Updated by</th> --}}
                                {{-- <th width="15%">Portals</th> --}}
                                {{-- <th width="3%">State</th> --}}
                                <th>Status</th>
                                {{-- <th>Created at</th> --}}
                                <th>Published at</th>
                                <th>Preview</th>

                            </tr>
                        </thead>

                        <body>
                            @php
                                $counter = 1;
                                use Carbon\Carbon;
                            @endphp

                            @foreach ($data as $item)
                                <tr>
                                    <td>
                                        <div class="form-check"><input class="form-check-input selectable-checkbox"
                                                type="checkbox" name="chk_child" value="{{ $item->id }}"></div>
                                    </td>
                                    {{-- <td> {{ $item->id }} </td> --}}
                                    <td>
                                        <span title=><a href="javascript:void(0)"
                                                class="font-12 badge  {{ replaceSpaces($item->for) }} badge-border">{{ $item->ref_no }}</a></span>
                                    </td>
                                    <td> {{ $item->unit_no }}</td>
                                    <td>
                                        {{ $item->property_category ? $item->property_category?->title : '-' }}
                                    </td>

                                    <td> <span class="badge {{ replaceSpaces($item->for) }} badge-border ">
                                            {{ $item->for }}</span></td>
                                    {{-- <td> {{ $item->rera_permit_number }}</td> --}}
                                    {{-- <td> validity </td> --}}
                                    <td>
                                        @if ($item->communityProperty)
                                            <p>{{ $item->communityProperty?->path_name }}</p>
                                        @elseif ($item->subCommunity)
                                            <p>{{ $item->subCommunity?->path_name }}</p>
                                        @else
                                            <p>-</p>
                                        @endif
                                    </td>
                                    <td> {{ $item->bed->name }}</td>
                                    <td>{{ formatPrice2($item->price->price) }} </td>
                                    <td>
                                        <div class="d-flex align-items-center userDetails"
                                            data-agent-id="{{ $item->agent->id }}" title="{{ $item->agent->name }}">
                                            <div class="flex-shrink-0 me-2">
                                                <img src="{{ $item->agent->thumb_url }}" alt=""
                                                    class="avatar-xs rounded-circle">
                                            </div>
                                            <div class="flex-grow-1">{{ $item->agent->first_name }}<p
                                                    title="Created at" class="mb-0">

                                                    @php
                                                        $created_at = Carbon::parse($item->created_at);
                                                        $humanReadableDate = $created_at->diffForHumans();
                                                    @endphp
                                                    <small>{{ $humanReadableDate }}</small>
                                                </p>
                                            </div>
                                        </div>

                                    </td>
                                    {{-- <td>
                                        <div class="d-flex align-items-center userDetails"
                                            data-agent-id="{{ $item->updatedBy?->id }}"
                                            title="{{ $item->updatedBy?->name }}">
                                            <div class="flex-shrink-0 me-2">
                                                <img src="{{ $item->updatedBy?->thumb_url }}" alt=""
                                                    class="avatar-xs rounded-circle">
                                            </div>
                                            <div class="flex-grow-1">{{ $item->updatedBy?->first_name }}<p
                                                    title="Updated at" class="mb-0">
                                                    @php
                                                        $updated_at = Carbon::parse($item->updated_at);
                                                        $humanReadableDate = $updated_at->diffForHumans();
                                                    @endphp
                                                    <small>{{ $humanReadableDate }}</small>

                                                </p>
                                            </div>
                                        </div>
                                    </td> --}}
                                    {{-- <td>
                                        @foreach ($item->portals as $portal)
                                            <a href="javascript:void(0)" class="remove-portal-button"
                                                data-id="{{ $item->id }}" data-portal="{{ $item->portal_id }}"
                                                title="{{ $item->name }}">
                                                <img src="{{ $portal->portal->thumbnail }}" alt=""
                                                    style="height: 20px; width: 20px">
                                            </a>
                                        @endforeach
                                    </td> --}}
                                    {{-- <td> {{ $item->state }}</td> --}}
                                    <td>


                                        <div title="{{ $item->publish_status }}"
                                            class="d-flex justify-content-between gap-2 align-items-center">
                                            <span
                                                class="btn btn-soft-{{ mapStatusToBadgeClass($item->publish_status) }} btn-label btn-sm rounded-pill pl-12">
                                                {{ $item->publish_status }}{!! $item->is_live ? '<span class="is_live"></span>' : '' !!}
                                            </span>

                                        </div>
                                    </td>
                                    {{-- <td> {{ date('D/M/Y h:m:s', strtotime($item->created_at)) }}</td> --}}
                                    <td> {{ date('d/M/Y h:m:s', strtotime($item->published_date)) }}</td>
                                    <td>
                                        @php
                                            $url = route('admin.listing.index', ['ref_number' => $item->ref_no]);
                                        @endphp
                                        <a title="Open Listing in New Tab" target="_blank" href="{{ $url }}">
                                            <span class="ri-external-link-line"></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </body>

                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
