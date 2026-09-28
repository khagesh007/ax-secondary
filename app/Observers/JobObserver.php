<?php

namespace App\Observers;

use App\Mail\JobAssignedMail;
use App\Mail\JobRequestedMail;
use App\Mail\JobStatusChangedMail;
use App\Models\Job;
use Illuminate\Support\Facades\Mail;

class JobObserver
{
    /**
     * Handle the Job "created" event.
     *
     * @param  \App\Models\Job  $job
     * @return void
     */
    public function created(Job $job)
    {
        if (!is_null($job->assigned_to_id)){
            $assignee = $job->assignedTo;
            Mail::to($assignee)->send(new JobAssignedMail($job, $assignee));
        }

        if (!is_null($job->requested_by_id)){
            $requestedBy = $job->requestedBy;
            Mail::to($requestedBy)->send(new JobRequestedMail($job, $requestedBy));
        }
    }

    /**
     * Handle the Job "updated" event.
     *
     * @param  \App\Models\Job  $job
     * @return void
     */
    public function updated(Job $job)
    {
        if ($job->isDirty("job_status")){
            if (!is_null($job->requested_by_id)){
                $requestedBy = $job->requestedBy;
                Mail::to($requestedBy)->send(new JobStatusChangedMail($job, $requestedBy));
            }
        }

        if ($job->isDirty("requested_by_id")){
            if (!is_null($job->requested_by_id)){
                $requestedBy = $job->requestedBy;
                Mail::to($requestedBy)->send(new JobRequestedMail($job, $requestedBy));
            }
        }

        if ($job->isDirty("assigned_to_id")){
            if (!is_null($job->assigned_to_id)){
                $assignee = $job->assignedTo;
                Mail::to($assignee)->send(new JobAssignedMail($job, $assignee));
            }
        }
    }
}
