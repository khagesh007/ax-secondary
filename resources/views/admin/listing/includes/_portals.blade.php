<div class="card own_property d-none" id="portalsTab">
    <div class="card-header p-05rem d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">Portals</h6>

        <a href="javascript:void(0);" class="" id="viewPortalLogPage">
            <i class="mdi mdi-bell" style="color: #d3a188;"></i>
        </a>
    </div>

    <div class="card-body form-steps">
        @foreach ($portals as $portal)
            <div class="form-check form-switch form-switch-success accordion accordion-flush">
                <input class="form-check-input inputs portal-switchs portalBtns " type="checkbox" name="portals[]"
                    value="{{ $portal->id }}" role="switch" id="portal-switch-{{ $portal->id }}">
                <label class="form-check-label2" for="portal-switch-{{ $portal->id }}">
                    <img src="{{ $portal->thumbnail }}" alt=""
                        style="height: 20px; width: 20px">
                    {{ $portal->name }}
                </label>

                <button class="accordion-button collapsed metaDataBtn "
                    id="metaDataBtn-{{ $portal->id }}" type="button" data-bs-toggle="collapse"
                    data-bs-target="#{{ $portal->slug }}-container"
                    data-portal-name="{{ $portal->name }}" data-property-id=""
                    data-portal-slug="{{ $portal->slug }}" aria-expanded="true"
                    aria-controls="flush-collapseOne"
                    style="width: 40px;float: right; padding: 2px 14px 5px 9px;"
                    ></button>

                <div id="{{ $portal->slug }}-container"
                    class="accordion-collapse meta-data-accordion collapse">

                </div>

            </div>
        @endforeach
    </div>
</div>
