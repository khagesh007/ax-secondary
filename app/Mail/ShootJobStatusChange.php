<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ShootJobStatusChange extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $shoot_request, public $user, public $statusMsg, public $header)
    {
        $this->header = $header;
        $this->shoot_request = $shoot_request;
        $this->user = $user;
        $this->statusMsg = $statusMsg;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function build()
    {
        $subject = $this->header." for ".($this->shoot_request?->property?->ref_no ?? "");
        return $this->replyTo($emailSettings["reply_to"] ?? [])
            ->subject($subject)
            ->view('emails.job.jobStatusChange');
    }
}
