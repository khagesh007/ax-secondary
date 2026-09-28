<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $event, public $user) {}

    public function build()
    {
        $subject = "You are invited to attend ". ($this->event->title ?? "") . ".";
        $emailSettings = getEmailSettings("Event Created\Updated");
        $roleIds = $emailSettings["roles"] ?? [];
        $userEmails = User::whereHas("roles", fn($q) => $q->whereIn("id", $roleIds))->pluck("email")->all();
        $cc = array_merge(($emailSettings["cc"] ?? []), $userEmails);
        return $this->replyTo($emailSettings["reply_to"] ?? [])
            ->cc($cc)
            ->bcc($emailSettings["bcc"] ?? [])
            ->subject($subject)
            ->view('emails.eventInvitation');
    }
}
