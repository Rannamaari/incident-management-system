<?php

namespace App\Mail;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncidentUpdatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $incident;
    public $updateMessage;
    public $userName;

    /**
     * Create a new message instance.
     */
    public function __construct(Incident $incident, string $updateMessage, string $userName)
    {
        $this->incident = $incident;
        $this->updateMessage = $updateMessage;
        $this->userName = $userName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[' . $this->incident->incident_code . '] ' . $this->incident->summary,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.incidents.updated',
            text: 'emails.incidents.updated-text',
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
