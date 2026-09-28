<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DealCommissionMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public $deal;
    public function __construct(public $data, public $file, public $user)
    {
        $deal = $data["deal"];
        $this->deal = $deal->load("property");
        $this->user = $user;
        $this->file = $file;
    }

    public function build()
    {
        $file_path = storage_path("app/public/" . $this->file);
        $subject = "Deal #".$this->deal->id." Commission Summary";
        $emailSettings = getEmailSettings("Deal Commission Summary");
        $roleIds = $emailSettings["roles"] ?? [];
        $userEmails = User::whereHas("roles", fn($q) => $q->whereIn("id", $roleIds))->pluck("email")->all();
        $cc = array_merge(($emailSettings["cc"] ?? []), $userEmails);
        return $this->replyTo($emailSettings["reply_to"] ?? [])
            ->cc($cc)
            ->bcc($emailSettings["bcc"] ?? [])
            ->subject($subject)
            ->attach($file_path, [
                'as' => "Deal no. ".$this->deal->id." Commission Summary.xlsx",
                'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])
            ->view('emails.deal.dealCommissionMail');
    }
}
