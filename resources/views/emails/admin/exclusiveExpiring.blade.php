@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $user->name ?? "" }},</h4> <br>
    <p> Exclusive status of {{ $property->ref_no ?? "" }} is about to expire {{$days == '1' ?'Today' : 'in '.$days.' days' ?? ''}}.</p>
    <p><strong>Listing Ref #:</strong> {{ $property->ref_no ?? "" }}</p>
    <p><strong>Expiring On:</strong> {{ date('dS F, Y', strtotime($property->exclusive_end)) ?? "" }}</p>
    <p><strong>Started On:</strong> {{ date('dS F, Y', strtotime($property->exclusive_start)) ?? "" }}</p>
    <p><strong>Agent:</strong> {{ $process['agent']['name'] ?? "" }}</p>
    @include("emails.layouts.button", ["url" => $process['url'], "text" => "View Listing"])
@endsection
