@extends('admin.layouts.master')

@section('title')
   KPI Dashboard
@endsection

@section('content')

    <div id="kpi" class="v-application"></div>

@endsection

@section('script')
    <!-- Load Vue App -->
    <script>
        const team_refresh_url = "{{route('reports.group.kpi-v2.kpi.team.refresh.report') }}?month=";
        const agent_refresh_url = "{{ route('reports.group.kpi-v2.kpi.agent.refresh.report') }}?month=";
        const team_kpi_setup_url =  "{{ route('reports.team.kpi-v2.team.setup') }}";
        const agent_kpi_setup_url =  "{{ route('reports.group.kpi-v2.setup') }}";
        $("#kpiReportSidebar").addClass('active')
    </script>
    <script src="{{ asset('vue/js/kpi.js') }}"></script>
@endsection
