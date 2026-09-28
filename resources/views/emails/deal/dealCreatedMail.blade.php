@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $user->name ?? "" }},</h4><br>
    <p>New deal has been created for Listing {{$deal?->property?->ref_no}}</p>
    <p><strong>Property</strong> {{ $deal->property?->ref_no ?? "" }} - {{ $deal->property?->title ?? "" }}</p>
    <p><strong>Property Type</strong> {{ $deal->property?->for ?? "" }}</p>
    <p><strong>Agent</strong> {{ $deal->agent?->first_name ?? "" }} {{ $deal->agent?->last_name ?? "" }}</p>
    <p><strong>Deal Type</strong> {{ $deal?->type ?? "" }}</p>
    <p><strong>Deal Created By</strong> {{ $deal->createdBy?->full_name ?? "" }}</p>
    @include("emails.layouts.button", ["url" => route('admin.deals_revamped.index') . '?deal_id=' . $deal->id, "text" => "View Deal"])
@endsection
