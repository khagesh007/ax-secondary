<div class="col-12">
<div class="card">
    <div class="card-header">
        <h4 class="card-title">Select Phone Number Column</h4>
    </div>
    <div class="card-body">
       <div class="row">


            @foreach ($headers as $key=>$header )
            <div class="col-md-3">
                <div class="form-check form-radio-outline form-radio-light">
                    <input class="form-check-input" type="radio" name="phone_number_index" id="header-{{ $key }}" value="{{ $key }}">
                    <label class="form-check-label" for="header-{{ $key }}">
                        {{ $header }}
                    </label>
                </div>
            </div>
            @endforeach
       </div>
    </div>
</div>
</div>
