@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $user->name ?? "" }},</h4><br>
    <p>A deal has been presented for your approval. Kindly examine the details and take necessary action as deemed appropriate. Your prompt attention to this matter is required.</p>
    <p><strong>Property</strong> {{ $deal->property?->ref_no ?? "" }} - {{ $deal->property?->title ?? "" }}</p>
    <p><strong>Property Type</strong> {{ $deal->property?->for ?? "" }}</p>
    <p><strong>Agent</strong> {{ $deal->agent?->first_name ?? "" }} {{ $deal->agent?->last_name ?? "" }}</p>
    <p><strong>Deal Type</strong> {{ $deal?->type ?? "" }}</p>
    <p><strong>Deal Created By</strong> {{ $deal->createdBy?->full_name ?? "" }}</p>
    <p><strong>Deal Submitted for Approval by</strong> {{$deal?->action_by?->name}}</p>
    @include("emails.layouts.button", ["url" => route('admin.deals_revamped.index') . '?deal_id=' . $deal->id, "text" => "View Deal"])
@endsection
