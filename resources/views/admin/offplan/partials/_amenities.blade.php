<div class="">
    @foreach ($amenities ?? [] as $amenity)
        <div class="single-feature col-6 col-3">
            <div class="form-check amenities-form-check">
                <input class="btn-check form-check-input inputs saveAmenities" name="features[]" type="checkbox"
                    value="{{ $amenity->id }}"
                    @if($project->project_amenities)
                    {{ in_array($amenity->id, json_decode($project->project_amenities)??[]) ? 'checked' : '' }}
                    @endif
                    data-title="{{ $amenity->title }}" id="feature-{{ $amenity->id }}"
                    autocomplete="off">
                <label class="btn btn-amenities-primary btn-custom btn-primary" for="feature-{{ $amenity->id }}">
                    {{ $amenity->title }}
                </label>
            </div>
        </div>
    @endforeach
</div>
