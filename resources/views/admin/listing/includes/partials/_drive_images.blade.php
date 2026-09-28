{{-- <div class="scrollable-gallery">
    @foreach ($images as $image)
        <div class="photo-element" @can('Change Media Order') data-id="${item.id}"  @endcan>
            <div class="text-center">
                <div class="image-wrapper">
                    <a href="{{ $image['path'] }}" data-lightbox="listing-gallery">
                        <img height="100%" width="100%" src="{{ $image['path'] }}" alt="">
                    </a>
                </div>
                <div class="photo-checkbox">
                    <input type="checkbox" value="{{ $image->id }}" name="media_id[]">
                </div>
            </div>
        </div>
    @endforeach
</div> --}}
<style>
    .gallery-container {
        position: relative;
        overflow: hidden;
        height: 200px;
        /* Set a fixed height for the container */
    }

    .view-image-from-listing {
        position: absolute;
        top: 10px;
        right: 35px;
        padding: 5px;
        z-index: 1;
        cursor: pointer;
    }

    .gallery-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-checkbox {
        position: absolute;
        top: 10px !important;
        left: auto !important;
        z-index: 1 !important;
        right: 10px !important;
    }
</style>

@if ($images->count() == 0)
    <div class="alert alert-info">
        <strong>Info!</strong> No images found.
    </div>
@else
    {{-- <div class="row p-1 folder-image-div" style="max-height: 350px; overflow:scroll">
        @foreach ($images as $media)
            <div class="element-item col-xxl-3 col-xl-3 col-sm-6 photography" data-category="photography"
                data-id="{{ $media->id }}" data-image-id="{{ $media->id }}">

                <div class="gallery-box gallery-box-selection card card-border-effect-none">

                    <label for="gallery_image_{{ $media->id }}">
                        <div class="gallery-container">
                            <img class="gallery-img img-fluid mx-auto" src="{{ asset($media->path) }}" alt="" />
                            <div class="gallery-overlay">
                                <h5 class="overlay-caption">{{ $media->name ?? '---' }}</h5>
                            </div>
                        </div>
                    </label>
                </div>
                <div class="box-content mb-4">
                    <div class="d-flex align-items-center mt-1">
                        <div class="flex-shrink-0">
                            <div class="d-flex gap-3">
                                <input type="checkbox" name="gallery_image[]" id="gallery_image_{{ $media->id }}"
                                    value="{{ $media->id }}" class="image-checkbox">
                                <a href="{{ asset($media->path) }}" target="_blank"
                                    class="btn btn-sm fs-12 btn-link text-body text-decoration-none px-0 view-image-from-listing">
                                    <i class="ri-eye-line text-white align-bottom me-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach


    </div>
    <div class="text-end mt-1">
        <button class="btn btn-primary btn-sm" id="attach-images-from-drive-select-all">Select All</button>
        <button class="btn btn-primary btn-sm" id="attach-images-from-drive">Attach Images</button>
        <button class="btn btn-outline-primary btn-sm btn-load d-none" id="attach-images-from-drive-placeholder" disabled>
            <span class="d-flex align-items-center">
                <span class="flex-grow-1 me-2">
                    Loading...
                </span>
                <span class="spinner-border flex-shrink-0" role="status">
                    <span class="visually-hidden">Loading...</span>
                </span>
            </span>
        </button>
    </div> --}}

    <div class="row p-2" id="">
        @foreach ($images as $media)
            <div class="col-md-3 expanded-images" style="padding-left: 2px;padding-right: 2px" data-id="{{ $media->id }}" id="">
                <div class="text-center">
                    <div class="image-wrapper">
                        <label for="gallery_image_{{ $media->id }}">

                            <img height="100%" width="100%" src="{{ asset($media->path) }}" alt="">
                        </label>

                    </div>

                    <div class="photo-checkbox-v2">
                        <input type="checkbox" name="gallery_image[]" id="gallery_image_{{ $media->id }}"
                            value="{{ $media->id }}" class="image-checkbox">


                        {{-- <input type="checkbox" value="328496" name="media_id[]" class="delete_media_ids"> --}}
                    </div>

                    <div class="photo-checkbox-v3">

                            <a href="{{ asset($media->path) }}" target="_blank"
                                class="btn btn-sm fs-12 btn-link text-body text-decoration-none px-0">
                                <i class="ri-eye-line text-white align-bottom me-1"></i>
                            </a>
                    </div>
                    {{-- <a href="javascript:void(0)" type="button" data-id="328496" class="mt-1 delete-photo">Delete</a> --}}

                </div>
            </div>
        @endforeach

    </div>
    <div class="text-end mt-1">
        {{-- <button class="btn btn-primary btn-sm" id="expand-modal-images_">Expand Modal</button> --}}
        <button class="btn btn-primary btn-sm" id="attach-images-from-drive-select-all">Select All</button>
        <button class="btn btn-primary btn-sm" id="attach-images-from-drive">Attach Images (<span id="selection_count">0</span>)</button>
        <button class="btn btn-outline-primary btn-sm btn-load d-none" id="attach-images-from-drive-placeholder"
            disabled>
            <span class="d-flex align-items-center">
                <span class="flex-grow-1 me-2">
                    Loading...
                </span>
                <span class="spinner-border flex-shrink-0" role="status">
                    <span class="visually-hidden">Loading...</span>
                </span>
            </span>
        </button>
    </div>
@endif
