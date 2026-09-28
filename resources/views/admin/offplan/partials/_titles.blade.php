@foreach ($developer->titles as $title)
    <div class="col-md-6 removeClass-{{ $title->id }}">
        <div class="row mt-2">
            <div class="col-md-10">
                <input type="hidden" name="ids[]" value="{{ $title->id }}">
                <input type="text" class="form-control" placeholder="Project Name" name="titles[]"
                    value="{{ $title->value }}">
            </div>
            <div class="col-md-2">
                <span class="btn btn-danger remove-title" data-id="{{ $title->id }}" data-type="title"><i
                    class="mdi-delete-outline mdi"></i></span>
            </div>
        </div>
    </div>
@endforeach
