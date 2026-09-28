<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu <?php echo e(env('APP_ENV') == 'local' ? 'localEnv' : ''); ?>">
    
    
    
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('assets/images/logo/logo.png')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('assets/images/logo/logo.png')); ?>" alt="" height="50">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="<?php echo e(URL::asset('assets/images/logo/logo.png')); ?>" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="<?php echo e(URL::asset('assets/images/logo/logo.png')); ?>" alt="" height="50">
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
                <li class="menu-title"><span><?php echo app('translator')->get('translation.menu'); ?>
                    </span></li>
                <li class="nav-item rm-disabled"  >
                    <a class="nav-link menu-link rm-disabled"

                       href="/" aria-expanded="false" id="DashActive">
                        <i class="mdi mdi-speedometer"></i> <span><?php echo app('translator')->get('translation.dashboards'); ?>
                        </span>
                    </a>
                </li>
                
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Team KPI Reports')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="kpiReportSidebar" href="<?php echo e(route('reports.kpi-v3.dashboard')); ?>"
                           aria-expanded="false">
                            <i class="mdi mdi-file-outline"></i> <span>KPI Reports</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Properties List')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="sidebarListings" href="<?php echo e(route('admin.listing.index')); ?>"
                            aria-expanded="false">
                            <i class="mdi mdi-home"></i> <span>Listing</span>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Call Logs List')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link d-none" id="callLog" href="<?php echo e(route('admin.calls.index')); ?>"
                            aria-expanded="false">
                            <i class="mdi mdi-cellphone"></i> <span>Call Logs</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Rents List', 'Sales List'])): ?>
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Leads List')): ?>
                    <li class="nav-item rm-disabled">
                        <a class="nav-link menu-link rm-disabled" id="sidebarLeads"
                            href="<?php echo e(route('admin.work.leads.index')); ?>" aria-expanded="false">
                            <i class="mdi mdi-account-cash"></i> <span>Leads</span>
                        </a>
                    </li>
                <?php endif; ?>







                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contacts List')): ?>

                    <?php if((auth()->user()->region_id != 2 && auth()->user()->parent_id != 699) || !auth()->user()->hasRole('Agent')): ?>
                        <li class="nav-item">
                            <a class="nav-link menu-link" id="sidebarContacts"
                                href="<?php echo e(route('admin.work.contacts.index')); ?>" aria-expanded="false">
                                <i class="mdi mdi-contacts"></i> <span>Contacts</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php if(auth()->user()->parent_id != 32 && auth()->user()->id != 32): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Leads List')): ?>
                        <li class="nav-item">
                            <a class="nav-link menu-link" id="listing-leads" href="<?php echo e(route('listings.leads.index')); ?>"
                                aria-expanded="false">
                                <i class="mdi mdi-home-edit"></i> <span>Listing Leads</span>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Pipeline List')): ?>
                        <li class="nav-item">
                            <a class="nav-link menu-link" id="sidebarPipelines" href="<?php echo e(route('admin.pipeline.index')); ?>"
                                aria-expanded="false">
                                <i class="ri-git-merge-line"></i> <span>Pipelines</span>
                            </a>
                        </li>
                    <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Ax Calculator')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="axCalculator" href="<?php echo e(route('admin.ax.calculator.index')); ?>"
                            aria-expanded="false">
                            <i class="mdi mdi-calculator-variant-outline"></i> <span>Deal Calculator</span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Deals List'])): ?>
                <li class="nav-item d-none">
                    <a class="nav-link menu-link" href="#sidebarDealBlock" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarDealBlock" id="dealSidebarBlock">
                        <i class="mdi mdi-cash-check"></i> <span>Deals</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDealBlock">
                        <ul class="nav nav-sm flex-column">
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Deals Dashboard')): ?>
                            <li class="nav-item">
                                <a href="<?php echo e(route('admin.work.deals.dashboard')); ?>" class="nav-link"
                                    id="dealDashboardSidebar"><span>Dashboard</span></a>
                            </li>
                        <?php endif; ?>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Deals List')): ?>
                            <li class="nav-item">
                                <a class="nav-link" id="sidebarDeals" href="<?php echo e(route('admin.work.deals.index')); ?>"
                                    aria-expanded="false">
                                    <span>List</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        </ul>
                    </div>
                </li>
            <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Events List'])): ?>
                    <li class="nav-item">
                        <a href="<?php echo e(route('admin.events.index')); ?>" class="nav-link" id="eventSidebar"><i
                                class="mdi mdi-calendar"></i> <span>Calendar</span></a>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Documents Library')): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="documentLibrary" href="<?php echo e(route('admin.documents.index')); ?>"
                            aria-expanded="false">
                            <i class="mdi mdi-folder-outline"></i> <span>Document Library</span>
                        </a>
                    </li>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Jobs List', 'Property Shoot List'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarJobs" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarJobs" id="jobActive">
                            <i class="mdi mdi-calendar"></i> <span>Jobs</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarJobs">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Jobs List')): ?>
                                    <li class="nav-item d-none">
                                        <a href="<?php echo e(route('admin.jobs.index')); ?>" class="nav-link" id="jobSidebar">Jobs</a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Property Shoot List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.jobs.shoot.index')); ?>" class="nav-link"
                                            id="shootJobSidebar"><span>Property Shoot</span></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Agent Reports', 'Agent Calender Reports'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarReports" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarReports" id="reportsActive">
                            <i class="mdi mdi-file"></i> <span>Reports
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarReports">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Team KPI Reports')): ?>
                                    <li class="nav-item d-none">
                                        <a href="<?php echo e(route('reports.team.kpi-v2.team.dashboard')); ?>" class="nav-link"
                                           id="sidebarTeamKpiV2Report"><span>Team KPI Reports</span></a>
                                    </li>
                                <?php endif; ?>

                                <?php if(auth()->user()->hasRole(["Agent"])): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('reports.group.kpi-v2.view', auth()->id())); ?>" class="nav-link"
                                           id="sidebarAgentKpiV2Report"><span>Agent KPI Dashboard</span></a>
                                    </li>
                                <?php endif; ?>

                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Agent Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.reports.agent')); ?>" class="nav-link"
                                            id="sidebarAgentReports"><span>Agent Reports</span></a>
                                    </li>
                                <?php endif; ?>
                                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Agent Consolidate Reports')): ?>
                                        <li class="nav-item">
                                            <a href="<?php echo e(route('admin.reports.agent.consolidate.report')); ?>" class="nav-link"
                                               id="sidebarAgentReports"><span>Agent Consolidate Reports</span></a>
                                        </li>
                                    <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Agent Calender Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.reports.calendar')); ?>" class="nav-link"
                                            id="sidebarAgentReports"><span>Calendar Reports</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Group Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.reports.group')); ?>" class="nav-link"
                                            id="sidebarGroupReports"><span>Group Reports</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Group Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('reports.group.v4.index')); ?>" class="nav-link"
                                            id="sidebarGroupReports"><span>Team Reports</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Primary Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('rootPrimary')); ?>" class="nav-link"
                                            id="sidebarGroupReports"><span>Primary Report</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Commercial Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('rootPrimary')); ?>" class="nav-link"
                                            id="sidebarGroupReports"><span>Commercial Report</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Request Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('report.requests.index')); ?>" class="nav-link"
                                            id="sidebarRequestReports"><span>Request Report</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Request Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.reports.listings')); ?>" class="nav-link"
                                            id="sidebarReraReport"><span>Listing Rera Report</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Bulk SMS')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('contacts.bulk.sms.index')); ?>" class="nav-link"
                                            id="sidebarReraReport"><span>Bulk Sms</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('SMS Campaign')): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('contacts.sms.campaigns.index')); ?>" class="nav-link"
                                        id="sidebarReraReport"><span>SMS Campaign</span></a>
                                </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Group Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('reports.group.v5.index')); ?>" class="nav-link"
                                           id="sidebarGroupV5Reports"><span>Group V5 Reports</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Agent Insights Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('reports.agent-insights.index')); ?>" class="nav-link"
                                           id="sidebarAgentInsightsReports"><span>Agent Insights</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead Insights Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('reports.lead-insights.index')); ?>" class="nav-link"
                                           id="sidebarLeadInsightsReports"><span>Lead Insights</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Listing Insights Reports')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('reports.listing-insights.index')); ?>" class="nav-link"
                                           id="sidebarListingInsightsReports"><span>Listing Insights</span></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Execute Fetch Jobs', 'Import Offplan Properties'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarOffplan" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarOffplan" id="jobActive">
                            <i class="bx bx-import"></i> <span>Offplan</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarOffplan">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Execute Fetch Jobs')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" id="sidebarStartJobs"
                                            href="<?php echo e(route('jobs.public.startJobs')); ?>" aria-expanded="false">
                                            <span>Execute Jobs</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Import Offplan Properties')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" id="sidebarImportJobs"
                                            href="<?php echo e(route('jobs.public.importProperties')); ?>" aria-expanded="false">
                                            <span>Import Properties</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Excel Import Offplan Properties')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" id="sidebarExcelImportJobs"
                                            href="<?php echo e(route('offplan.import.index')); ?>" aria-expanded="false">
                                            <span>Excel Import Properties</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Quiz'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sideBarQuiz" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sideBarQuiz" id="sidebarQuizActive">
                            <i class="ri ri-award-fill"></i> <span>Quiz</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sideBarQuiz">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Dashboard')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="sidebarDashboard"
                                           href="<?php echo e(route('admin.quiz.dashboard')); ?>" aria-expanded="false">
                                            <span>Quiz Dashboard</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz List')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="sidebarQuizList"
                                           href="<?php echo e(route('admin.quiz.list')); ?>" aria-expanded="false">
                                            <span>Quizzes</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Quiz Level List')): ?>
                                    <li class="nav-item">
                                        <a class="nav-link" id="sidebarQuizLevel"
                                            href="<?php echo e(route('admin.quiz.level.index')); ?>" aria-expanded="false">
                                            <span>Quiz Level</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Regions List', 'Communities List', 'Sub Communities List', 'Community Properties List'])): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Managements</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarLocations" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarLocations" id="locationActive">
                            <i class="mdi mdi-map-marker-multiple"></i> <span>Locations
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarLocations">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Regions List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.location.region.index')); ?>" class="nav-link"
                                            id="sidebarRegion"><span>Regions</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Communities List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.location.communities.index')); ?>" class="nav-link"
                                            id="sidebarCommunities"><span>Communities</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Sub Communities List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.location.sub-communities.index')); ?>" class="nav-link"
                                            id="sidebarSubCommunity"><span>Sub Communities</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Community Properties List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.location.community-properties.index')); ?>" class="nav-link"
                                            id="sidebarCommunityProp"><span>Community Properties</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Users List', 'Users Tree View', 'Roles List', 'Permissions List'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarAuth" id="userActive">
                            <i class="mdi mdi-account-circle-outline"></i> <span>User Management
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarAuth">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Users List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.user.users.index')); ?>" class="nav-link"
                                            id="sidbarUsers"><span>Users</span></a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Roles List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.user.roles.index')); ?>" class="nav-link"
                                            id="sidebarRoles"><span>Roles</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Permissions List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.user.permissions.index')); ?>" class="nav-link"
                                            id="sidebarPermission"><span>Permissions</span></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
                    'Property Amenities List',
                    'Property Categories List',
                    'Property Types List',
                    'Property
                    Statuses List',
                    'Beds List',
                    'Baths List',
                    ])): ?>
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
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Developers List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.property.developers')); ?>" class="nav-link"
                                            id="sidebarDevelopers"><span>Developers</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Projects List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.developer.meta.index')); ?>" class="nav-link"
                                            id="portalMetaIndex"><span>Projects</span></a>
                                    </li>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Property Amenities List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.property.feature')); ?>" class="nav-link"
                                            id="sidebarFeature"><span>Features/Amenities</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Property Categories List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.property.category')); ?>" class="nav-link"
                                            id="sidebarCategory"><span>Category</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Property Statuses List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.property.status')); ?>" class="nav-link"
                                            id="sidebarPropStatus"><span>Status</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Beds List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.property.bed')); ?>" class="nav-link"
                                            id="sidebarBeds"><span>Beds</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Baths List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.property.bath')); ?>" class="nav-link"
                                            id="sidebarBaths"><span>Bath</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Commission List', 'Commission Slab List'])): ?>
                    <li class="nav-item d-none">
                        <a class="nav-link menu-link" href="#commission_dropdown" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="sidebarJobs" id="commissionActive">
                            <i class="mdi mdi-calendar"></i> <span>Commission
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="commission_dropdown">
                            <ul class="nav nav-sm flex-column">

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Commission Slab List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.work.commission.slab.index')); ?>" class="nav-link"
                                            id="commissionSlabSidebar"><span>Commission Slab</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Deal Deductible List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.general.dealsDeductableType.index')); ?>" class="nav-link"
                                            id="dealsDeductableType"><span>Deals Fee Type</span></a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Lead Statuses List', 'Lead Sub Statuses List'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="leadParameterSidebar" href="#leadParameterSidebarList"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="leadParameterSidebarList">
                            <i class="mdi mdi-hubspot"></i> <span>Lead Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="leadParameterSidebarList">
                            <ul class="nav nav-sm flex-column">
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead Statuses List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.lead.status')); ?>" class="nav-link"
                                            id="sidebarLeadStatus"><span>Status</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead Sub Statuses List')): ?>
                                    <li class="nav-item d-none">
                                        <a href="<?php echo e(route('admin.lead.sub.statuses')); ?>" class="nav-link"
                                            id="sidebarLeadSubStatus"><span>Lead Sub Status</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Lead Statuses List')): ?>
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
                                    <a href="<?php echo e(route('listings.leads.status')); ?>" class="nav-link"
                                        id="sidebarLeadStatus"><span>Status</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Contact Sources List', 'Contact Statuses List', 'Contact Types List'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="contactParameterSidebar" href="#contactParameterlayout"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="contactParameterlayout">
                            <i class="mdi mdi-contacts"></i> <span>Contact Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="contactParameterlayout">
                            <ul class="nav nav-sm flex-column">
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Contact Statuses List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.contact.status.index')); ?>" class="nav-link"
                                            id="sidebarContactStatus"><span>Status</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any([
                    'Deal Types List',
                    'Transaction Types List',
                    'Deal Sources List',
                    'Deal Statuses List',
                    'Deal
                    Sub Statuses List',
                    ])): ?>
                    <li class="nav-item d-none">
                        <a class="nav-link menu-link" id="dealParameterSidebarLayout" href="#dealParameterSidebar"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="dealParameterSidebar">
                            <i class="mdi mdi-handshake"></i> <span>Deal Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="dealParameterSidebar">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Deal Types List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.deals.types.index')); ?>" class="nav-link"
                                            id="sidebarDealType"><span>Deal Types</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Transaction Types List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.deals.transaction.index')); ?>" class="nav-link"
                                            id="sidebarDealTransType"><span>Transaction Types</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Deal Statuses List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.deals.statuses.index')); ?>" class="nav-link"
                                            id="sidebarDealStatus"><span>Deal Status</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Deal Sub Statuses List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.deals.sub.statuses.index')); ?>" class="nav-link"
                                            id="sidebarDealSubStatus"><span>Deal Sub Status</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </li>

                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Event Types List'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" id="eventParameterSidebarLayout" href="#eventParameterSidebar"
                            data-bs-toggle="collapse" role="button" aria-expanded="false"
                            aria-controls="eventParameterSidebar">
                            <i class="mdi mdi-calendar"></i> <span>Calendar Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="eventParameterSidebar">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Event Types List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.events.type.index')); ?>" class="nav-link"
                                            id="eventTypeSideBar"><span>Calendar/Event Types</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </li>

                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Sources List', 'Mediums List', 'Type List'])): ?>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#contactParameterSources" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="contactParameterSources"
                            id="generalParemeters">
                            <i class="mdi  mdi-contacts"></i> <span>General Parameters
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="contactParameterSources">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Sources List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.general.source.contacts')); ?>" class="nav-link"
                                            id="sidebarContactSourceContacts"><span>Sources</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Mediums List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.general.source.medium')); ?>" class="nav-link"
                                            id="sidebarContactMediumContacts"><span>Mediums</span></a>
                                    </li>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Type List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.general.type.all')); ?>" class="nav-link"
                                            id="sidebarContactTypes"><span>Types</span></a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

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
                                <a href="<?php echo e(route('admin.message.inbox')); ?>" class="nav-link"
                                    id="sidebarNotificationInbox"><span>Notifications</span>
                                </a>
                            </li>

                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('App Logs List')): ?>
                                <li class="nav-item">
                                    <a href="<?php echo e(route('admin.app.log')); ?>" class="nav-link"
                                        id="sidebarAppLogs"><span>App Logs</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </div>
                </li>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Email Settings List'])): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Configuration</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarConfiguration" data-bs-toggle="collapse"
                            role="button" aria-expanded="false" aria-controls="sidebarConfiguration" id="configActive">
                            <i class="mdi mdi-application-cog-outline"></i> <span>Configuration
                            </span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarConfiguration">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Email Settings List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.configuration.email-settings.index')); ?>" class="nav-link"
                                            id="sidebarConfigEmail"><span>Email Settings</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Email Settings List')): ?>
                                    <li class="nav-item" id="configureNotificationSidebar">
                                        <a href="<?php echo e(route('admin.configuration.notification-settings.index')); ?>"
                                            class="nav-link" id="sidebarConfigEmail"><span>Notification Settings</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Traccar Client Settings List')): ?>
                                    <li class="nav-item" id="configureTraccarClientSidebar">
                                        <a href="<?php echo e(route('admin.configuration.traccar.client_settings')); ?>" class="nav-link"
                                            id="sidebarConfigTraccarClient"><span>Spot Client Settings</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('User Devices List')): ?>
                                    <li class="nav-item" id="userDevices">
                                        <a href="<?php echo e(route('admin.configuration.devices.index')); ?>" class="nav-link"
                                            id="userDevicesList"><span>User Devices List</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>


                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Portals List'])): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Settings</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarSettings" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarLocations" id="portalSettingsActive">
                            <i class="mdi mdi-cog-outline"></i> <span>Settings</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSettings">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Portals List')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.settings.portals.index')); ?>" class="nav-link"
                                            id="sidebarPortals"><span>Portals</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('General Settings')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.settings.general.index')); ?>" class="nav-link"
                                            id="sidebarGeneralSetting"><span>General Settings</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create group')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.settings.group.index')); ?>" class="nav-link"
                                            id="sidebarGroup"><span>Groups</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Create group')): ?>
                                    <li class="nav-item d-none">
                                        <a href="<?php echo e(route('admin.settings.group.makeTree')); ?>" class="nav-link"
                                            id="sidebarTree"><span>Tree</span></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Security'])): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Analysis</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarSecurity" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="sidebarSecurity" id="sidebarSecurityActive">
                            <i class="mdi mdi-lock-outline"></i> <span>Security Analysis</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSecurity">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Security Analysis Dashboard')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.security.index')); ?>" class="nav-link"
                                            id="sidebarSecurityIndex"><span>Dashboard</span></a>
                                    </li>
                                <?php endif; ?>
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Security Analysis Dashboard')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.traccar.recording.index')); ?>" class="nav-link"
                                            id="sidebarSecurityRecoding"><span>Spot Recordings</span></a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['Criteria'])): ?>
                    <li class="menu-title"><i class="ri-more-fill"></i> <span>Criteria Matching</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#criteriaMatching" data-bs-toggle="collapse" role="button"
                            aria-expanded="false" aria-controls="criteriaMatching" id="criteriaMatchingActive">
                            <i class="mdi mdi-lock-outline"></i> <span>Criteria Matching</span>
                        </a>
                        <div class="collapse menu-dropdown" id="criteriaMatching">
                            <ul class="nav nav-sm flex-column">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('Security Analysis Dashboard')): ?>
                                    <li class="nav-item">
                                        <a href="<?php echo e(route('admin.criteria.matching.index')); ?>" class="nav-link"
                                            id="criteriaMatchingIndex"><span>Match</span></a>
                                    </li>
                                <?php endif; ?>



                            </ul>
                        </div>
                    </li>
                <?php endif; ?>




            </ul>
        </div>
    </div>
    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<!-- <div class="vertical-overlay"></div> -->
<?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/ax_secondary/resources/views/admin/layouts/sidebar.blade.php ENDPATH**/ ?>