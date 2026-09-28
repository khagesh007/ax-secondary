@extends("emails.layouts.layout")
@section("body")
    <h4>{{ $user->name ?? "" }} has requested to publish listing {{ $property->ref_no ?? "" }}</h4><br>
    <p><strong>Listing Ref #:</strong> {{ $property->ref_no ?? "" }}</p>
    <p><strong>Listing Type:</strong> {{ \App\Models\Property::FOR_SELECT[$property->for] ?? "" }}</p>
    <p><strong>Agent:</strong> {{ $property->agent->name ?? "" }}</p>
@endsection
