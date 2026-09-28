<?php

namespace App\Mail\Admin;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PropertyPublishRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $property, public $user)
    {
        $this->property->load("agent");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = ($this->user->name ?? ""). " has requested to publish listing ".($this->property->ref_no ?? "");
        $emailSettings = getEmailSettings("Listing Publish Request");
        return $this->replyTo($emailSettings["reply_to"] ?? [])
            ->cc($emailSettings["cc"] ?? [])
            ->bcc($emailSettings["bcc"] ?? [])
            ->subject($subject)
            ->view('emails.admin.publishRequest');
    }
}
