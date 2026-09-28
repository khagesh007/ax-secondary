<div class="row">
    <div class="col-xxl-12">
        <div class="table-sticky-header-container" id="table-sticky-header">

        </div>
        <div class="card" id="contactList">
            <div class="card-body">

                <table id="datatable" class="table align-middle" style="width:100%">
                    <thead class="table-light2">
                        <tr class="text-uppercase">
                            <th scope="col" style="width: 50px;">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="checkAll" value="option"
                                        data-com.bitwarden.browser.user-edited="yes">
                                </div>
                            </th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>mobile</th>
                            <th>Source</th>
                            <th>Agent</th>
                            <th>Location</th>

                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Budget</th>
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

                                <td><a href="">{{ $item->id }}</a></td>
                                <td>{{ $item->first_name }} {{ $item->last_name }}</td>
                                <td>
                                    @if ($item->agent_id == auth()->user()->id)
                                        {{ $item->mobile }}
                                    @elseif (auth()->user()->hasRole('Super Admin'))
                                        {{ validatePhoneNumber($item->mobile) }}
                                    @else
                                        **********
                                    @endif
                                </td>
                                <td>{{ $item->leadSource->name }}</td>
                                <td>
                                    @php
                                        $created_at = Carbon::parse($item->created_at);
                                        $humanReadableDate = $created_at->diffForHumans();
                                    @endphp

                                    @if ($item->agent)
                                        <div class="d-flex align-items-center userDetails"
                                            data-agent-id="{{ $item->agent?->id }}" title="{{ $item->agent?->name }}">
                                            <div class="flex-shrink-0 me-2">
                                                <img src="{{ $item->agent?->thumb_url }}" alt=""
                                                    class="avatar-xs rounded-circle">
                                            </div>
                                            <div class="flex-grow-1">{{ $item->agent?->first_name }}<p
                                                    title="Created at" class="mb-0">


                                                    <small>{{ $humanReadableDate }}</small>
                                                </p>
                                            </div>
                                        </div>
                                    @else
                                        <div class="d-flex align-items-center">

                                            <div class="flex-grow-1">UnAssigned<p title="Created at" class="mb-0">


                                                    <small>{{ $humanReadableDate }}</small>
                                                </p>
                                            </div>
                                        </div>
                                    @endif

                                </td>
                                <td>
                                    @php
                                        $locations = optional($item->criteria)->locations ?? [];
                                        $path_name = '';

                                        foreach ($locations as $loc) {
                                            $path_name .= $loc->parentable->path_name . '<br>';
                                        }
                                    @endphp
                                    {!! $path_name !!}
                                </td>
                                <td>
                                    @php
                                        $subStatus = '';
                                        if (is_array($item->for)) {
                                            $subStatus = implode(
                                                '',
                                                array_map(function ($subItem) {
                                                    return '<span class="badge align-middle badge-soft-dark">' . $subItem . '</span>';
                                                }, $item->for),
                                            );
                                        } else {
                                            $subStatus = '<span class="badge align-middle badge-soft-dark">Unspecified</span>';
                                        }

                                    @endphp
                                    {!! $subStatus !!}



                                </td>
                                <td><span
                                        class="badge align-middle {{ $item->leadStatus->badge ?? 'color-bg-19' }}">{{ $item->leadStatus->name }}</span>
                                </td>
                                <td>{{ $item->criteria->min_price }} - {{ $item->criteria->max_price }}</td>
                                <td>
                                    @php
                                        $url = route('admin.work.leads.index', ['id' => $item->id]);
                                    @endphp
                                    <a title="Open Listing in New Tab" target="_blank" href="{{ $url }}">
                                        <span class="ri-external-link-line"></span>
                                    </a>
                                </td>

                            </tr>
                            @php
                                $counter++;
                            @endphp
                        @endforeach
                    </body>

                </table>
            </div>
        </div>
    </div>
</div>
