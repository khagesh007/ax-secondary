@extends("emails.layouts.layout")
@section("body")

   Hi {{ $deal->first_name ?? "" }} {{ $deal->last_name ?? "" }},<br>

   Please fill the details required for the your property deal. You can click on the link below to fill the details.<br>
   @php

    $link = route('collab.deals.lead.kyc.form',['invite_code'=>$invite->invite_code]);

   @endphp

    @include("emails.layouts.button", ["url" => $link, "text" => "Fill the form here"])

    <p>If you are unable to see the button please click on the link below
        <a target="_blank" href="{{ $link }}">{{ $link }}</a>
    </p>
@endsection
