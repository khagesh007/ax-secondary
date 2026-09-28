@foreach ($languages as $i)
    <div class="row mt-2 currentbtn mb-3">
        <div class="col-3 col-md-3">
            <span class="language_name">{{ $i->meta_key }}</span>
        </div>
        <div class="col-7 col-md-7">
            <input type="hidden" name="language_name[]" value="{{ $i->meta_key }}" class="myLanguages">
            <input type="range" step="10" name="language_level[]" class="language_level ranger" id="language_level_{{ $i->meta_key }}" meta-key="{{ $i->meta_key }}" value="{{ $i->meta_value }}"> <span class="level_value_text">{{ $i->meta_value }}</span> %
        </div>
        <div class="col-2 col-md-2">
            <i class="bx bx-trash text-danger deleteBtn cursur-pointer" data-id="{{$i->id}}"></i>
        </div>
    </div>
@endforeach


{{-- <div class="col-12 col-md-12">
    <button class="btn btn-primary saveBulk btn-sm" type="button" id="">Update</button>
</div> --}}

