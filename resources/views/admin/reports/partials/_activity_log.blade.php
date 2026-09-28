@if ($activities->count() > 0)

@foreach ($activities as $key => $act)
    {{ $key }}
    @foreach ($act as $y => $items)
        @php
            $class = $y;
            $lastWord = substr($class, strrpos($class, '\\') + 1);
        @endphp

        <div class="profile-timeline">
            <div class="accordion accordion-flush" id="todayExample">
                <div class="accordion-item border-0">
                    @foreach ($items as $item)
                        <div class="accordion-header" id="headingOne_{{ $item->id }}">
                            <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse"
                                href="#collapseOne_{{ $item->id }}" aria-expanded="true">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <img src="{{ getAvatar($item->causer->id) }}" alt="{{ $item->causer->id }}"
                                            class="avatar-xs rounded-circle shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="fs-14 mb-1">
                                            {{ $item->causer->name }} made some changes in {{ $lastWord }}
                                        </h6>
                                        <small class="text-muted">{{ $item->description }} </small>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div id="collapseOne_{{ $item->id }}" class="accordion-collapse collapse"
                            aria-labelledby="headingOne_{{ $item->id }}" data-bs-parent="#accordionExample">
                            <div class="accordion-body ms-2 ps-5">




                                @php
                                    $jsonData = json_decode($item->properties, true);
                                @endphp

                                @if (isset($jsonData['attributes']))
                                    Attributes:
                                    @foreach ($jsonData['attributes'] as $key => $value)
                                        @if (is_array($value))
                                            <strong>{{ $key }}:</strong>
                                            <ul>
                                                @foreach ($value as $subKey => $subValue)
                                                    <li>{{ formatText($subKey) }}: {{ $subValue ?? '' }} </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <li><strong>{{ formatText($key) }}:</strong>: {{ $value ?? '' }}</li>
                                        @endif
                                    @endforeach
                                @endif

                                @if (isset($jsonData['old']))
                                    Old:
                                    @foreach ($jsonData['old'] as $key => $value)
                                        @if (is_array($value))
                                            <strong>{{ $key }}:</strong>
                                            <ul>
                                                @foreach ($value as $subKey => $subValue)
                                                    <li>{{ formatText($subKey) }}: {{ $subValue ?? '' }} </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <li><strong>{{ formatText($key) }}:</strong>: {{ $value ?? '' }}</li>
                                        @endif
                                    @endforeach
                                @endif

                                <br>





                                <br>

                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <!--end accordion-->
        </div>
    @endforeach
@endforeach
@else

@include('admin.reports.partials._empty_log')
@endif
