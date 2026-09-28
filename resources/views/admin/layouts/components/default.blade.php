{{-- @if ($properties->count() > 0) --}}
    @include('admin.layouts.components.property')

{{-- @endif

@if ($leads->count() > 0) --}}
    @include('admin.layouts.components.leads')
{{-- @endif

@if ($contacts->count() > 0) --}}
    @include('admin.layouts.components.contacts')
{{-- @endif

@if ($users->count() > 0) --}}
    @include('admin.layouts.components.user')
{{-- @endif --}}
    @include('admin.layouts.components.listingleads')
