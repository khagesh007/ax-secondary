@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $lead->agent->name ?? "" }},</h4><br>
    <p>You have follow up for lead today.</p>

    <p><strong>Name:</strong> {{ $lead->first_name ?? "" }} {{ $lead->last_name ?? "" }}</p>
    <p><strong>Mobile:</strong> {{ $lead->mobile ?? "" }}</p>
    <p><strong>Email:</strong> {{ $lead->email ?? "" }}</p>
    <p><strong>Priority:</strong> {{ $lead->priority ?? "" }}</p>
    <p><strong>Lead for:</strong> {{ collect($lead->for ?? [])->implode(", ") }}</p>
@endsection
