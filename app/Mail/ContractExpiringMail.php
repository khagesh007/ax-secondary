<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContractExpiringMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $property, public $days, public $user, public $process)
    {
        $this->property = $property;
        $this->days = $days;
        $this->user = $user;
        $this->process = $process;

    }


    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        if($this->days == '1'){
            return new Envelope(
                subject: $this->user?->name . ', Your listing ' . $this->property?->ref_no . ' will go offline in 1 day.',
            );
        }else {
            return new Envelope(
                subject: $this->user?->name . ', Your listing ' . $this->property?->ref_no . ' contract is expiring in ' . $this->days . ' days.',
            );
        }
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.admin.contractExpiring',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
