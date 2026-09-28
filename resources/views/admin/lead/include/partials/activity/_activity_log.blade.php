<div data-simplebar id="scrolltobottom" class="scrolltobottom scrollable-notes">
    @if ($activities)
        @foreach ($activities as $index => $item)
            <div class="accordion-item border-0">
                <div class="accordion-header" id="heading6">
                    <a class="accordion-button p-2 shadow-none collapsed" data-bs-toggle="collapse"
                        href="#collapse{{ $item->id }}" aria-expanded="true">

                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <img src="{{ $item?->causer?->thumb_url ?? URL::asset('assets/images/users/multi-user.jpg') }}"
                                    alt="" class="avatar-xs rounded-circle">
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="fs-14 mb-1"> {{ $item->causer ? $item->causer?->full_name : 'System' }} </h6>
                                <small class="text-muted">{{ $item->description }} - <small class="mb-0 text-muted"
                                        title="{{ $item->created_at }}">
                                        {{ \Carbon\Carbon::parse($item->created_at)->diffForHumans() }}</small>
                                </small>
                            </div>
                        </div>
                    </a>
                </div>
                @php
                    $properties = json_decode($item->properties, true);
                    $old = $properties['old'];
                    $attributes = $properties['attributes'];
                @endphp

                <div id="collapse{{ $item->id }}" class="accordion-collapse hide collapse"
                    aria-labelledby="heading6" data-bs-parent="#accordion{{ $item->id }}">
                    <div class="accordion-body ms-2 ps-5 pt-2">
                        <div class="code-view" id="hiddenLog{{ $item->id }}">
                            <span class="language-markup">
                                <small class="text-muted">

                                    <br><strong>New Data:</strong><br>
                                    @foreach ($attributes as $key => $value)
                                        @if (is_array($value))
                                            <strong>{{ $key }}:</strong>
                                            <ul>
                                                @foreach ($value as $subKey => $subValue)
                                                    <li>{{ $subKey }}: {{ $subValue ?? '' }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                        @if (in_array($key, ['Mobile', 'Whatsapp', 'Phone']) && auth()->user()->hasRole('Agent'))
                                        <li>{{ $key }}: *************</li>
                                            @else
                                                <li>{{ $key }}: {{ $value ?? '' }}</li>
                                            @endif
                                        @endif
                                    @endforeach
                                    <br> <strong>Old data:</strong>
                                    <br>
                                    @foreach ($old as $key => $value)
                                        @if (is_array($value))
                                            <strong>{{ $key }}:</strong>
                                            <ul>
                                                @foreach ($value as $subKey => $subValue)
                                                    <li>{{ $subKey }}: {{ $subValue ?? '' }}</li>
                                                @endforeach
                                            </ul>
                                        @else
                                            @if (in_array($key, ['Mobile', 'Whatsapp', 'Phone']) && auth()->user()->hasRole('Agent'))
                                                <li>{{ $key }}: *************sss </li>
                                            @else
                                                <li>{{ $key }}: {{ $value ?? '' }}</li>
                                            @endif
                                        @endif
                                    @endforeach
                                </small></span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        @include('admin.lead.include.partials.activity._empty_activities')
    @endif
</div>


<script>
    var objDiv2 = document.getElementById("activity-log-div");
    objDiv2.scrollTop = objDiv2.scrollHeight;
</script>
