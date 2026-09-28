<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobAssignedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $job, public $assignee) {}

    public function build()
    {
        $subject = "Job assigned to you.";
        $emailSettings = getEmailSettings("Job Assigned");
        $roleIds = $emailSettings["roles"] ?? [];
        $userEmails = User::whereHas("roles", fn($q) => $q->whereIn("id", $roleIds))->pluck("email")->all();
        $cc = array_merge(($emailSettings["cc"] ?? []), $userEmails);
        return $this->replyTo($emailSettings["reply_to"] ?? [])
            ->cc($cc)
            ->bcc($emailSettings["bcc"] ?? [])
            ->subject($subject)
            ->view('emails.jobAssigned');
    }
}
