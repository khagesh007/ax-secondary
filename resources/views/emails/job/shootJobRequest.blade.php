@extends("emails.layouts.layout")
@section("body")

    <p>New property shoot request has been created for <strong>{{ $shoot_request->property?->ref_no ?? "" }}</strong> </p>
    <br>
    <p><strong>Preferred time and date:</strong> {{ date('d/m/y h:i A',strtotime($shoot_request->preferred_time)) ?? "" }}</p>
    <p><strong>Sale or Rent: </strong>  {{$shoot_request?->property?->for}}</p>
    <p><strong>Reference No. from CRM:</strong> {{$shoot_request?->property?->ref_no}}</p>
    <p><strong>Location/Community:</strong> {{ $shoot_request?->property?->communityProperty?->path_name ?? $shoot_request?->property?->subCommunity?->path_name ?? $shoot_request?->property?->community?->path_name }}</p>
    <p><strong>Property Name:</strong> {{ $shoot_request?->property?->title }}</p>
    <p><strong>No. of Bedrooms:</strong> {{ $shoot_request?->property?->bed?->name }}</p>
    <p><strong>No. of Bathrooms:</strong> {{ $shoot_request?->property?->bath?->name }}</p>
    <p><strong>Price:</strong> {{ $shoot_request?->property?->price?->price }}</p>
    <p><strong>Unit Number:</strong>{{ $shoot_request?->property?->unit_no }}</p>
    <p><strong>Access Instructions:</strong> {{ $shoot_request->access_information }}</p>
    <p><strong>Is the DEWA on/off?:</strong> {{ $shoot_request->dewa == 1 ? 'On' : 'Off' }}</p>
    <p><strong>Furnished or Unfurnished:</strong> {{ $shoot_request->furnished == 1 ? 'Furnished' : 'Unfurnished' }}</p>
    <p><strong>Clean/Uncleaned:</strong> {{ $shoot_request->cleaned == 1 ? 'Cleaned' : 'Uncleaned' }}</p>
    <p><strong>Title:</strong> {{ $shoot_request->title }}</p>
    <p><strong>Description:</strong> {{ $shoot_request->description }}</p>
    @include("emails.layouts.button", ["url" => route('admin.jobs.shoot.index') . '?id=' . $shoot_request->id, "text" => "View Request"])
@endsection
