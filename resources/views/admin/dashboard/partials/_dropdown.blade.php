<div class="col-12">
    <div class="d-flex align-items-lg-center flex-lg-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-16 mb-1">{{ timeGreetings() }}, <span class="ax-text"> {{ auth()?->user()?->name }}!</span></h4>
            <p class="text-muted mb-0">Here's what's happening with your properties
                today.</p>
        </div>
        @can('Dashboard Reports')

            <div class="flex-grow-1">
                <form action="">
                <label for="dashboard-selector" class="form-label">Select Dashboard</label>
                <select name="dashboard-selector" class="form-control" id="dashboard-selector">
                    <option value="">Select Dashboard</option>
                    <option value="All" {{ request()->is('/') ? 'selected' : '' }} data-url="{{ route('root') }}">All</option>
                    <option value="Primary" {{ request()->is('primary') ? 'selected' : '' }} data-url="{{ route('rootPrimary') }}">Primary</option>
                    <option value="Secondary" {{ request()->is('secondary') ? 'selected' : '' }} data-url="{{ route('rootSecondary') }}">Secondary</option>
                    <option value="Commercial" {{ request()->is('commercial') ? 'selected' : '' }} data-url="{{ route('rootCommercial') }}">Commercial</option>
                    <option value="Abu Dhabi" {{ request()->is('abu-dhabi') ? 'selected' : '' }} data-url="{{ route('rootAbuDhabi') }}">Abu Dhabi</option>
                    <option value="Chinese" {{ request()->is('chinese-team') ? 'selected' : '' }} data-url="{{ route('rootChinese') }}">Chinese</option>
                </select>
            </form>
            </div>

        @endcan


        <div class="mt-3 mt-lg-0 d-none">
            <form action="javascript:void(0);">
                <div class="row g-3 mb-0 align-items-center">
                    <div class="col-sm-auto">
                        <div class="input-group">
                            <input type="text"
                                   class="form-control border-0 dash-filter-picker shadow"
                                   data-provider="flatpickr" data-range-date="true"
                                   data-date-format="d M, Y"
                                   data-deafult-date="01 Jan 2022 to 31 Jan 2022">
                            <div class="input-group-text bg-primary border-primary text-white">
                                <i class="ri-calendar-2-line"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
