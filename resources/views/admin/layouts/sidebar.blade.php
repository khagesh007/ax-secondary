<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu {{ env('APP_ENV') == 'local' ? 'localEnv' : '' }}">
    {{-- Snowflake code to be removed --}}
    {{-- <canvas class="snow"></canvas> --}}
    {{-- Snowflake code to be removed ends here --}}
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo/logo.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo/logo.png') }}" alt="" height="50">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo/logo.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo/logo.png') }}" alt="" height="50">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span>@lang('translation.menu')
                    </span></li>
                <li class="nav-item rm-disabled"  >
                    <a class="nav-link menu-link rm-disabled"

                       href="/" aria-expanded="false" id="DashActive">
                        <i class="mdi mdi-speedometer"></i> <span>@lang('translation.dashboards')
                        </span>
                    </a>
                </li>
                {{-- @can('Dashboard V2')
                <li class="nav-item rm-disabled">
                    <a class="nav-link menu-link rm-disabled" href="{{ route('root.v2') }}" aria-expanded="false" id="DashActivev2">
                        <i class="mdi mdi-speedometer"></i> <span>@lang('translation.dashboards') V2
                        </span>
                    </a>
                </li>
                @endcan --}}
                @can('Team KPI Reports')
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="kpiReportSidebar" href="{{ route('reports.kpi-v3.dashboard') }}"
                           aria-expanded="false">
                            <i class="mdi mdi-file-outline"></i> <span>KPI Reports</span>
                        </a>
                    </li>
                @endcan
                @can('Properties List')
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="sidebarListings" href="{{ route('admin.listing.index') }}"
                            aria-expanded="false">
                            <i class="mdi mdi-home"></i> <span>Listing</span>
                        </a>
                    </li>
                @endcan
                @can('Call Logs List')
                    <li class="nav-item">
                        <a class="nav-link menu-link d-none" id="callLog" href="{{ route('admin.calls.index') }}"
                            aria-expanded="false">
                            <i class="mdi mdi-cellphone"></i> <span>Call Logs</span>
                        </a>
                    </li>
                @endcan

                @canany(['Rents List', 'Sales List'])
                    {{--                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Listings</span></li> --}}
                    {{--                    <li class="nav-item"> --}}
                    {{--                        <a class="nav-link menu-link" href="#sidebarListing" data-bs-toggle="collapse" role="button" --}}
                    {{--                           aria-expanded="false" aria-controls="sidebarListing"> --}}
                    {{--                            <i class="mdi mdi-home"></i> <span>Listing --}}
                    {{--                        </span> --}}
                    {{--                        </a> --}}
                    {{--                        <div class="collapse menu-dropdown" id="sidebarListing"> --}}
                    {{--                            <ul class="nav nav-sm flex-column"> --}}
                    {{--                                @can('Rents List') --}}
                    {{--                                    <li class="nav-item"> --}}
                    {{--                                        <a href="{{route('admin.listing.rent.index')}}" class="nav-link" id="sidebarForRent">For Rent</a> --}}
                    {{--                                    </li> --}}
                    {{--                                @endcan --}}
                    {{--                                @can('Sales List') --}}
                    {{--                                    <li class="nav-item"> --}}
                    {{--                                        <a href="{{route('admin.listing.sale.index')}}" class="nav-link" id="sidebarForSale">For Sale --}}
                    {{--                                        </a> --}}
                    {{--                                    </li> --}}
                    {{--                                @endcan --}}
                    {{--                            </ul> --}}
                    {{--                        </div> --}}
                    {{--                    </li> --}}
                @endcanany

                @can('Leads List')
                    <li class="nav-item rm-disabled">
                        <a class="nav-link menu-link rm-disabled" id="sidebarLeads"
                            href="{{ route('admin.work.leads.index') }}" aria-expanded="false">
                            <i class="mdi mdi-account-cash"></i> <span>Leads</span>
                        </a>
                    </li>
                @endcan







                @can('Contacts List')

                    @if ((auth()->user()->region_id != 2 && auth()->user()->parent_id != 699) || !auth()->user()->hasRole('Agent'))
                        <li class="nav-item">
                            <a class="nav-link menu-link" id="sidebarContacts"
                                href="{{ route('admin.work.contacts.index') }}" aria-expanded="false">
                                <i class="mdi mdi-contacts"></i> <span>Contacts</span>
                            </a>
                        </li>
                    @endif
                @endcan

                @if (auth()->user()->parent_id != 32 && auth()->user()->id != 32)
                    @can('Listing Leads List')
                        <li class="nav-item">
                            <a class="nav-link menu-link" id="listing-leads" href="{{ route('listings.leads.index') }}"
                                aria-expanded="false">
                                <i class="mdi mdi-home-edit"></i> <span>Listing Leads</span>
                            </a>
                        </li>
                    @endcan
                @endif

                    @can('Pipeline List')
                        <li class="nav-item">
                            <a class="nav-link menu-link" id="sidebarPipelines" href="{{ route('admin.pipeline.index') }}"
                                aria-expanded="false">
                                <i class="ri-git-merge-line"></i> <span>Pipelines</span>
                            </a>
                        </li>
                    @endcan


                @can('Ax Calculator')
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="axCalculator" href="{{ route('admin.ax.calculator.index') }}"
                            aria-expanded="false">
                            <i class="mdi mdi-calculator-variant-outline"></i> <span>Deal Calculator</span>
                        </a>
                    </li>
                @endcan


                @canany(['Deals List'])
                <li class="nav-item d-none">
                    <a class="nav-link menu-link" href="#sidebarDealBlock" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarDealBlock" id="dealSidebarBlock">
                        <i class="mdi mdi-cash-check"></i> <span>Deals</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDealBlock">
                        <ul class="nav nav-sm flex-column">
                            @can('Deals Dashboard')
                            <li class="nav-item">
                                <a href="{{ route('admin.work.deals.dashboard') }}" class="nav-link"
                                    id="dealDashboardSidebar"><span>Dashboard</span></a>
                            </li>
                        @endcan
                            @can('Deals List')
                            <li class="nav-item">
                                <a class="nav-link" id="sidebarDeals" href="{{ route('admin.work.deals.index') }}"
                                    aria-expanded="false">
                                    <span>List</span>
                                </a>
                            </li>
                        @endcan

                        </ul>
                    </div>
                </li>
            @endcanany

                @canany(['Events List'])
                    <li class="nav-item">
                        <a href="{{ route('admin.events.index') }}" class="nav-link" id="eventSidebar"><i
                                class="mdi mdi-calendar"></i> <span>Calendar</span></a>
                    </li>
                @endcanany
                @can('Documents Library')
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="documentLibrary" href="{{ route('admin.documents.index') }}"
                            aria-expanded="false">
                            <i class="mdi mdi-folder-outline"></i> <span>Document Library</span>
                        </a>
                    </li>
                @endcan


                @canany(['Jobs List', 'Property Shoot List'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarJobs" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarJobs" id="jobActive">
                            <i class="mdi mdi-calendar"></i> <span>Jobs</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarJobs">
                            <ul class="nav nav-sm flex-column">
                                @can('Jobs List')
                                    <li class="nav-item d-none">
                                        <a href="{{ route('admin.jobs.index') }}" class="nav-link" id="jobSidebar">Jobs</a>
                                    </li>
                                @endcan
                                @can('Property Shoot List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.jobs.shoot.index') }}" class="nav-link"
                                            id="shootJobSidebar"><span>Property Shoot</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['Agent Reports', 'Agent Calender Reports'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarReports" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarReports" id="reportsActive">
                            <i class="mdi mdi-file"></i> <span>Reports
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarReports">
                            <ul class="nav nav-sm flex-column">
                                @can('Team KPI Reports')
                                    <li class="nav-item d-none">
                                        <a href="{{ route('reports.team.kpi-v2.team.dashboard') }}" class="nav-link"
                                           id="sidebarTeamKpiV2Report"><span>Team KPI Reports</span></a>
                                    </li>
                                @endcan

                                @if(auth()->user()->hasRole(["Agent"]))
                                    <li class="nav-item">
                                        <a href="{{ route('reports.group.kpi-v2.view', auth()->id()) }}" class="nav-link"
                                           id="sidebarAgentKpiV2Report"><span>Agent KPI Dashboard</span></a>
                                    </li>
                                @endif

                                    @can('Agent Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.reports.agent') }}" class="nav-link"
                                            id="sidebarAgentReports"><span>Agent Reports</span></a>
                                    </li>
                                @endcan
                                    @can('Agent Consolidate Reports')
                                        <li class="nav-item">
                                            <a href="{{ route('admin.reports.agent.consolidate.report') }}" class="nav-link"
                                               id="sidebarAgentReports"><span>Agent Consolidate Reports</span></a>
                                        </li>
                                    @endcan
                                @can('Agent Calender Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.reports.calendar') }}" class="nav-link"
                                            id="sidebarAgentReports"><span>Calendar Reports</span></a>
                                    </li>
                                @endcan
                                @can('Group Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.reports.group') }}" class="nav-link"
                                            id="sidebarGroupReports"><span>Group Reports</span></a>
                                    </li>
                                @endcan
                                @can('Group Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('reports.group.v4.index') }}" class="nav-link"
                                            id="sidebarGroupReports"><span>Team Reports</span></a>
                                    </li>
                                @endcan
                                @can('Primary Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('rootPrimary') }}" class="nav-link"
                                            id="sidebarGroupReports"><span>Primary Report</span></a>
                                    </li>
                                @endcan
                                @can('Commercial Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('rootPrimary') }}" class="nav-link"
                                            id="sidebarGroupReports"><span>Commercial Report</span></a>
                                    </li>
                                @endcan
                                @can('Request Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('report.requests.index') }}" class="nav-link"
                                            id="sidebarRequestReports"><span>Request Report</span></a>
                                    </li>
                                @endcan
                                @can('Request Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.reports.listings') }}" class="nav-link"
                                            id="sidebarReraReport"><span>Listing Rera Report</span></a>
                                    </li>
                                @endcan
                                @can('Bulk SMS')
                                    <li class="nav-item">
                                        <a href="{{ route('contacts.bulk.sms.index') }}" class="nav-link"
                                            id="sidebarReraReport"><span>Bulk Sms</span></a>
                                    </li>
                                @endcan
                                @can('SMS Campaign')
                                <li class="nav-item">
                                    <a href="{{ route('contacts.sms.campaigns.index') }}" class="nav-link"
                                        id="sidebarReraReport"><span>SMS Campaign</span></a>
                                </li>
                                @endcan
                                @can('Group Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('reports.group.v5.index') }}" class="nav-link"
                                           id="sidebarGroupV5Reports"><span>Group V5 Reports</span></a>
                                    </li>
                                @endcan
                                @can('Agent Insights Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('reports.agent-insights.index') }}" class="nav-link"
                                           id="sidebarAgentInsightsReports"><span>Agent Insights</span></a>
                                    </li>
                                @endcan
                                @can('Lead Insights Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('reports.lead-insights.index') }}" class="nav-link"
                                           id="sidebarLeadInsightsReports"><span>Lead Insights</span></a>
                                    </li>
                                @endcan
                                @can('Listing Insights Reports')
                                    <li class="nav-item">
                                        <a href="{{ route('reports.listing-insights.index') }}" class="nav-link"
                                           id="sidebarListingInsightsReports"><span>Listing Insights</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['Execute Fetch Jobs', 'Import Offplan Properties'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarOffplan" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarOffplan" id="jobActive">
                            <i class="bx bx-import"></i> <span>Offplan</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarOffplan">
                            <ul class="nav nav-sm flex-column">
                                @can('Execute Fetch Jobs')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" id="sidebarStartJobs"
                                            href="{{ route('jobs.public.startJobs') }}" aria-expanded="false">
                                            <span>Execute Jobs</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Import Offplan Properties')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" id="sidebarImportJobs"
                                            href="{{ route('jobs.public.importProperties') }}" aria-expanded="false">
                                            <span>Import Properties</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Excel Import Offplan Properties')
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" id="sidebarExcelImportJobs"
                                            href="{{ route('offplan.import.index') }}" aria-expanded="false">
                                            <span>Excel Import Properties</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['Quiz'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sideBarQuiz" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sideBarQuiz" id="sidebarQuizActive">
                            <i class="ri ri-award-fill"></i> <span>Quiz</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sideBarQuiz">
                            <ul class="nav nav-sm flex-column">
                                @can('Quiz Dashboard')
                                    <li class="nav-item">
                                        <a class="nav-link" id="sidebarDashboard"
                                           href="{{ route('admin.quiz.dashboard') }}" aria-expanded="false">
                                            <span>Quiz Dashboard</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Quiz List')
                                    <li class="nav-item">
                                        <a class="nav-link" id="sidebarQuizList"
                                           href="{{ route('admin.quiz.list') }}" aria-expanded="false">
                                            <span>Quizzes</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Quiz Level List')
                                    <li class="nav-item">
                                        <a class="nav-link" id="sidebarQuizLevel"
                                            href="{{ route('admin.quiz.level.index') }}" aria-expanded="false">
                                            <span>Quiz Level</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['Regions List', 'Communities List', 'Sub Communities List', 'Community Properties List'])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Managements</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarLocations" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarLocations" id="locationActive">
                            <i class="mdi mdi-map-marker-multiple"></i> <span>Locations
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarLocations">
                            <ul class="nav nav-sm flex-column">
                                @can('Regions List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.location.region.index') }}" class="nav-link"
                                            id="sidebarRegion"><span>Regions</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Communities List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.location.communities.index') }}" class="nav-link"
                                            id="sidebarCommunities"><span>Communities</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Sub Communities List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.location.sub-communities.index') }}" class="nav-link"
                                            id="sidebarSubCommunity"><span>Sub Communities</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Community Properties List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.location.community-properties.index') }}" class="nav-link"
                                            id="sidebarCommunityProp"><span>Community Properties</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['Users List', 'Users Tree View', 'Roles List', 'Permissions List'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarAuth" id="userActive">
                            <i class="mdi mdi-account-circle-outline"></i> <span>User Management
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarAuth">
                            <ul class="nav nav-sm flex-column">
                                @can('Users List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.user.users.index') }}" class="nav-link"
                                            id="sidbarUsers"><span>Users</span></a>
                                    </li>
                                @endcan
                                {{-- @can('Users Tree View')
                                    <li class="nav-item">
                                        <a href="{{route('admin.user.users.tree')}}" class="nav-link">Users Tree</a>
                                    </li>
                                @endcan --}}
                                @can('Roles List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.user.roles.index') }}" class="nav-link"
                                            id="sidebarRoles"><span>Roles</span></a>
                                    </li>
                                @endcan
                                @can('Permissions List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.user.permissions.index') }}" class="nav-link"
                                            id="sidebarPermission"><span>Permissions</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany([
                    'Property Amenities List',
                    'Property Categories List',
                    'Property Types List',
                    'Property
                    Statuses List',
                    'Beds List',
                    'Baths List',
                    ])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Parameters</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="propertyManagementSidebar" href="#sidebarParameters"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="sidebarParameters">
                            <i class="mdi mdi-home-group-plus"></i> <span>Property Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarParameters">
                            <ul class="nav nav-sm flex-column">
                                @can('Developers List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.property.developers') }}" class="nav-link"
                                            id="sidebarDevelopers"><span>Developers</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Projects List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.developer.meta.index') }}" class="nav-link"
                                            id="portalMetaIndex"><span>Projects</span></a>
                                    </li>
                                @endcan

                                @can('Property Amenities List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.property.feature') }}" class="nav-link"
                                            id="sidebarFeature"><span>Features/Amenities</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Property Categories List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.property.category') }}" class="nav-link"
                                            id="sidebarCategory"><span>Category</span>
                                        </a>
                                    </li>
                                @endcan
                                {{-- @can('Property Types List')
                                    <li class="nav-item">
                                        <a href="{{route('admin.property.type')}}" class="nav-link" id="paramPropertyType">Types
                                        </a>
                                    </li>
                                @endcan --}}
                                @can('Property Statuses List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.property.status') }}" class="nav-link"
                                            id="sidebarPropStatus"><span>Status</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Beds List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.property.bed') }}" class="nav-link"
                                            id="sidebarBeds"><span>Beds</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Baths List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.property.bath') }}" class="nav-link"
                                            id="sidebarBaths"><span>Bath</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['Commission List', 'Commission Slab List'])
                    <li class="nav-item d-none">
                        <a class="nav-link menu-link" href="#commission_dropdown" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="sidebarJobs" id="commissionActive">
                            <i class="mdi mdi-calendar"></i> <span>Commission
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="commission_dropdown">
                            <ul class="nav nav-sm flex-column">

                                @can('Commission Slab List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.work.commission.slab.index') }}" class="nav-link"
                                            id="commissionSlabSidebar"><span>Commission Slab</span></a>
                                    </li>
                                @endcan
                                @can('Deal Deductible List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.general.dealsDeductableType.index') }}" class="nav-link"
                                            id="dealsDeductableType"><span>Deals Fee Type</span></a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['Lead Statuses List', 'Lead Sub Statuses List'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="leadParameterSidebar" href="#leadParameterSidebarList"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="leadParameterSidebarList">
                            <i class="mdi mdi-hubspot"></i> <span>Lead Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="leadParameterSidebarList">
                            <ul class="nav nav-sm flex-column">
                                {{-- @can('Lead Sources List')
                                    <li class="nav-item">
                                        <a href="{{route('admin.lead.source')}}" class="nav-link" id="sidebarLeadSource">Sources
                                        </a>
                                    </li>
                                @endcan --}}
                                @can('Lead Statuses List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.lead.status') }}" class="nav-link"
                                            id="sidebarLeadStatus"><span>Status</span>
                                        </a>
                                    </li>
                                @endcan
                                {{-- @can('Lead Types List')
                                    <li class="nav-item">
                                        <a href="{{route('admin.lead.type')}}" class="nav-link" id="sidebarleadType">Types
                                        </a>
                                    </li>
                                @endcan --}}
                                @can('Lead Sub Statuses List')
                                    <li class="nav-item d-none">
                                        <a href="{{ route('admin.lead.sub.statuses') }}" class="nav-link"
                                            id="sidebarLeadSubStatus"><span>Lead Sub Status</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @can('Lead Statuses List')
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="listingleadParameterSidebar"
                            href="#listingleadParameterSidebarList" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="listingleadParameterSidebarList">
                            <i class="mdi mdi-hubspot"></i> <span>Listing Lead Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="listingleadParameterSidebarList">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ route('listings.leads.status') }}" class="nav-link"
                                        id="sidebarLeadStatus"><span>Status</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                @endcan
                @canany(['Contact Sources List', 'Contact Statuses List', 'Contact Types List'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="contactParameterSidebar" href="#contactParameterlayout"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="contactParameterlayout">
                            <i class="mdi mdi-contacts"></i> <span>Contact Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="contactParameterlayout">
                            <ul class="nav nav-sm flex-column">
                                {{-- @can('Contact Sources List')
                                    <li class="nav-item">
                                        <a href="{{route('admin.contact.source')}}" class="nav-link" id="sidebarContactSource">Sources
                                        </a>
                                    </li>
                                @endcan --}}
                                @can('Contact Statuses List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.contact.status.index') }}" class="nav-link"
                                            id="sidebarContactStatus"><span>Status</span>
                                        </a>
                                    </li>
                                @endcan
                                {{-- @can('Contact Types List')
                                    <li class="nav-item">
                                        <a href="{{route('admin.contact.types')}}" class="nav-link" id="sidebarCotnactTypes">Types
                                        </a>
                                    </li>
                                @endcan --}}
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany([
                    'Deal Types List',
                    'Transaction Types List',
                    'Deal Sources List',
                    'Deal Statuses List',
                    'Deal
                    Sub Statuses List',
                    ])
                    <li class="nav-item d-none">
                        <a class="nav-link menu-link" id="dealParameterSidebarLayout" href="#dealParameterSidebar"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="dealParameterSidebar">
                            <i class="mdi mdi-handshake"></i> <span>Deal Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="dealParameterSidebar">
                            <ul class="nav nav-sm flex-column">
                                @can('Deal Types List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.deals.types.index') }}" class="nav-link"
                                            id="sidebarDealType"><span>Deal Types</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Transaction Types List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.deals.transaction.index') }}" class="nav-link"
                                            id="sidebarDealTransType"><span>Transaction Types</span>
                                        </a>
                                    </li>
                                @endcan
                                {{-- @can('Deal Sources List')
                                    <li class="nav-item">
                                        <a href="{{route('admin.deals.sources.index')}}" class="nav-link" id="sidebarDealSource">Deal Sources
                                        </a>
                                    </li>
                                @endcan --}}
                                @can('Deal Statuses List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.deals.statuses.index') }}" class="nav-link"
                                            id="sidebarDealStatus"><span>Deal Status</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Deal Sub Statuses List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.deals.sub.statuses.index') }}" class="nav-link"
                                            id="sidebarDealSubStatus"><span>Deal Sub Status</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>

                @endcanany
                @canany(['Event Types List'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="eventParameterSidebarLayout" href="#eventParameterSidebar"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="eventParameterSidebar">
                            <i class="mdi mdi-calendar"></i> <span>Calendar Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="eventParameterSidebar">
                            <ul class="nav nav-sm flex-column">
                                @can('Event Types List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.events.type.index') }}" class="nav-link"
                                            id="eventTypeSideBar"><span>Calendar/Event Types</span>
                                        </a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>

                @endcanany
                @canany(['Sources List', 'Mediums List', 'Type List'])
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#contactParameterSources" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="contactParameterSources"
                            id="generalParemeters">
                            <i class="mdi  mdi-contacts"></i> <span>General Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="contactParameterSources">
                            <ul class="nav nav-sm flex-column">
                                @can('Sources List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.general.source.contacts') }}" class="nav-link"
                                            id="sidebarContactSourceContacts"><span>Sources</span>
                                        </a>
                                    </li>
                                @endcan

                                @can('Mediums List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.general.source.medium') }}" class="nav-link"
                                            id="sidebarContactMediumContacts"><span>Mediums</span></a>
                                    </li>
                                @endcan

                                @can('Type List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.general.type.all') }}" class="nav-link"
                                            id="sidebarContactTypes"><span>Types</span></a>
                                    </li>
                                @endcan

                            </ul>
                        </div>
                    </li>
                @endcanany

                <li class="menu-title"><i class="ri-more-fill"></i> <span>Notifications</span></li>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarNotifications" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarNotifications"
                        id="sidebarNotiActive">
                        <i class="bx bx-bell"></i> <span>Notifications
                        </span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarNotifications">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a href="{{ route('admin.message.inbox') }}" class="nav-link"
                                    id="sidebarNotificationInbox"><span>Notifications</span>
                                </a>
                            </li>

                            @can('App Logs List')
                                <li class="nav-item">
                                    <a href="{{ route('admin.app.log') }}" class="nav-link"
                                        id="sidebarAppLogs"><span>App Logs</span>
                                    </a>
                                </li>
                            @endcan

                        </ul>
                    </div>
                </li>

                @canany(['Email Settings List'])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Configuration</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarConfiguration" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="sidebarConfiguration" id="configActive">
                            <i class="mdi mdi-application-cog-outline"></i> <span>Configuration
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarConfiguration">
                            <ul class="nav nav-sm flex-column">
                                @can('Email Settings List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.configuration.email-settings.index') }}" class="nav-link"
                                            id="sidebarConfigEmail"><span>Email Settings</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Email Settings List')
                                    <li class="nav-item" id="configureNotificationSidebar">
                                        <a href="{{ route('admin.configuration.notification-settings.index') }}"
                                            class="nav-link" id="sidebarConfigEmail"><span>Notification Settings</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('Traccar Client Settings List')
                                    <li class="nav-item" id="configureTraccarClientSidebar">
                                        <a href="{{ route('admin.configuration.traccar.client_settings') }}" class="nav-link"
                                            id="sidebarConfigTraccarClient"><span>Spot Client Settings</span>
                                        </a>
                                    </li>
                                @endcan
                                @can('User Devices List')
                                    <li class="nav-item" id="userDevices">
                                        <a href="{{ route('admin.configuration.devices.index') }}" class="nav-link"
                                            id="userDevicesList"><span>User Devices List</span>
                                        </a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany


                @canany(['Portals List'])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Settings</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarSettings" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarLocations" id="portalSettingsActive">
                            <i class="mdi mdi-cog-outline"></i> <span>Settings</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSettings">
                            <ul class="nav nav-sm flex-column">
                                @can('Portals List')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.settings.portals.index') }}" class="nav-link"
                                            id="sidebarPortals"><span>Portals</span></a>
                                    </li>
                                @endcan
                                @can('General Settings')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.settings.general.index') }}" class="nav-link"
                                            id="sidebarGeneralSetting"><span>General Settings</span></a>
                                    </li>
                                @endcan
                                @can('Create group')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.settings.group.index') }}" class="nav-link"
                                            id="sidebarGroup"><span>Groups</span></a>
                                    </li>
                                @endcan
                                @can('Create group')
                                    <li class="nav-item d-none">
                                        <a href="{{ route('admin.settings.group.makeTree') }}" class="nav-link"
                                            id="sidebarTree"><span>Tree</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany
                @canany(['Security'])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Analysis</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarSecurity" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarSecurity" id="sidebarSecurityActive">
                            <i class="mdi mdi-lock-outline"></i> <span>Security Analysis</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSecurity">
                            <ul class="nav nav-sm flex-column">
                                @can('Security Analysis Dashboard')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.security.index') }}" class="nav-link"
                                            id="sidebarSecurityIndex"><span>Dashboard</span></a>
                                    </li>
                                @endcan
                                @can('Security Analysis Dashboard')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.traccar.recording.index') }}" class="nav-link"
                                            id="sidebarSecurityRecoding"><span>Spot Recordings</span></a>
                                    </li>
                                @endcan
                            </ul>
                        </div>
                    </li>
                @endcanany

                @canany(['Criteria'])
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Criteria Matching</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#criteriaMatching" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="criteriaMatching" id="criteriaMatchingActive">
                            <i class="mdi mdi-lock-outline"></i> <span>Criteria Matching</span>
                        </a>
                        <div class="collapse menu-dropdown" id="criteriaMatching">
                            <ul class="nav nav-sm flex-column">
                                @can('Security Analysis Dashboard')
                                    <li class="nav-item">
                                        <a href="{{ route('admin.criteria.matching.index') }}" class="nav-link"
                                            id="criteriaMatchingIndex"><span>Match</span></a>
                                    </li>
                                @endcan



                            </ul>
                        </div>
                    </li>
                @endcanany




            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<!-- <div class="vertical-overlay"></div> -->
