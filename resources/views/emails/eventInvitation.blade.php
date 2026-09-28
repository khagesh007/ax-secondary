@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $user->name ?? "" }},</h4><br>
    <p>I would like to invite you to attend <strong>{{ $event->title ?? "" }}</strong> which will be held on <strong>{{ $event->start_time }}</strong> at <strong>{{ $event->location }}</strong>.</p>
    <p><strong>Event Type:</strong> {{ $event?->type?->name ?? "" }}</p>
    <p><strong>Dates:</strong> {{ date('d/m/y h:i A',strtotime($event->start_time)) ?? "" }} - {{ date('d/m/y h:i A',strtotime($event->end_time)) ?? "" }}</p>
    <p><strong>Location:</strong> {{ $event->location ?? "" }}</p>
    @if($event?->contact !=null) <p><strong>Contact:</strong> {{ $event?->contact?->first_name . ' ' . $event?->contact?->last_name}} </p>@endif
    @if($event?->lead !=null)<p><strong>Lead:</strong> {{ $event?->lead?->first_name . ' ' . $event?->lead?->last_name}} </p>@endif
    @if($event?->property !=null)<p><strong>Property:</strong> ({{$event?->property?->ref_no}}) {{$event?->property?->title}}</p> @endif
    <p><strong>Title:</strong> {{ $event->title ?? "" }}</p>
    <p><strong>Description:</strong> {{ $event->description ?? "" }}</p>
    @include("emails.layouts.button", ["url" => route('admin.events.index') . '?event=' . $event->id, "text" => "View Event"])
@endsection
