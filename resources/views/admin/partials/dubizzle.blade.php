<div class="accordion-body" style="padding:0px;">
    <div data-simplebar>
        <!-- Small Tables -->
        <table class="table table-sm table-nowrap">
            <tbody id="dubizzleMeta">

                @if (isset($propertyMeta['dubizzle_status']))
                <tr>
                    <td>Status</td>
                    <td><span class="badge badge-soft-{{strtolower($propertyMeta['dubizzle_status'] == "Active" ? "success" : "warning")}}">{{$propertyMeta['dubizzle_status'] ?? ""}}</span></td>
                </tr>
                @endif
                @if (isset($propertyMeta['dubizzle_rank']))
                <tr>
                    <td>Quality Score</td>
                    <td>
                        <div class="progress animated-progress custom-progress progress-label">
                            <div class="progress-bar {{($propertyMeta['dubizzle_rank'] >= 80) ? "bg-success" : (($propertyMeta['dubizzle_rank'] >= 60) ? "bg-warning" : "bg-danger")}}" role="progressbar" style="width: {{$propertyMeta['dubizzle_rank']}}%" aria-valuenow="{{$propertyMeta['dubizzle_rank']}}" aria-valuemin="0" aria-valuemax="100" title="{{$propertyMeta['dubizzle_rank']."%" ?? ""}}"><div class="label">{{$propertyMeta['dubizzle_rank']."%" ?? ""}}</div></div>
                        </div>
                    </td>
                </tr>
                @endif
                @if (isset($propertyMeta['dubizzle_level']))
                <tr>
                    <td>Product</td>
                    <td><span class="badge badge-soft-success badge-bayut-{{strtolower($propertyMeta['dubizzle_level'])}}">{{$propertyMeta['dubizzle_level'] ?? ""}}</span></td>
                </tr>
                @endif
                @if (isset($propertyMeta['dubizzle_trucheck_status']))
                <tr>
                    <td>Verification</td>
                    <td><span class="badge badge-soft-dark badge-bayut-{{$propertyMeta['dubizzle_trucheck_status'] && $propertyMeta['dubizzle_trucheck_status']== "truchecked" ? "verified" :"not-verified"}}">{{$propertyMeta['dubizzle_trucheck_status'] ?? null}}</span></td>
                </tr>
                @endif
                @if (isset($propertyMeta['dubizzle_created_at']))
                @php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['dubizzle_created_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                @endphp
                <tr>
                    <td>Published Date</td>
                    <td title="{{$propertyMeta['dubizzle_created_at']}}">{{ $daysAgo ?? null}}</td>
                </tr>
                @endif
                @if (isset($propertyMeta['dubizzle_updated_at']))

                @php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['dubizzle_updated_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                @endphp
                <tr>
                    <td>Last Update</td>
                    <td title="{{$propertyMeta['dubizzle_updated_at']}}">{{ $daysAgo ?? null}}</td>
                </tr>
                @endif
                @if (isset($propertyMeta['dubizzle_refreshed_at']))
                @php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['dubizzle_refreshed_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                @endphp
                <tr>
                    <td>Last Refreshed</td>
                    <td title="{{$propertyMeta['dubizzle_refreshed_at']}}">{{ $daysAgo ?? null}}</td>
                </tr>
                @endif
                @if (isset($propertyMeta['dubizzle_url']))
                <tr>
                    <td>URL</td>
                    <td>
                        <a href="{{$propertyMeta['dubizzle_url']}}" class="link-info" target="_blank">View <i class="ri-external-link-line align-middle"></i></a></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
