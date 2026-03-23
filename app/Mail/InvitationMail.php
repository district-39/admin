<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $signedUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(public Invitation $invitation)
    {
        $this->signedUrl = URL::temporarySignedRoute(
            'register',
            now()->addDays(7),
            ['invitation' => $invitation->id],
        );
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You\'ve Been Invited to D39 Admin',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.invitation',
        );
    }
}
