@extends("emails.layouts.layout")
@section("body")
    <h4>Dear {{ $assignee->name ?? "" }},</h4><br>
    <p>Job for {{ $job->type ?? "" }} assigned to you.</p>

    <p><strong>Job Type:</strong> {{ $job->type ?? "" }}</p>
    <p><strong>Deadline Date:</strong> {{ $job->deadline_date ?? "" }}</p>
    <p><strong>Priority:</strong> {{ $job->priority ?? "" }}</p>
    <p><strong>Description:</strong> {{ $job->description ?? "" }}</p>
@endsection
