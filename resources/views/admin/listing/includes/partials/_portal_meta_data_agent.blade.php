
<div class="col-md-4">
    <div class="card mb-1">
        <div class="card-body">
            <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#leadDiscovered11"
                role="button" aria-expanded="false" aria-controls="leadDiscovered11">
                <div class="flex-shrink-0">
                    <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644265820230215063058.png"
                        alt="" class="avatar-xs rounded-circle">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">Property Finder</h6>
                    <p class="text-muted mb-0">{{ $pf_level->meta_value??'-' }}</p>
                </div>
                <span class="text-center mt-2" style="font-size: 20px"><i class=" ri-arrow-down-s-line" ></i></span>

            </a>
        </div>
        <div class="border-top border-top-dashed collapse" id="leadDiscovered11" style="">
            <div class="card-body">
                <ul class="list-unstyled vstack gap-2 mb-0" id="pf-meta-data-div">
                    @include('admin.listing.includes.partials._meta_data',['metas'=>$pf_metas])
                </ul>
            </div>

        </div>
    </div>
</div>
<div class="col-md-4">
    <div class="card mb-1">
        <div class="card-body">
            <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#leadDiscovered22"
                role="button" aria-expanded="false" aria-controls="leadDiscovered22">
                <div class="flex-shrink-0">
                    <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644259820230215062958.png"
                        alt="" class="avatar-xs rounded-circle">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">Bayut</h6>
                    <p class="text-muted mb-0">{{ $bayut_level->meta_value??'-' }}</p>
                </div>
                <span class="text-center mt-2" style="font-size: 20px"><i class=" ri-arrow-down-s-line" ></i></span>

            </a>

        </div>
        <div class="border-top border-top-dashed collapse" id="leadDiscovered22" style="">
            <div class="card-body">
                <ul class="list-unstyled vstack gap-2 mb-0" id="bayut-meta-data-div">
                    @include('admin.listing.includes.partials._meta_data',['metas'=>$bayut_metas])
                </ul>
            </div>

        </div>

    </div>
</div>
<div class="col-md-4">
    <div class="card mb-1">
        <div class="card-body">
            <a class="d-flex align-items-center collapsed" data-bs-toggle="collapse" href="#leadDiscovered33"
                role="button" aria-expanded="false" aria-controls="leadDiscovered33">
                <div class="flex-shrink-0">
                    <img src="https://secondaryaxcrm.s3.eu-west-1.amazonaws.com/images/thumb167644264020230215063040.png"
                        alt="" class="avatar-xs rounded-circle">
                </div>
                <div class="flex-grow-1 ms-3">
                    <h6 class="fs-14 mb-1">Dubizzle</h6>
                    <p class="text-muted mb-0">{{ $dubizzle_level->meta_value??'-' }}</p>
                </div>
                <span class="text-center mt-2" style="font-size: 20px"><i class=" ri-arrow-down-s-line" ></i></span>

            </a>
        </div>
        <div class="border-top border-top-dashed collapse" id="leadDiscovered33" style="">
            <div class="card-body">
                <ul class="list-unstyled vstack gap-2 mb-0" id="dubizzle-meta-data-div">
                    @include('admin.listing.includes.partials._meta_data',['metas'=>$dubizzle_metas])
                </ul>
            </div>
        </div>
    </div>
</div>
