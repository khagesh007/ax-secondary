<div class="tab-pane fade active show" id="" role="tabpanel" aria-labelledby="">
    @foreach ($keys as $key => $value)
        <div>
            @php
                $random = rand(1, 10000);
            @endphp

            <div class="row  getInfo" id="{{ $value->slug }}">
                <div class="col-2">
                    <div class="mb-3">
                        <input type="text" class="form-control {{ $value->slug }}_{{ $random }} module"
                            placeholder="Module" id="module_{{ $value->slug }}_{{ $random }}"
                            value="{{ $value->module }}" readonly>
                    </div>
                </div>
                <div class="col-2">
                    <div class="mb-3">
                        <input type="text" class="form-control {{ $value->slug }}_{{ $random }} slug"
                            placeholder="Slug" id="slug_{{ $value->slug }}_{{ $random }}"
                            value="{{ $value->slug }}" readonly>
                    </div>
                </div>
                <div class="col-3">
                    <div class="mb-3">
                        <input type="text" class="form-control {{ $random }} key" placeholder="Key"
                            id="key_{{ $random }}" value="{{ $value->key }}">
                    </div>
                </div>
                <div class="col-3">
                    <div class="mb-3">
                        <input type="text" class="form-control {{ $random }} value" placeholder="Value"
                            value="{{ $value->value }}" id="value_{{ $random }}">
                    </div>
                </div>
                <div class="col-2">
                    <button class="btn btn-sm btn-primary submit_form" data-class="{{ $key }}_{{ $random }}">Update</button>
                    <button type="button" class="btn btn-sm btn-danger delete_form" data-id="{{ $value->id }}">
                        <i class="ri-delete-bin-7-line"></i>
                    </button>
                </div>
            </div>
    @endforeach

    <div class="row  getInfo" id="">
        <div class="col-2">
            <div class="mb-3">
                <input type="text" class="form-control module" placeholder="Module" id="module_"
                    value="{{ $module }}" readonly>
            </div>
        </div>
        <div class="col-2">
            <div class="mb-3">
                <input type="text" class="form-control slug" placeholder="Slug" id="slug"
                    value="{{ $slug }}" readonly>

            </div>
        </div>
        <div class="col-3">
            <div class="mb-3">
                <input type="text" class="form-control key" placeholder="Key" id="" value="">
            </div>
        </div>
        <div class="col-3">
            <div class="mb-3">
                <input type="text" class="form-control value" placeholder="Value" value="" id="v">
            </div>
        </div>
        <div class="col-2">
            <button class="btn btn-sm btn-primary submit_form" data-class="">Save</button>
        </div>
    </div>
</div>
</div>
