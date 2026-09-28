<div class="row mt-2 d-none">
    <div class="col-4">
        <label for="{{$filter_id}}" class="form-label">{{$label}}</label>
        <select name='{{$filter_id}}' id="{{$filter_id}}" class="form-control operator-select">
            <option value="in" selected>is</option>
            <option value="not_in">is not</option>
            <option value="null">is empty</option>
            <option value="not_null">is not empty</option>
        </select>
    </div>
    <div class="col-8">
        <select name='{{$id}}' id="{{$id}}" class="select2 form-control search-inputs searchInput" {{ $selectType??'multiple' }}>
            @foreach ($agents ?? [] as $agent)
            <option value=""></option>
                <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}" value="{{ $agent->id }}">
                    {{ $agent->first_name }}
                    {{ $agent->last_name }}</option>
            @endforeach
        </select>
    </div>
</div>


@push('agent_filter')
    <script>
        if ($('#{{$id}}').hasClass("select2")) {
            $('#{{$id}}').select2({
                placeholder: 'Select {{$label}}',
                allowClear: true,
                focusOnOpen: false,
                closeOnSelect: false,
                minimumResultsForSearch: Infinity,
                templateResult: formatAgent,
            });
        }
    </script>
@endpush
