<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels, InteractsWithQueue;

    /**
     * Create a new message instance.
     */
    public $codeVerification;

    public function __construct($codeVerifications)
    {
        $this->codeVerification = $codeVerifications;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Mot de passe oublié',
        );
    }

    /**
     * Get the message content definition.
     */



    public function content(): Content
    {
        return (new Content())
            ->view('VerificationPassword')
            ->with(['codeVerification' => $this->codeVerification]);
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
