<div class="accordion-body" style="padding:0px;">
    <div data-simplebar style="max-height: 215px; padding: 0px !important;">
        <table class="table table-sm table-nowrap">
            <tbody id="propertyFinderMeta">

            @php
                $qs = $listing['qualityScore'] ?? [];
                $score = (int) ($qs['value'] ?? 0);
                $details = $qs['details'] ?? [];

                $toBs = fn ($c) => $c === 'green' ? 'success' : ($c === 'red' ? 'danger' : 'secondary');
            @endphp

            <tr>
                <td>Quality score</td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <div class="progress flex-grow-1" style="height: 10px;">
                            <div class="progress-bar bg-{{ $toBs($qs['color'] ?? 'secondary') }}"
                                 role="progressbar"
                                 style="width: {{ $score }}%;"
                                 aria-valuenow="{{ $score }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <span class="badge bg-{{ $toBs($qs['color'] ?? 'secondary') }}">{{ $score }}/100</span>
                    </div>
                </td>
            </tr>

            @foreach ($details as $key => $row)
                @php
                    $val = (int) ($row['value'] ?? 0);
                    $weight = (int) ($row['weight'] ?? 0);
                    $right = $weight > 0 ? "$val/$weight" : ($row['tag'] ?? '');
                    $clr = $toBs($row['color'] ?? 'secondary');
                    $help = trim((string) ($row['help'] ?? ''));
                @endphp
                <tr>
                    <td>{{ \Illuminate\Support\Str::headline($key) }}</td>
                    <td class="text-{{ $clr }}">{{ $right }}</td>
                </tr>
                @if ($help !== '' && $row['color'] == 'red')
                    <tr>
                        <td colspan="2">
                            <div class="border rounded bg-light px-3 py-2 mb-2 d-flex align-items-start">
                                <span class="me-2 text-warning" style="line-height:1;">
                                    <i class="ri-lightbulb-flash-line"></i>
                                </span>
                                <div>
                                    <div class="fw-semibold">Tip:</div>
                                    <div class="text-muted small">{{ $help }}</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endif
            @endforeach

            </tbody>
        </table>
    </div>
</div>
