@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $user->name ?? "" }},</h4> <br>
    <p>Contract of {{ $property->ref_no ?? "" }} is expiring  {{$days == '1' ?'Today' : 'in '.$days.' days' ?? ''}}.</p>
    <p>The contract of your listing with reference # {{ $property->ref_no ?? "" }} is about to expire {{$days == '1' ?'Today' : 'in '.$days.' days' ?? ''}}. Please take necessary action to prevent your listing from going offline.</p>
    <p><strong>Listing Ref #:</strong> {{ $property->ref_no ?? "" }}</p>
    <p><strong>Expiring On:</strong> {{ date('dS F, Y', strtotime($property->contract_end)) ?? "" }}</p>
    <p><strong>Started On:</strong> {{ date('dS F, Y', strtotime($property->contract_start)) ?? "" }}</p>
    <p><strong>Agent:</strong> {{ $process['agent']['name'] ?? "" }}</p>
    @include("emails.layouts.button", ["url" => $process['url'], "text" => "View Listing"])
@endsection
