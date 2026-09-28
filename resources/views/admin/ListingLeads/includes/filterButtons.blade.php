<div class="float-end">
    <div class="d-flex justify-content-end align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-soft-secondary waves-effect btn-m w-100 action-filter"
                onclick="toggleAdvanceSearch();"><i
                class="ri-equalizer-fill me-1 align-bottom"></i>
            Advanced Filters
        </button>
        <div class="d-flex justify-content-between gap-1">
            {{-- @foreach($portals ?? [] as $portal)
                <a href="javascript:void(0)" class="select-portal" data-id="{{ $portal->id }}"
                   title="{{ $portal->name }}">
                    <img src="{{ $portal->thumbnail }}" alt="" style="height: 20px; width: 20px">
                </a>
            @endforeach --}}
        </div>
        <div class="d-flex justify-content-between gap-1">
            {{-- <a href="javascript:void(0)" data-status="PUBLISHED" title="show published"
               class="text-success select-publish-status">
                <i class="ri-checkbox-circle-line me-2 align-bottom" style="font-size: 20px"></i>
            </a> --}}

            {{-- <a href="javascript:void(0)" data-status="PUBLISHED" title="Published With State"
            class="text-info select-publish-status_state_approved"><i class="bx bx-check-shield me-2 align-bottom" style="font-size: 20px;margin-top:5px;"></i> --}}
         </a>

            {{-- <a href="javascript:void(0)" data-status="DRAFT" title="show draft"
               class="text-primary select-publish-status">
                <i class="ri-edit-circle-line me-2 align-bottom" style="font-size: 20px"></i>
            </a> --}}
            {{-- <a href="javascript:void(0)" data-status="UNPUBLISHED" title="show unpublished"
               class="text-danger select-publish-status">
                <i class="ri-close-circle-line me-2 align-bottom" style="font-size: 20px"></i>
            </a> --}}
        </div>
        <a href="javascript:void(0)" class="text-secondary" title="Refresh table"
           id="refreshTableButton">
            <i class="bx bx-refresh me-1 align-bottom" style="font-size: 25px"></i>
        </a>


     </a>
    </div>
</div>
