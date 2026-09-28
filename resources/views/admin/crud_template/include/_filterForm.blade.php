<div class="collapse slideIn" id="collapseFilterForm">
    <div class="row">
        <form id="searchForm" class="col-xl-12">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Advanced Filters</h6>
                </div>
                <div class="card-body bg-light">
                    <div class="row">


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
            var operators = ["null", "not_null", "today", "yesterday", "last_week", "last_month", "this_month",
                "this_week"
            ];
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
