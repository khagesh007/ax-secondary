@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $user->name ?? "" }},</h4><br>
    <p>Reminder to attend <strong>{{ $event->title ?? "" }}</strong> which will be held on <strong>{{ $event->start_time }}</strong> at <strong>{{ $event->location }}</strong>.</p>

    <p><strong>Title:</strong> {{ $event->title ?? "" }}</p>
    <p><strong>Type:</strong> {{ $event->type?->name ?? "" }}</p>
    <p><strong>Start Date and Time:</strong> {{ $event->start_time ?? "" }}</p>
    <p><strong>End Date and Time:</strong> {{ $event->end_time ?? "" }}</p>
    <p><strong>Location:</strong> {{ $event->location ?? "" }}</p>
    <p><strong>Description:</strong> {{ $event->description ?? "" }}</p>
    @include("emails.layouts.button", ["url" => route('admin.events.index') . '?event=' . $event->id, "text" => "View Event"])
@endsection
