<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $data)
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Your password has been changed.";
        $emailSettings = getEmailSettings("User Password Updated");
        $roleIds = $emailSettings["roles"] ?? [];
        $userEmails = User::whereHas("roles", fn($q) => $q->whereIn("id", $roleIds))->pluck("email")->all();
        $cc = array_merge(($emailSettings["cc"] ?? []), $userEmails);
        return $this->replyTo($emailSettings["reply_to"] ?? [])
            ->cc($cc)
            ->bcc($emailSettings["bcc"] ?? [])
            ->subject($subject)
            ->view('emails.passwordChanged');
    }
}
