<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EventCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $creator;
    public $company;

    /**
     * Create a new message instance.
     */
    public function __construct($event, $creator, $company)
    {
        $this->event = $event;
        $this->creator = $creator;
        $this->company = $company;
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
           subject: 'New Event Created: ' . $this->event->name,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.event_created',
            with: [
                'event' => $this->event,
                'creator' => $this->creator,
                'company' => $this->company,
            ],
            
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
