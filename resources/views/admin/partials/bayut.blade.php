<div class="accordion-body" style="padding:0px;">
    <div data-simplebar>
        {{-- @dd($propertyMeta); --}}
        <!-- Small Tables -->
        <table class="table table-sm table-nowrap">
            <tbody id="bayutMeta">
                {{-- @dd($propertyMeta) --}}
                {{-- // check if $propertyMeta['bayut_status'] is present --}}
                @if (isset($propertyMeta['bayut_status']))
                <tr>
                    <td>Status</td>
                    <td><span class="badge badge-soft-{{strtolower($propertyMeta['bayut_status'] == "Active" ? "success" : "warning")}}">{{$propertyMeta['bayut_status'] ?? ""}}</span></td>
                </tr>
                @endif
                @if (isset($propertyMeta['bayut_rank']))

                <tr>
                    <td>Quality Score</td>
                    <td>
                        <div class="progress animated-progress custom-progress progress-label">
                            <div class="progress-bar {{($propertyMeta['bayut_rank'] >= 80) ? "bg-success" : (($propertyMeta['bayut_rank'] >= 60) ? "bg-warning" : "bg-danger")}}" role="progressbar" style="width: {{$propertyMeta['bayut_rank']}}%" aria-valuenow="{{$propertyMeta['bayut_rank']}}" aria-valuemin="0" aria-valuemax="100" title="{{$propertyMeta['bayut_rank']."%" ?? ""}}"><div class="label">{{$propertyMeta['bayut_rank']."%" ?? ""}}</div></div>
                        </div>
                    </td>
                </tr>
                @endif
                @if (isset($propertyMeta['bayut_level']))

                <tr>
                    <td>Product</td>

                    @php
                        $level = "";

                        if($propertyMeta['bayut_level'] == "Premium"){
                            $level = "Basic";
                        }else{
                           $level = $propertyMeta['bayut_level'];
                        }
                    @endphp
                    <td><span class="badge badge-soft-success badge-bayut-{{strtolower($level)}}">{{$level ?? ""}}</span></td>
                </tr>
                @endif
                @if (isset($propertyMeta['bayut_trucheck_status']))

                <tr>
                    <td>Validation</td>
                    <td><span class="badge badge-soft-dark badge-bayut-{{ $propertyMeta['bayut_trucheck_status'] ?? "not-verified"}}">{{$propertyMeta['bayut_trucheck_status'] ?? null}}</span></td>
                </tr>
                @endif
                @if (isset($propertyMeta['bayut_created_at']))
                @php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['bayut_created_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                @endphp
                <tr>
                    <td>Published Date</td>
                    <td title="{{$propertyMeta['bayut_created_at']}}">{{ $daysAgo ?? null}}</td>
                </tr>
                @endif
                @if (isset($propertyMeta['bayut_updated_at']))
                @php
                    $carbonDateTime = Carbon\Carbon::parse($propertyMeta['bayut_updated_at']);
                    $daysAgo = $carbonDateTime->diffForHumans();
                @endphp
                <tr>
                    <td>Last Update</td>
                    <td title="{{$propertyMeta['bayut_updated_at']}}">{{ $daysAgo ?? null}}</td>
                </tr>
                @endif
                @if (isset($propertyMeta['bayut_url']))
                <tr>
                    <td>URL</td>
                    <td>
                        <a href="{{$propertyMeta['bayut_url']}}" class="link-info" target="_blank">View <i class="ri-external-link-line align-middle"></i></a></td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
