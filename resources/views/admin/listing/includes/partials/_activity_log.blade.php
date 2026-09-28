<style>
    .highlight {
        background-color: yellow;
    }
</style>
<form id="search-activity-form">
    <input type="text" name="" class="form-control form-control-sm mb-2" id="search-activity"
    placeholder="Search in Activity Log">
</form>


<div id="scrolltobottom" class="scrolltobottom">
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
                                            <li>{{ $key }}: {{ $value ?? '' }}</li>
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
                                            <li>{{ $key }}: {{ $value ?? '' }}</li>
                                        @endif
                                    @endforeach
                                </small></span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        @include('admin.listing.includes.partials.activity._empty_activities')
    @endif
</div>


<script>
    var objDiv2 = document.getElementById("activity-log-div");
    objDiv2.scrollTop = objDiv2.scrollHeight;

    $('#search-activity_old').on('input', function() {
        $('.highlight').removeClass('highlight');
        var value = $(this).val().toLowerCase();
        value = value.replace(/[^a-zA-Z0-9\s]/g, '');
        console.log(value)
        $('#scrolltobottom .accordion-item').each(function() {

            if (value && $(this).text().toLowerCase().indexOf(value) > -1) {
                $(this).find('.accordion-button').addClass('show');
                $(this).find('.accordion-collapse').addClass('show');
                $(this).find('.accordion-collapse small').highlightText(value);
                $(this).parent().addClass('bg-dark');
            } else {
                $(this).find('.accordion-button').removeClass('show');
                $(this).find('.accordion-collapse').removeClass('show');
                $(this).find('.accordion-collapse small').unhighlightText();
                $(this).parent().removeClass('bg-dark');
            }

        });
    });
    $('#search-activity').on('input', function() {
    $('.highlight').removeClass('highlight');
    var value = $(this).val(); // Remove .toLowerCase() to preserve case sensitivity
    value = value.replace(/[^a-zA-Z0-9\s:]/g, ''); // Preserve colons while removing other special characters
    console.log(value);
    $('#scrolltobottom .accordion-item').each(function() {
        if (value && $(this).text().toLowerCase().indexOf(value.toLowerCase()) > -1) { // Use .toLowerCase() only for comparison
            $(this).find('.accordion-button').addClass('show');
            $(this).find('.accordion-collapse').addClass('show');
            $(this).find('.accordion-collapse small').highlightText(value);
            $(this).parent().addClass('bg-dark');
        } else {
            $(this).find('.accordion-button').removeClass('show');
            $(this).find('.accordion-collapse').removeClass('show');
            $(this).find('.accordion-collapse small').unhighlightText();
            $(this).parent().removeClass('bg-dark');
        }
    });
});


    $('#search-activity_idk').on('input', function() {
    $('.highlight').removeClass('highlight');
    var value = $(this).val().toLowerCase();
    value = value.replace(/[^a-zA-Z0-9\s]/g, '');

    $('#scrolltobottom').find('*').each(function() {
        var $element = $(this);
        if ($element.text().toLowerCase().indexOf(value) > -1) {
            $element.addClass('highlight');
        }
    });
});
    $.fn.highlightText = function(searchText) {
        return this.each(function() {
            var content = $(this).html();
            var regex = new RegExp('\\b(' + searchText + ')\\b', 'ig');
            var highlighted = content.replace(regex, '<span class="highlight">$1</span>');
            $(this).html(highlighted);
        });
    };

    $.fn.unhighlightText = function() {
        return this.each(function() {
            $(this).find('.highlight').contents().unwrap();
        });
    };

    // on pressing enter key in search input do not submit the form
    $('#search-activity').keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
        }
    });


</script>
