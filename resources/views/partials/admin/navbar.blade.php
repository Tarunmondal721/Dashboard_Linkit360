<div class="sidenav custom-sidenav" id="sidenav-main">
    <div class="sidenav-header d-flex align-items-center">
        <a class="navbar-brand" href="{{route('home')}}">
            <img src="{{asset('assets/images/logo.png')}}" alt="{{ config('app.name', 'LeadGo') }}" class="navbar-brand-img">
        </a>
        <div class="ml-auto">
            <div class="sidenav-toggler sidenav-toggler-dark d-md-none" data-action="sidenav-unpin"         data-target="#sidenav-main">
                <div class="sidenav-toggler-inner">
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                    <i class="sidenav-toggler-line bg-white"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="scrollbar-inner">
        <div class="div-mega">
            <ul class="navbar-nav navbar-nav-docs">
                @if((\Auth::user()->type != 'Owner'))
                <li class="nav-item" style="font-weight: 400;">{{ Auth::user()->org_name }}</li>
                @endif
                <li class="nav-item">
                    <a href="{{route('home')}}" class="nav-link {{ (Request::route()->getName() == 'home') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>{{__('Dashboard')}}
                    </a>
                </li>
                @if (\Auth::user()->can('Reports Management'))
                <!-- Report SECTION -->
                <li class="nav-item {{
                    (Request::route()->getName() == 'report.summary' ||
                    Request::route()->getName() == 'report.summary.daily.country' ||
                    Request::route()->getName() == 'report.summary.daily.manager' ||
                    Request::route()->getName() == 'report.summary.monthly.operator' ||
                    Request::route()->getName() == 'report.summary.monthly.country' ||
                    Request::route()->getName() == 'report.summary.monthly.manager' ||
                    Request::route()->getName() == 'report.summary.daily.business' ||
                    Request::route()->getName() == 'report.summary.monthly.business' ||
                    Request::route()->getName() == 'report.details' ||
                    Request::route()->getName() == 'report.service.details' ||
                    Request::route()->getName() == 'report.pnlsummary' ||
                    Request::route()->getName() == 'report.pnlsummary.daily.operator' ||
                    Request::route()->getName() == 'report.pnlsummary.monthly.operator'||
                    Request::route()->getName() == 'report.pnlsummary.daily.country' ||
                    Request::route()->getName() == 'report.pnlsummary.monthly.country' ||
                    Request::route()->getName() == 'report.pnlsummary.daily.company' ||
                    Request::route()->getName() == 'report.pnlsummary.monthly.company' ||
                    Request::route()->getName() == 'report.pnlsummary.daily.business' ||
                    Request::route()->getName() == 'report.pnlsummary.monthly.business' ||
                    Request::route()->getName() == 'report.adnetreport'||
                    Request::route()->getName() == 'roi.monitor.operator' ||
                    Request::route()->getName() == 'report.daily.business.pnldetails' ||
                    Request::route()->getName() == 'report.monthly.business.pnldetails'||
                    Request::route()->getName() == 'roi.monitor.country') ? 'active' : 'collapsed' }}">
                    @can('Reports Management')
                    <a class="nav-link collapsed" href="#navbar-getting-started-report" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'report.summary') ? 'true' : 'false' }}" aria-controls="navbar-getting-started-report">
                        <i class="fas fa-paste"></i>{{__('Report')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (
                        Request::route()->getName() == 'report.summary' ||
                        Request::route()->getName() == 'report.summary.daily.country' ||
                        Request::route()->getName() == 'report.summary.daily.manager' ||
                        Request::route()->getName() == 'report.summary.monthly.operator' ||
                        Request::route()->getName() == 'report.summary.monthly.country' ||
                        Request::route()->getName() == 'report.summary.monthly.manager' ||
                        Request::route()->getName() == 'report.summary.daily.business' ||
                        Request::route()->getName() == 'report.summary.monthly.business' ||
                        Request::route()->getName() == 'report.details' ||
                        Request::route()->getName() == 'report.service.details' ||
                        Request::route()->getName() == 'report.pnlsummary' ||
                        Request::route()->getName() == 'report.pnlsummary.daily.operator' ||
                        Request::route()->getName() == 'report.pnlsummary.monthly.operator'||
                        Request::route()->getName() == 'report.pnlsummary.daily.country' ||
                        Request::route()->getName() == 'report.pnlsummary.monthly.country' ||
                        Request::route()->getName() == 'report.pnlsummary.daily.company' ||
                        Request::route()->getName() == 'report.pnlsummary.monthly.company' ||
                        Request::route()->getName() == 'report.pnlsummary.daily.business' ||
                        Request::route()->getName() == 'report.pnlsummary.monthly.business' ||
                        Request::route()->getName() == 'report.daily.business.pnldetails' ||
                        Request::route()->getName() == 'report.monthly.business.pnldetails'||
                        Request::route()->getName() == 'roi.monitor.operator' ||
                        Request::route()->getName() == 'roi.monitor.country')  ? 'show' : '' }}" id="navbar-getting-started-report">
                        <ul class="nav flex-column submenu-ul">
                            @can('Report Summary')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.summary' || Request::route()->getName() == 'report.summary.daily.country' ||
                            Request::route()->getName() == 'report.summary.daily.manager' ||
                            Request::route()->getName() == 'report.summary.monthly.operator' ||
                            Request::route()->getName() == 'report.summary.monthly.country' ||
                            Request::route()->getName() == 'report.pnlsummary.daily.business' ||
                            Request::route()->getName() == 'report.summary.daily.business' ||
                            Request::route()->getName() == 'report.summary.monthly.business' ||
                            Request::route()->getName() == 'report.pnlsummary.monthly.business' ||
                            Request::route()->getName() == 'report.summary.monthly.manager') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('report.summary')}}">{{__('Report Summary')}}</a>
                            </li>
                            @endcan
                            @can('Reporting Details')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.details' || Request::route()->getName() == 'report.service.details') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('report.details')}}">{{__('Reporting Details')}}</a>
                            </li>
                            @endcan
                            @can('PNL Summary')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.pnlsummary' || Request::route()->getName() == 'report.pnlsummary.daily.operator' ||
                            Request::route()->getName() == 'report.pnlsummary.monthly.operator'||
                            Request::route()->getName() == 'report.pnlsummary.daily.country' ||
                            Request::route()->getName() == 'report.pnlsummary.monthly.country' ||
                            Request::route()->getName() == 'report.pnlsummary.daily.company' ||
                            Request::route()->getName() == 'report.pnlsummary.monthly.company') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('report.pnlsummary')}}">{{__('GP Summary')}}</a>
                            </li>
                            @endcan
                            @can('PNL Detail')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.daily.operator.pnldetails' ||
                            Request::route()->getName() == 'report.daily.business.pnldetails' ||
                            Request::route()->getName() == 'report.monthly.business.pnldetails') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('report.daily.operator.pnldetails')}}">{{__('GP Detail')}}</a>
                            </li>
                            @endcan
                            @can('Monitor ROI')
                                <li class="nav-item {{ Request::route()->getName() == 'roi.monitor.operator' || Request::route()->getName() == 'roi.monitor.country' ? 'active' : '' }}">
                                    <a class="nav-link" href="{{ route('roi.monitor.operator') }}">{{ __('Monitor ROI') }}</a>
                                </li>
                            @endcan

                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Analytic Management'))
                <!-- Analytic SECTION -->
                <li class="nav-item {{ ( Request::route()->getName() == 'analytic.revenueMonitoring' || Request::route()->getName() == 'analytic.logPerformance' || Request::route()->getName() == 'report.adnetreport' || Request::route()->getName() == 'analytic.adsMonitoring.business' || Request::route()->getName() == 'analytic.monitor.daily.operator' ||
                    Request::route()->getName() == 'analytic.monitor.daily.country' ||
                    Request::route()->getName() == 'analytic.monitor.monthly.operator'||
                    Request::route()->getName() == 'analytic.monitor.monthly.country') ? 'active' : 'collapsed' }}">
                    @can('Analytic Management')
                    <a class="nav-link collapsed" href="#navbar-getting-started-analytic" data-toggle="collapse" role="button" aria-expanded="{{ (Request::route()->getName() == 'analytic.adsMonitoring' || Request::route()->getName() == 'analytic.adsMonitoring.business' ||  Request::route()->getName() == 'analytic.monitor.daily.operator' ||
                    Request::route()->getName() == 'analytic.monitor.daily.country' ||
                    Request::route()->getName() == 'analytic.monitor.monthly.operator'||
                    Request::route()->getName() == 'analytic.monitor.monthly.country') ? 'true' : 'false' }}" aria-controls="navbar-getting-started-analytic">
                        <i class="fas fa-chart-bar"></i>{{__('Analytic')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ ( Request::route()->getName() == 'analytic.revenueMonitoring' || Request::route()->getName() == 'analytic.revenueAlert' || Request::route()->getName() == 'analytic.roi' || Request::route()->getName() == 'analytic.logPerformance' || Request::route()->getName() == 'report.adnetreport' )  ? 'show' : '' }}" id="navbar-getting-started-analytic">
                        <ul class="nav flex-column submenu-ul">
                            @can('Revenue Monitoring')
                            <li class="nav-item {{ (Request::route()->getName() == 'analytic.revenueMonitoring' || Request::route()->getName() == 'analytic.revenueMonitoringMonthly' || Request::route()->getName() == 'analytic.revenueMonitoring.countryMonthly' || Request::route()->getName() == 'analytic.revenueMonitoring.companyMonthly' || Request::route()->getName() == 'analytic.revenueMonitoring.business' || Request::route()->getName() == 'analytic.revenueMonitoring.businessMonthly') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('analytic.revenueMonitoring')}}">{{__('Revenue Monitoring')}}</a>
                            </li>
                            @endcan
                            @can('Ads Monitoring')
                            <li class="nav-item {{ (Request::route()->getName() == 'analytic.adsMonitoring.operator' ||Request::route()->getName() == 'analytic.adsMonitoring.business' ) ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('analytic.adsMonitoring.operator')}}">{{__('Ads Spending')}}</a>
                            </li>
                            @endcan
                            @can('Revenue Alert')
                            <li class="nav-item {{ (Request::route()->getName() == 'analytic.revenueAlert') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('analytic.revenueAlert')}}">{{__('Revenue Alert')}}</a>
                            </li>
                            @endcan
                            @can('ROI Report')
                            <li class="nav-item {{ (Request::route()->getName() == 'analytic.roi') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('analytic.roi')}}">{{__('ROI Report')}}</a>
                            </li>
                            @endcan
                            @can('Log Performance')
                            <li class="nav-item {{ (Request::route()->getName() == 'analytic.logPerformance') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('analytic.logPerformance')}}">{{__('Log Performance')}}</a>
                            </li>
                            @endcan
                            @can('Adnet Report')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.adnetreport') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('report.adnetreport')}}">{{__('Arpu Details')}}</a>
                            </li>
                            @endcan
                            @can('Monitor Operational')
                            <li class="nav-item {{ (Request::route()->getName() == 'analytic.monitor.daily.operator' ||
                                Request::route()->getName() == 'analytic.monitor.daily.country' ||
                                Request::route()->getName() == 'analytic.monitor.monthly.operator' ||
                                Request::route()->getName() == 'analytic.monitor.monthly.country') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('analytic.monitor.daily.operator') }}">{{ __('Monitor Operational') }}</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Management'))
                <!-- Management SECTION -->
                <li class="nav-item {{ (Request::route()->getName() == 'management.user' || Request::route()->getName() == 'management.revShare' || Request::route()->getName() == 'management.companyAssign'  || Request::route()->getName() == 'management.company' || Request::route()->getName() == 'management.currency' || Request::route()->getName() == 'management.operator' || Request::route()->getName() == 'users') ? 'active' : 'collapsed' }}">
                    @can('Management')
                    <a class="nav-link collapsed" href="#navbar-getting-started-management" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'management.user') ? 'true' : 'false' }}" aria-controls="navbar-getting-started-management">
                        <i class="fas fa-users"></i>{{__('Management')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (Request::route()->getName() == 'management.user' || Request::route()->getName() == 'management.revShare' || Request::route()->getName() == 'management.companyAssign' || Request::route()->getName() == 'management.company' || Request::route()->getName() == 'management.currency' || Request::route()->getName() == 'management.operator' || Request::route()->getName() == 'project.management' || Request::route()->getName() == 'users' )  ? 'show' : '' }}" id="navbar-getting-started-management">
                        <ul class="nav flex-column submenu-ul">
                            @can('Company Management')
                            <li class="nav-item {{ (Request::route()->getName() == 'management.company') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('management.company')}}">{{__('Company Management')}}</a>
                            </li>
                            @endcan
                            @can('Currency Management')
                            <li class="nav-item {{ (Request::route()->getName() == 'management.currency') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('management.currency')}}">{{__('Currency Management')}}</a>
                            </li>
                            @endcan
                            @can('Operator Management')
                            <li class="nav-item {{ (Request::route()->getName() == 'management.operator') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('management.operator')}}">{{__('Operator Management')}}</a>
                            </li>
                            @endcan
                            @if(Gate::check('Manage Users') || Gate::check('Manage Clients') || Gate::check('Manage Roles') || Gate::check('Manage Permissions'))
                            @if((\Auth::user()->type == 'Business Owner') || (\Auth::user()->type == 'Administrator') || (\Auth::user()->type == 'Owner'))
                            @can('Manage Users')
                            <li class="nav-item  {{ Request::route()->getName() == 'users' ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('users')}}">
                                    {{__(' Users Management')}}
                                </a>
                            </li>
                            @endcan
                            @endif
                            @endif
                            @can('Project Management')
                            <li class="nav-item {{ (Request::route()->getName() == 'project.management') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('project.management')}}">{{__('Project Management')}}</a>
                            </li>
                            @endcan
                            {{-- @can('PMO Statistic')
                            <li class="nav-item {{ (Request::route()->getName() == 'pmo.statistic') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('pmo.statistic')}}">{{__('PMO Statistic')}}</a>
                            </li>
                            @endcan --}}
                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Finance Management'))
                <!-- Finance SECTION -->
                <li class="nav-item {{ (Request::route()->getName() == 'finance.revenueReconcile' || Request::route()->getName() == 'finance.targetRevenue' || Request::route()->getName() == 'finance.financeCostReport' || Request::route()->getName() == 'finance.targetRevenue.company') ? 'active' : 'collapsed' }}">
                    @can('Finance Management')
                    <a class="nav-link collapsed" href="#navbar-getting-started-finance" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'finance.revenueReconcile') ? 'true' : 'false' }}" aria-controls="navbar-getting-started-finance">
                        <i class="far fa-money-bill-alt"></i>{{__('Finance')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (Request::route()->getName() == 'finance.revenueReconcile' || Request::route()->getName() == 'finance.targetRevenue' || Request::route()->getName() == 'finance.financeCostReport' || Request::route()->getName() == 'finance.reconcialiation.daily.operator' || Request::route()->getName() == 'finance.reconcialiation.daily.country' || Request::route()->getName() == 'finance.reconcialiation.monthly.operator' || Request::route()->getName() == 'finance.reconcialiation.monthly.country')  ? 'show' : '' }}" id="navbar-getting-started-finance">
                        <ul class="nav flex-column submenu-ul">
                            @can('Revenue Reconcile')
                            <li class="nav-item {{ (Request::route()->getName() == 'finance.revenueReconcile') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('finance.revenueReconcile')}}">{{__('Revenue Reconcile')}}</a>
                            </li>
                            @endcan
                            @can('Target Revenue')
                            <li class="nav-item {{ (Request::route()->getName() == 'finance.targetRevenue' || Request::route()->getName() == 'finance.targetRevenue.company') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('finance.targetRevenue')}}">{{__('Target Revenue')}}</a>
                            </li>
                            @endcan
                            @can('Target Revenue')
                            <li class="nav-item {{ (Request::route()->getName() == 'finance.financeCostReport') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('finance.financeCostReport')}}">{{__('Finance Budget')}}</a>
                            </li>
                            @endcan
                            @can('Reconcialiation Media')
                            <li class="nav-item {{ (Request::route()->getName() == 'finance.reconcialiation.daily.operator' || Request::route()->getName() == 'finance.reconcialiation.daily.country' || Request::route()->getName() == 'finance.reconcialiation.monthly.operator' || Request::route()->getName() == 'finance.reconcialiation.monthly.country') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('finance.reconcialiation.daily.operator') }}">{{ __('Reconcialiation Media') }}</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Activity Log Management'))
                <!-- Activity Log SECTION -->
                <li class="nav-item {{ (Request::route()->getName() == 'activity.user' || Request::route()->getName() == 'activity.system' ) ? 'active' : 'collapsed' }}">
                    @can('Activity Log Management')
                    <a class="nav-link collapsed" href="#navbar-getting-started-activitylog" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'activity.system') ? 'true' : 'false' }}" aria-controls="navbar-getting-started-activitylog">
                        <i class="fas fa-book"></i>{{__('Activity Log')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (Request::route()->getName() == 'activity.user' || Request::route()->getName() == 'activity.system')  ? 'show' : '' }}" id="navbar-getting-started-activitylog">
                        <ul class="nav flex-column submenu-ul">
                            @can('User Activity')
                            <li class="nav-item {{ (Request::route()->getName() == 'activity.user') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('activity.user')}}">{{__('User Activity')}}</a>
                            </li>
                            @endcan
                            @can('System Activity')
                            <li class="nav-item {{ (Request::route()->getName() == 'activity.system') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('activity.system')}}">{{__('System Activity')}}</a>
                            </li>
                            @endcan
                            <li class="nav-item {{ (Request::route()->getName() == '
                                log-viewer::logs.list') ? 'active' : '' }}">
                                <a class="nav-link" href="{{ route('log-viewer::logs.list') }}">{{__('Log Viewer')}}</a>
                            </li>
                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Service Catalogue'))
                <!-- Create Service SECTION -->
                <li class="nav-item {{ (Request::route()->getName() == 'report.create' || Request::route()->getName() == 'report.list' ) ? 'active' : 'collapsed' }}">
                    @can('Service Catalogue')
                    <a class="nav-link collapsed" href="#navbar-getting-report-create" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'report.create') ? 'true' : 'false' }}" aria-controls="navbar-getting-report-create">
                        <i class="fas fa-book"></i>{{__('Service  Catalogue')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (Request::route()->getName() == 'report.create' || Request::route()->getName() == 'report.list')  ? 'show' : '' }}" id="navbar-getting-report-create">
                        <ul class="nav flex-column submenu-ul">
                            @can('Add New Service')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.create') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('report.create')}}">{{__('Add New Service')}}</a>
                            </li>
                            @endcan
                            @can('Service List')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.list') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('report.list')}}">{{__('Service List')}}</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Product Management'))
                <!-- Create Product SECTION -->
                <li class="nav-item {{ (Request::route()->getName() == 'report.product.index' || Request::route()->getName() == 'report.product.list' ) ? 'active' : 'collapsed' }}">
                    @can('Product Management')
                    <a class="nav-link collapsed" href="#navbar-getting-Product-create" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'report.product.index') ? 'true' : 'false' }}" aria-controls="navbar-getting-Product-create">
                        <i class="fas fa-book"></i>{{__('Product  Management')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (Request::route()->getName() == 'report.product.index' || Request::route()->getName() == 'report.product.list')  ? 'show' : '' }}" id="navbar-getting-Product-create">
                        <ul class="nav flex-column submenu-ul">
                            @can('Add New Product')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.product.index') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('report.product.index')}}">{{__('Add New Product')}}</a>
                            </li>
                            @endcan
                            @can('Product List')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.product.list') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('report.product.list')}}">{{__('Product List')}}</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Service Catalogue'))
                <!-- Log File SECTION -->
                <li class="nav-item {{ (Request::route()->getName() == 'management.pivot' || Request::route()->getName() == 'report.pivot.summary' ) ? 'active' : 'collapsed' }}">
                    @can('Service Catalogue')
                    <a class="nav-link collapsed" href="#navbar-getting-pivot-report-create" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'management.pivot') ? 'true' : 'false' }}" aria-controls="navbar-getting-pivot-report-create">
                        <i class="fas fa-book"></i>{{__('Pivot Management')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (Request::route()->getName() == 'management.pivot' || Request::route()->getName() == 'report.pivot.summary')  ? 'show' : '' }}" id="navbar-getting-pivot-report-create">
                        <ul class="nav flex-column submenu-ul">
                            @can('Add New Service')
                            <li class="nav-item {{ (Request::route()->getName() == 'management.pivot') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('management.pivot')}}">{{__('Add Pivot Filter')}}</a>
                            </li>
                            @endcan
                            @can('Service List')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.pivot.summary') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('report.pivot.summary')}}">{{__('Pivot Summary')}}</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Tools Management'))
                <!-- Tools SECTION -->
                <li class="nav-item {{ (Request::route()->getName() == 'report.tools.show'  ) ? 'active' : 'collapsed' }}">
                    @can('Tools Management')
                    <a class="nav-link collapsed" href="#navbar-getting-notification" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'report.tools.show') ? 'true' : 'false' }}" aria-controls="navbar-getting-notification">
                        <i class="fas fa-book"></i>{{__('Tools Management')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (Request::route()->getName() == 'report.tools.show' )  ? 'show' : '' }}" id="navbar-getting-notification">
                        <ul class="nav flex-column submenu-ul">
                            @can('Tools Show')
                            <li class="nav-item {{ (Request::route()->getName() == 'report.tools.show') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('report.tools.show')}}">{{__('Cs Tools')}}</a>
                            </li>
                            @endcan
                        </ul>
                    </div>
                </li>
                @endif
                @if (\Auth::user()->can('Log File Management'))
                <li class="nav-item {{ (Request::route()->getName() == 'logfile.pageLoad' || Request::route()->getName() == 'logfile.dataUpdate' || Request::route()->getName() == 'logfile.query' || Request::route()->getName() == 'logfile.currencyExchange') ? 'active' : 'collapsed' }}">
                    @can('Log File Management')
                    <a class="nav-link collapsed" href="#navbar-getting-started-logfile" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'logfile.dataUpdate') ? 'true' : 'false' }}" aria-controls="navbar-getting-started-logfile">
                        <i class="fas fa-server"></i>{{__('Log File')}}
                        <i class="fas fa-sort-up"></i>
                    </a>
                    @endcan
                    <div class="collapse {{ (Request::route()->getName() == 'logfile.cron' || Request::route()->getName() == 'logfile.pageLoad' || Request::route()->getName() == 'logfile.dataUpdate' || Request::route()->getName() == 'logfile.query' || Request::route()->getName() == 'logfile.currencyExchange') ? 'show' : '' }}" id="navbar-getting-started-logfile">
                        <ul class="nav flex-column submenu-ul">
                            @can('Cron Log')
                            <li class="nav-item {{ (Request::route()->getName() == 'logfile.cron') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('logfile.cron')}}">{{__('Cron Log')}}</a>
                            </li>
                            @endcan
                            {{-- <li class="nav-item {{ (Request::route()->getName() == 'logfile.pageLoad') ? 'active' : '' }}">
                                <a class="nav-link  " href="{{route('logfile.pageLoad')}}">{{__('Page Load Logs')}}</a>
                            </li>
                            <li class="nav-item {{ (Request::route()->getName() == 'logfile.dataUpdate') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('logfile.dataUpdate')}}">{{__('Data Update Logs')}}</a>
                            </li>
                            <li class="nav-item {{ (Request::route()->getName() == 'logfile.query') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('logfile.query')}}">{{__('Query Log')}}</a>
                            </li>
                            <li class="nav-item {{ (Request::route()->getName() == 'logfile.currencyExchange') ? 'active' : '' }}">
                                <a class="nav-link" href="{{route('logfile.currencyExchange')}}">{{__('Currency Exchange Logs')}}</a>
                            </li> --}}
                        </ul>
                    </div>
                </li>
                @endif
                @if(Gate::check('Manage Users') || Gate::check('Manage Clients') || Gate::check('Manage Roles') || Gate::check('Manage Permissions'))
                    {{-- @if((\Auth::user()->type == 'Owner') || (\Auth::user()->type == 'Administrator'))
                        @can('Manage Users')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{route('users')}}">
                                <i class="fas fa-users"></i>{{__('Users')}}
                            </a>
                        </li>
                        @endcan
                    @endif --}}
                    @if((\Auth::user()->type == 'Owner'))
                        @can('Manage Roles')
                        <li class="nav-item">
                            <a class="nav-link {{ (Request::route()->getName() == 'roles.index') ? 'active' : '' }}" href="{{route('roles.index')}}">
                                <i class="fas fa-user-cog"></i>{{__('Roles')}}
                            </a>
                        </li>
                        @endcan
                    @endif
                    @if((\Auth::user()->type == 'Owner') || (\Auth::user()->type == 'Admin'))
                        <li class="nav-item {{ (Request::route()->getName() == 'notification.report.create' || Request::route()->getName() == 'notification.report.detail.incident' || Request::route()->getName() == 'notification.report.list' ) ? 'active' : 'collapsed' }}">
                            @can('Notification Management')
                            <a class="nav-link collapsed"
                                href="#navbar-getting-tools" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'notification.report.create') ? 'true' : 'false' }}" aria-controls="navbar-getting-tools">
                                <i class="fas fa-bell"></i>{{__('Notification Management')}}
                                <i class="fas fa-sort-up"></i>
                            </a>
                            @endcan
                            <div class="collapse {{ (Request::route()->getName() == 'notification.report.index' || Request::route()->getName() == 'notification.report.create' )  ? 'show' : '' }}" id="navbar-getting-tools">
                                <ul class="nav flex-column submenu-ul">
                                    @can('Tools Show')
                                    <li class="nav-item {{ (Request::route()->getName() == 'notification.report.create') ? 'active' : '' }}">
                                        <a class="nav-link  " href="{{route('notification.report.create')}}">{{__('Create Notification')}}</a>
                                    </li>
                                    <li class="nav-item {{ (Request::route()->getName() == 'notification.report.index' || Request::route()->getName() == 'notification.report.detail.incident') ? 'active' : '' }}">
                                        <a class="nav-link  " href="{{route('notification.report.index')}}">{{__('Notification Show')}}</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item {{ (Request::route()->getName() == 'arpu.summary' ) ? 'active' : 'collapsed' }}">
                            @can('Summary Management')
                            <a class="nav-link collapsed"
                                href="#navbar-getting-arpu-summary" data-toggle="collapse" role="button" aria-expanded="{ (Request::route()->getName() == 'arpu.summary') ? 'true' : 'false' }}" aria-controls="navbar-getting-arpu-summary">
                                <i class="fas fa-server"></i>{{__('Arpu Logs')}}
                                <i class="fas fa-sort-up"></i>
                            </a>
                            @endcan
                            <div class="collapse {{ (Request::route()->getName() == 'notification.report.index' || Request::route()->getName() == 'arpu.summary' )  ? 'show' : '' }}" id="navbar-getting-arpu-summary">
                                <ul class="nav flex-column submenu-ul">
                                    @can('Summary Arpu')
                                    <li class="nav-item {{ (Request::route()->getName() == 'arpu.summary') ? 'active' : '' }}">
                                        <a class="nav-link  " href="{{route('arpu.summary')}}">{{__('Summary Arpu')}}</a>
                                    </li>
                                    @endcan
                                </ul>
                            </div>
                        </li>

                        @can('System Settings')
                        <li class="nav-item">
                            <a class="nav-link {{ (Request::route()->getName() == 'settings') ? 'active' : '' }}" href="{{route('settings')}}">
                                <i class="fas fa-cogs"></i>{{__('System Settings')}}
                            </a>
                        </li>
                        @endcan
                    @endif
                @endif
                <li class="nav-item">
                    <a href="{{ route('logout') }}" class="nav-link dropdown-item" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>{{__('Logout')}}</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>
