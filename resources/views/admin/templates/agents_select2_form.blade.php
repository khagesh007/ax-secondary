@if (isset($layout) && $layout == 'horizontal')
<div class="col-4 mt-2">{{$label}}{{$required=='true'?'*':''}}</div>
<div class="col-8 mt-2">
    <select name='{{$id}}' id="{{$id}}"
        class="form-control select2 inputs" {{$required=='true'?'required':''}}>
        <option selected disabled value="">{{$label}}</option>
        </option>
        @foreach ($agents ?? [] as $agent)
        <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}" value="{{ $agent->id }}">
            {{ $agent->first_name }}
            {{ $agent->last_name }}</option>
        @endforeach
    </select>
    <div class="invalid-feedback">
        Please choose {{$label}}.
    </div>
</div>
@else
<div class="col-md-12">
    <label for="{{$id}}" class="form-label">{{$label}}{{$required=='true'?'*':''}}</label>
    <select name='{{$id}}' id="{{$id}}" class="form-control select2 inputs" {{$required=='true'?'required':''}}>
        <option selected disabled value="">{{$label}}</option>
        @foreach ($agents ?? [] as $agent)
        <option data-image="{{ $agent->thumbnail }}" data-status="{{ $agent->status }}" value="{{ $agent->id }}">
            {{ $agent->first_name }}
            {{ $agent->last_name }}</option>
        @endforeach
    </select>
    <div class="invalid-feedback"> Please choose {{$label}}. </div>
</div>
@endif

@push('script-bottom')
<script>
    if ($('#{{$id}}').hasClass("select2")) {
        $('#{{$id}}').select2({
            placeholder: '{{$label}}',
            allowClear: true,
            focusOnOpen: false,
            templateResult: formatAgent,
            templateSelection: formatAgent,
        });
    }
</script>
@endpush
