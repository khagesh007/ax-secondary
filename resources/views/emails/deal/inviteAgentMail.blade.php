@extends("emails.layouts.layout")
@section("body")

    Deal Type: Secondary Sale
    Property: axc-3969325 - 1 bed , 2 baths,  New Bridge Hills 3, New Bridge Hills, Motor City, Dubai
    Property Type:  Apartment
    Agent: Kiran Abbasi



    <h4>Dear {{ $user->name ?? "" }},</h4><br>
    <p>Khagesh Ax Capital has Invited you to add buyer’s/Tenant details for a split Deal [# 50].</p>
    <p><strong>Deal Type</strong> {{ $deal?->type ?? "" }}</p>
    <p><strong>Property</strong> {{ $deal->property?->ref_no ?? "" }} - {{ $deal->property?->title ?? "" }}</p>
    <p><strong>Property Type</strong> {{ $deal->property?->for ?? "" }}</p>
    <p><strong>Agent</strong> {{ $deal->agent?->first_name ?? "" }} {{ $deal->agent?->last_name ?? "" }}</p>
    Transaction Details:<br>
    <p><strong>Listing Price</strong> AED {{number_format($deal->property?->price?->price)}}</p>
    <p><strong>Deal Value</strong> AED {{number_format($deal->deal_price)}}
    @include("emails.layouts.button", ["url" => route('admin.deals_revamped.index') . '?deal_id=' . $deal->id, "text" => "View Deal"])
@endsection
