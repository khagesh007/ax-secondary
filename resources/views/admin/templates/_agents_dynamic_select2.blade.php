

<div class="col-4">{{$label}}*</div>
<div class="col-8">
    <select name='{{$id}}' id="{{$id}}" class="form-control select2">
        <option value="" selected disabled>{{$label}}</option>

        @foreach ($agents ?? [] as $agent)
        <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}" value="{{ $agent->id }}">
            {{ $agent->first_name }}
            {{ $agent->last_name }}</option>
        @endforeach
    </select>
    <div class="valid-feedback">
        Please Select any {{$label}}.
    </div>
</div>




@push('script-bottom')
<script>

    if ($('#{{$id}}').hasClass("select2")) {
        $('#{{$id}}').select2({
            placeholder: '{{$label}}',
            allowClear: true,
            focusOnOpen: true,
            templateResult: formatAgent,
            templateSelection: formatAgent,
            dropdownParent: $('#generalModal')
        });
    }

</script>
@endpush

