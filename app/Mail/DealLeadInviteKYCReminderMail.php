<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DealLeadInviteKYCReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $invite, public $lead)
    {
        $this->invite = $invite;
        $this->lead = $lead;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function build()
    {
        $subject = 'Reminder: KYC Required for Deal';
        return $this->replyTo($emailSettings["reply_to"] ?? [])
            ->subject($subject)
            ->view('emails.deal.InviteLeadKycReminder');
    }
}
