<div class="row sticky-top-duration position-sticky">
    <div class="col-md-12">
        <div class="card p-2" style="
        /* background: #e9e9ee; */
        border: none;
        box-shadow: none;
        padding: 5px 5px !important;
    ">
            <div class="card-body p-1" style="overflow: scroll">
                <form id="filter-form">
                    <div class="d-flex flex-nowrap justify-content-between">
                        <div class="me-1 d-flex justify-content-between gap-2" style="min-width: 150px!important">
                            <input class="form-control-sm form-control" id="date-filter" name="date">
                            <select name='groups[]' id="group-select" class="form-control select2 inputs" multiple>
                                @foreach ($groups as $group)
                                    @if(!is_null($loggedInUser->zone_id))
                                        @if($loggedInUser->zone_id == $group->id)
                                            <option value="{{ $group->id }}" selected>{{ $group->name }}</option>
                                        @endif
                                    @else
                                        <option value="{{ $group->id }}" {{ in_array($group->id, request("groups") ?? []) ? "selected" : "" }}>{{ $group->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
