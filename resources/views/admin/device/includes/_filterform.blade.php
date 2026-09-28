
<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Advanced Filters</h6>
                </div>
                <div class="card-body bg-light">
                    <div class="row">
                        <div class="col-xl-4 col-md-6 col-sm-12">

                            <div class="row">
                                <div class="col-4">
                                    <label for="agent" class="form-label">Lead ID</label>
                                    <select name='search_id_operator' id="search_id_operator"
                                        class="form-control   operator-select">
                                        <option value="in" selected>is</option>
                                        <option value="not_in">is not</option>
                                        <option value="null">is empty</option>
                                        <option value="not_null">is not empty</option>
                                    </select>
                                </div>
                                <div class="col-8">

                                    <input type="number" class="form-control search-inputs searchInput" id="search_id"
                                        name="id" placeholder="Search ID" value="">
                                </div>
                            </div>
                           {{-- @include('admin.filters._agent') --}}
                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-12">


                        </div>
                        <div class="col-xl-4 col-md-6 col-sm-12">

                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@section('pagejs')
    <script>
        $(document).on('change', '.operator-select', function() {
            var operator = $(this).val();
            var input = $(this).closest('.row').find('.searchInput');
            var select2Element = input.next('.select2-container').find('select');
            var operators = ["null", "not_null", "today", "yesterday", "last_week", "last_month", "this_month", "this_week"];
            if (operators.includes(operator)) {
                select2Element.prop('disabled', true);
                input.prop('disabled', true);
            } else {
                select2Element.prop('disabled', false);
                input.prop('disabled', false);
            }
        });


    </script>



@endsection

